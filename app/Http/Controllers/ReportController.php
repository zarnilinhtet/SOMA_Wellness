<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\Category;
use App\Models\ClassSchedule;
use App\Models\Instructor;
use App\Models\InstructorCategoryFee;
use App\Models\Onboarding;
use App\Models\Package;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // ==========================================
    // Customer Reports
    // ==========================================
    public function getCustomerReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');

        $customers = User::customersOnly()
            ->with(['purchases' => function ($query) use ($startDate, $endDate, $categoryId) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
            if ($categoryId) {
                    $query->whereHas('package', function ($q) use ($categoryId) {
                        $q->where('type', $categoryId);
                    });
                }
            }])
            ->latest()
            ->get();

        $customers->each(function ($customer) {
            $customer->filtered_total_packages = $customer->purchases->count();
            $customer->filtered_total_amount = $customer->purchases->sum('amount');
        });

        if ($startDate || $endDate || $categoryId) {
            $customers = $customers->filter(function ($customer) {
                return $customer->filtered_total_packages > 0;
            })->values();
        }

        $grandTotals = [
            'packages' => $customers->sum('filtered_total_packages'),
            'amount' => $customers->sum('filtered_total_amount'),
        ];

        $categories = Category::all();

        return view('backends.reports.customerReport', compact('customers', 'grandTotals', 'categories'));
    }

    public function getCustomerPackagesAjax(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');

        $customer = User::with([
            'purchases' => function ($query) use ($startDate, $endDate, $categoryId) {
                $query->latest();

                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                if ($categoryId) {
                    $query->whereHas('package', function ($q) use ($categoryId) {
                        $q->where('type', $categoryId);
                    });
                }
            },
            'purchases.package'
        ])->findOrFail($id);

        return response()->json([
            'packages' => $customer
        ]);
    }

    // ==========================================
    // Sales & Monthly Reports
    // ==========================================
    public function getTopPackages(Request $request)
    {
        return $this->getMonthlySales($request);
    }

    public function getMonthlySales(Request $request)
    {
        $currentYear = Carbon::now()->year;

        $topPackages = Package::query()->withCount('purchases')->orderByDesc('purchases_count')->paginate(10);

        $paymentSummary = Purchase::select('payment_method', DB::raw('SUM(amount) as total_amount'))
            ->where('pay_status', 'confirmed')
            ->groupBy('payment_method')
            ->get();

        $subQuery = Onboarding::select(
            DB::raw("
            CASE 
                WHEN know_where IN ('Facebook', 'Instagram', 'Tiktok', 'Friend') THEN know_where
                ELSE 'Others'
            END as channel
        ")
        )->whereNotNull('know_where');

        $reportKnow = DB::table($subQuery, 'channels')
            ->select('channel', DB::raw('COUNT(*) as total_users'))
            ->groupBy('channel')
            ->get();

        $monthlyData = Purchase::whereYear('created_at', $currentYear)
            ->where('pay_status', 'confirmed')
            ->select(
                DB::raw("MONTH(created_at) as month"),
                DB::raw("COUNT(id) as package_count"),
                DB::raw("SUM(amount) as total_revenue")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy('month');

        $months = [];
        $packageCounts = [];
        $totalRevenues = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
            $packageCounts[] = $monthlyData->has($i) ? $monthlyData[$i]->package_count : 0;
            $totalRevenues[] = $monthlyData->has($i) ? $monthlyData[$i]->total_revenue : 0;
        }

        $categoryData = Purchase::whereYear('purchases.created_at', $currentYear)
            ->where('pay_status', 'confirmed')
            ->join('packages', 'purchases.selected_packages_id', '=', 'packages.id')
            ->join('categories', 'packages.type', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw("COUNT(purchases.id) as category_package_count")
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('category_package_count', 'desc')
            ->get();

        $categories = $categoryData->pluck('category_name')->toArray();
        $categoryCounts = $categoryData->pluck('category_package_count')->toArray();

        return view('backends.reports.monthly', compact(
            'topPackages',
            'paymentSummary',
            'reportKnow',
            'months',
            'packageCounts',
            'totalRevenues',
            'currentYear',
            'categories',
            'categoryCounts'
        ));
    }

    // ==========================================
    // Instructor Reports (Admin View)
    // ==========================================
    public function getInstructorReport()
    {
        $instructors = Instructor::with(['user', 'categoryFees'])->get();
        $classes = ClassSchedule::with('category')->get();
        $instructorFees = InstructorCategoryFee::all()->groupBy('instructor_id');

        $instructors = $instructors->map(function ($instructor) use ($classes, $instructorFees) {
            $instructorId = $instructor->id;

            $assignedClasses = $classes->filter(function ($schedule) use ($instructorId) {
                $ids = is_string($schedule->instructor_ids)
                    ? json_decode($schedule->instructor_ids, true)
                    : (array) $schedule->instructor_ids;

                return is_array($ids) && in_array($instructorId, $ids);
            });

            $attendances = Attendance::with(['client', 'class'])
                ->where('instructor_id', $instructorId)
                ->get();

            $groupedByClass = $attendances->groupBy('class_id');
            $feesConfig = $instructorFees->get($instructorId, collect())->keyBy('category_id');

            $classEarnings = 0;
            $instructorType = $instructor->instructor_type ?? 'full_time';

            // Full Time Base Salary Definition
            $baseSalary = 0;
            if ($instructorType === 'full_time') {
                $feeRecord = $instructor->categoryFees->whereIn('fee_type', ['full_time_fixed', 'full_time_percentage'])->first();
                $baseSalary = $feeRecord ? (float) $feeRecord->fee_value : 0;
            }

            foreach ($groupedByClass as $classId => $records) {
                $firstRecord = $records->first();
                $classSchedule = $firstRecord->class;

                if (!$classSchedule) continue;

                $totalClients = Booking::where('selected_class_id', $classId)->where('status', 'confirmed')->count();

                $classTotalFee = $this->calculateClassFee($classSchedule, $totalClients, $feesConfig);
                $classEarnings += $classTotalFee;
            }

            // Grand Total (Base Salary + Calculated Class Earnings)
            $grandTotal = $baseSalary + $classEarnings;

            return [
                'id' => $instructor->id,
                'name' => $instructor->user->name ?? 'Unknown',
                'type' => $instructorType,
                'base_salary' => $baseSalary,
                'class_earnings' => $classEarnings,
                'grand_total' => $grandTotal,
                'total_classes' => $assignedClasses->count(),
            ];
        });

        return view('backends.reports.instructorReport', compact('instructors'));
    }

    public function getInstructorPackagesAjax($instructorId)
    {
        $attendances = Attendance::with(['client', 'class'])
            ->where('instructor_id', $instructorId)
            ->get();

        $feesConfig = InstructorCategoryFee::where('instructor_id', $instructorId)
            ->get()
            ->keyBy('category_id');

        $groupedByClass = $attendances->groupBy('class_id');
        $data = [];

        foreach ($groupedByClass as $classId => $records) {
            $firstRecord = $records->first();
            $classSchedule = $firstRecord->class;

            if (!$classSchedule) continue;

            $totalClients = Booking::where('selected_class_id', $classId)->where('status', 'confirmed')->count();

            // Calculate class cut
            $totalFee = $this->calculateClassFee($classSchedule, $totalClients, $feesConfig);

            $data[] = [
                'class_id' => $classId,
                'class_name' => $classSchedule->class_name,
                'start_date' => Carbon::parse($classSchedule->start_date)->format('d M Y'),
                'end_date' => Carbon::parse($classSchedule->end_date)->format('d M Y'),
                'time' => Carbon::parse($classSchedule->start_time)->format('h:i A') . ' - ' . Carbon::parse($classSchedule->end_time)->format('h:i A'),
                'total_fee' => $totalFee,
                'total_clients' => $totalClients,
            ];
        }

        return response()->json($data);
    }

    // ==========================================
    // Instructor Reports (Instructor Own View Dashboard)
    // ==========================================
    public function getInstructorReports()
    {
        $instructor = Instructor::where('instructor_id', auth()->user()->id)
            ->with('user')
            ->firstOrFail();

        $instructorId = $instructor->id;

        $classes = ClassSchedule::with('category')
            ->get()
            ->filter(function ($schedule) use ($instructorId) {
                $ids = is_string($schedule->instructor_ids)
                    ? json_decode($schedule->instructor_ids, true)
                    : (array) $schedule->instructor_ids;

                return is_array($ids) && in_array($instructorId, $ids);
            });

        $attendances = Attendance::with('client')
            ->where('instructor_id', $instructorId)
            ->get()
            ->groupBy('class_id');

        $classIds = $classes->pluck('id')->toArray();
        $bookingCounts = Booking::whereIn('selected_class_id', $classIds)
            ->where('status', 'confirmed')
            ->selectRaw('selected_class_id, count(*) as count')
            ->groupBy('selected_class_id')
            ->pluck('count', 'selected_class_id');

        $instructorFees = InstructorCategoryFee::where('instructor_id', $instructorId)
            ->get()
            ->keyBy('category_id');

        $reports = $classes->map(function ($cls) use ($attendances, $bookingCounts, $instructorFees) {
            $classAttendances = $attendances->get($cls->id, collect());
            $totalClients = $bookingCounts->get($cls->id, 0);

            $totalFeePerClass = $this->calculateClassFee($cls, $totalClients, $instructorFees);

            $userBreakdown = $classAttendances->whereNotNull('client_id')->map(function ($att) {
                return [
                    'user_name' => $att->client->name ?? 'Unknown Student',
                    'fee_amount' => $att->fee_amount,
                    'date' => Carbon::parse($att->attendance_date)->format('d M Y')
                ];
            })->values();

            return [
                'class_id' => $cls->id,
                'class_name' => $cls->class_name,
                'start_date' => Carbon::parse($cls->start_date)->format('d M Y'),
                'end_date' => Carbon::parse($cls->end_date)->format('d M Y'),
                'time' => Carbon::parse($cls->start_time)->format('h:i A') . ' - ' . Carbon::parse($cls->end_time)->format('h:i A'),
                'total_clients' => $totalClients,
                'bonus_fee' => 0,
                'total_fee' => $totalFeePerClass,
                'user_breakdown' => $userBreakdown,
            ];
        })->values();

        $totalClasses = $reports->count();
        $grandTotalFees = $reports->sum('total_fee');
        $grandTotalClients = $reports->sum('total_clients');

        return view('backends.instrcutor_report', compact(
            'instructor',
            'reports',
            'totalClasses',
            'grandTotalFees',
            'grandTotalClients'
        ));
    }

    // ==========================================
    // Centralized Calculation Helper
    // ==========================================
    private function calculateClassFee($classSchedule, $totalClients, $feesConfig)
    {
        $feeConfig = $feesConfig->get($classSchedule->category_id);
        if (!$feeConfig) return 0;

        $calculatedFee = 0;

        // Compute total class revenue from confirmed bookings for percentage checks
        $classRevenue = 0;
        $bookings = Booking::with('package')->where('selected_class_id', $classSchedule->id)->where('status', 'confirmed')->get();
        foreach ($bookings as $booking) {
            $classRevenue += $booking->package->price ?? 0;
        }

        $bonuses = [];
        if (is_array($feeConfig->bonuses)) {
            $bonuses = collect($feeConfig->bonuses)->values();
        }

        // Rule 1: Full Time - Fixed Commission per class
        if ($feeConfig->fee_type === 'full_time_fixed') {
            // fee_value = base salary (handled globally in report mapping)
            // bonuses[0]['amount'] = flat commission per class (e.g. 30,000)
            $calculatedFee = $bonuses->first()['amount'] ?? 0;
        }

        // Rule 2: Full Time - Percentage Commission per class
        elseif ($feeConfig->fee_type === 'full_time_percentage') {
            // fee_value = base salary (handled globally in report mapping)
            // bonuses[0]['amount'] = percentage (e.g. 10%)
            $percentage = $bonuses->first()['amount'] ?? 0;
            $calculatedFee = $classRevenue * ((float)$percentage / 100);
        }

        // Rule 3: Part Time - Fixed Base Rate per class + Bonus above limit
        elseif ($feeConfig->fee_type === 'part_time_fixed') {
            // fee_value = base rate for 1-5 people (e.g. 15,000)
            $baseRate = (float) $feeConfig->fee_value;
            $calculatedFee = $baseRate;

            if ($bonuses->isNotEmpty()) {
                $threshold = (int) $bonuses->first()['threshold']; // e.g. 5
                $extraAmount = (float) $bonuses->first()['amount']; // e.g. 2,000

                if ($totalClients > $threshold) {
                    $extraStudents = $totalClients - $threshold;
                    $calculatedFee += ($extraStudents * $extraAmount);
                }
            }
        }

        // Rule 4: Part Time - Percentage per class & Maintenance Deduction
        elseif ($feeConfig->fee_type === 'part_time_percentage') {
            // fee_value = percentage (e.g. 40%)
            // bonuses[0]['amount'] = maintenance fee (e.g. 5,000)
            $percentage = (float) $feeConfig->fee_value;
            $maintenanceFee = (float) ($bonuses->first()['amount'] ?? 0);

            $gross = $classRevenue * ($percentage / 100);
            $calculatedFee = max(0, $gross - $maintenanceFee); // Ensures it doesn't go below 0
        }

        return $calculatedFee;
    }
}
