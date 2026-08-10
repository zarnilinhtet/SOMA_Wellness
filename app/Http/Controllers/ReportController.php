<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\Category;
use App\Models\ClassSchedule;
use App\Models\Instructor;
use App\Models\Onboarding;
use App\Models\Package;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getCustomerReport()
    {
        $customers = User::customersOnly()->with('purchases')->latest()->get();
        $grandTotals = [
            'packages' => $customers->sum('purchases_summary.total_packages'),
            'amount' => $customers->sum('purchases_summary.total_amount'),
        ];
        $categories = Category::all();

        return view('backends.reports.customerReport', compact('customers', 'grandTotals', 'categories'));
    }

    public function getCustomerPackagesAjax($id)
    {
        $customer = User::with([
            'purchases' => function ($query) {
                $query->latest();
            },
            'purchases.package'
        ])->findOrFail($id);

        logger($customer->toArray());

        return response()->json([
            'packages' => $customer
        ]);
    }

    // If you visit the old Top Packages route, it will now load the combined dashboard
    public function getTopPackages(Request $request)
    {
        return $this->getMonthlySales($request);
    }

    // -----------------------------------------------------------------------------
    // COMBINED DASHBOARD DATA
    // -----------------------------------------------------------------------------
    public function getMonthlySales(Request $request)
    {
        $currentYear = Carbon::now()->year;

        // 1. TOP PACKAGES & PAYMENT SUMMARY & CHANNELS
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

        // 2. MONTHLY DATA (For Chart 1 & Chart 2)
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

        // 3. CATEGORY DATA (For Chart 3)
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

        // Returning the 'monthly' view that we are updating below
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
    // -----------------------------------------------------------------------------

    public function getInstructorReport()
    {
        $instructors = Instructor::with('user')->get();
        $classes = ClassSchedule::with('category')->get();

        $instructors = $instructors->map(function ($instructor) use ($classes) {
            $instructorId = $instructor->id;

            $assignedClasses = $classes->filter(function ($schedule) use ($instructorId) {
                return isset($schedule->instructor_ids) && in_array($instructorId, (array) $schedule->instructor_ids);
            });

            $attendances = Attendance::with(['client', 'class'])
                ->where('instructor_id', $instructorId)
                ->whereNotNull('client_id')
                ->get();

            $groupedByInstructorId = $attendances->groupBy('instructor_id');

            foreach ($groupedByInstructorId as $instructorId => $records) {
                $totalFee = $records->sum('fee_amount');

                return [
                    'id' => $instructor->id,
                    'name' => $instructor->user->name ?? 'Unknown',
                    'total_classes' => $assignedClasses->count(),
                    'total_fee' => number_format($totalFee, 2)
                ];
            }
        });

        return view('backends.reports.instructorReport', compact('instructors'));
    }

    public function getInstructorPackagesAjax($instructorId)
    {
        $attendances = Attendance::with(['client', 'class'])
            ->where('instructor_id', $instructorId)
            ->whereNotNull('client_id')
            ->get();

        $groupedByClass = $attendances->groupBy('class_id');
        $data = [];

        foreach ($groupedByClass as $classId => $records) {
            $firstRecord = $records->first();
            $classSchedule = $firstRecord->class;

            if (!$classSchedule)
                continue;

            $total_clients = Booking::where('selected_class_id', $classId)->where('status', 'confirmed')->count();
            $totalFee = $records->sum('fee_amount');

            $userBreakdown = $records->map(function ($att) {
                return [
                    'user_name' => $att->client->name ?? 'Unknown Student',
                    'fee_amount' => number_format($att->fee_amount, 2),
                    'date' => Carbon::parse($att->attendance_date)->format('d M Y')
                ];
            })->values();

            $data[] = [
                'class_id' => $classId,
                'class_name' => $classSchedule->class_name,
                'start_date' => Carbon::parse($classSchedule->start_date)->format('d M Y'),
                'end_date' => Carbon::parse($classSchedule->end_date)->format('d M Y'),
                'time' => Carbon::parse($classSchedule->start_time)->format('h:i A') . ' - ' . Carbon::parse($classSchedule->end_time)->format('h:i A'),
                'total_fee' => number_format($totalFee, 2),
                'total_clients' => $total_clients,
                'breakdown' => $userBreakdown
            ];
        }

        return response()->json($data);
    }

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
            ->whereNotNull('client_id')
            ->get()
            ->groupBy('class_id');

        $classIds = $classes->pluck('id')->toArray();
        $bookingCounts = Booking::whereIn('selected_class_id', $classIds)
            ->where('status', 'confirmed')
            ->selectRaw('selected_class_id, count(*) as count')
            ->groupBy('selected_class_id')
            ->pluck('count', 'selected_class_id');

        $reports = $classes->map(function ($cls) use ($attendances, $bookingCounts) {
            $classAttendances = $attendances->get($cls->id, collect());
            $totalFeePerClass = $classAttendances->sum('fee_amount');
            $totalClients = $bookingCounts->get($cls->id, 0);

            $userBreakdown = $classAttendances->map(function ($att) {
                return [
                    'user_name' => $att->client->name ?? 'Unknown Student',
                    'fee_amount' => number_format($att->fee_amount, 2),
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
}
