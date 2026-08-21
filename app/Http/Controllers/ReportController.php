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
    public function getCustomerReport(Request $request)
    {
        // View မှ လာသော Filter တန်ဖိုးများကို ယူပါမည်
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');

        // Customer များနှင့် ၎င်းတို့၏ Purchase များကို Filter စစ်ပြီး ခေါ်ပါမည်
        $customers = User::customersOnly()
            ->with(['purchases' => function ($query) use ($startDate, $endDate, $categoryId) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                if ($categoryId) {
                    // Package ၏ Type သည် Category ID ဖြစ်သောကြောင့်
                    $query->whereHas('package', function ($q) use ($categoryId) {
                        $q->where('type', $categoryId);
                    });
                }
            }])
            ->latest()
            ->get();

        // Customer တစ်ယောက်ချင်းစီအတွက် Total Package နှင့် Total Amount များကို တွက်ချက်ပါမည်
        $customers->each(function ($customer) {
            $customer->filtered_total_packages = $customer->purchases->count();
            $customer->filtered_total_amount = $customer->purchases->sum('amount');
        });

        // Filter သုံးထားပါက Package ဝယ်ယူထားခြင်းမရှိသူ (Total = 0) များကို Report မှ ဖျောက်ထားပါမည်
        if ($startDate || $endDate || $categoryId) {
            $customers = $customers->filter(function ($customer) {
                return $customer->filtered_total_packages > 0;
            })->values();
        }

        // Grand Total တွက်ချက်ခြင်း
        $grandTotals = [
            'packages' => $customers->sum('filtered_total_packages'),
            'amount' => $customers->sum('filtered_total_amount'),
        ];

        $categories = Category::all();

        return view('backends.reports.customerReport', compact('customers', 'grandTotals', 'categories'));
    }

    public function getCustomerPackagesAjax(Request $request, $id)
    {
        // View (Ajax) မှ လာသော Filter တန်ဖိုးများကို ယူပါမည်
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

    public function getInstructorReport()
    {
        $instructors = Instructor::with('user')->get();
        $classes = ClassSchedule::with('category')->get();
        // Instructor Fee config များကို ကြိုတင်ခေါ်ထားပါမည်
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

            $totalFee = 0;

            foreach ($groupedByClass as $classId => $records) {
                $firstRecord = $records->first();
                $classSchedule = $firstRecord->class;

                if (!$classSchedule) continue;

                // Booking အတည်ပြုပြီးသော ကျောင်းသားအရေအတွက် (Total Clients)
                $totalClients = Booking::where('selected_class_id', $classId)->where('status', 'confirmed')->count();

                // Dynamic Bonus Fee တွက်ချက်ခြင်း
                $bonusFee = 0;
                $feeConfig = $feesConfig->get($classSchedule->category_id);

                if ($feeConfig && is_array($feeConfig->bonuses)) {
                    // Threshold အကြီးဆုံးမှ အငယ်သို့စီပြီး တိုက်စစ်မည်
                    $bonuses = collect($feeConfig->bonuses)->sortByDesc('threshold')->values();
                    foreach ($bonuses as $b) {
                        if ($totalClients >= (int)$b['threshold']) {
                            $bonusFee = (float)$b['amount'];
                            break;
                        }
                    }
                }

                $studentFees = $records->whereNotNull('client_id')->sum('fee_amount');
                $dbBonusFee = $records->filter(function ($att) {
                    return empty($att->client_id);
                })->sum('fee_amount');

                // Attendance တွင် null ဖြင့် သိမ်းထားတာရှိရင် အဲ့တာကိုသုံးမယ်၊ မရှိရင် Dynamic တွက်ထားတာကိုသုံးမယ်
                $finalBonusFee = $dbBonusFee > 0 ? $dbBonusFee : $bonusFee;

                $totalFee += ($studentFees + $finalBonusFee);
            }

            return [
                'id' => $instructor->id,
                'name' => $instructor->user->name ?? 'Unknown',
                'total_classes' => $assignedClasses->count(),
                'total_fee' => $totalFee
            ];
        });

        return view('backends.reports.instructorReport', compact('instructors'));
    }

    public function getInstructorPackagesAjax($instructorId)
    {
        $attendances = Attendance::with(['client', 'class'])
            ->where('instructor_id', $instructorId)
            ->get();

        $groupedByClass = $attendances->groupBy('class_id');
        $data = [];

        foreach ($groupedByClass as $classId => $records) {
            $firstRecord = $records->first();
            $classSchedule = $firstRecord->class;

            if (!$classSchedule) continue;

            $total_clients = Booking::where('selected_class_id', $classId)->where('status', 'confirmed')->count();

            // Dynamic Bonus Fee တွက်ချက်ခြင်း
            $instructorFee = InstructorCategoryFee::where('instructor_id', $instructorId)
                ->where('category_id', $classSchedule->category_id)
                ->first();

            $bonusFee = 0;
            if ($instructorFee && is_array($instructorFee->bonuses)) {
                $bonuses = collect($instructorFee->bonuses)->sortByDesc('threshold')->values();
                foreach ($bonuses as $b) {
                    if ($total_clients >= (int)$b['threshold']) {
                        $bonusFee = (float)$b['amount'];
                        break;
                    }
                }
            }

            // Student Fee Total
            $studentFees = $records->whereNotNull('client_id')->sum('fee_amount');

            // Check Database saved bonus fee
            $dbBonusFee = $records->filter(function ($att) {
                return empty($att->client_id);
            })->sum('fee_amount');

            $finalBonusFee = $dbBonusFee > 0 ? $dbBonusFee : $bonusFee;
            $totalFee = $studentFees + $finalBonusFee;

            $userBreakdown = $records->whereNotNull('client_id')->map(function ($att) {
                return [
                    'user_name' => $att->client->name ?? 'Unknown Student',
                    'fee_amount' => $att->fee_amount,
                    'date' => Carbon::parse($att->attendance_date)->format('d M Y')
                ];
            })->values();

            $data[] = [
                'class_id' => $classId,
                'class_name' => $classSchedule->class_name,
                'start_date' => Carbon::parse($classSchedule->start_date)->format('d M Y'),
                'end_date' => Carbon::parse($classSchedule->end_date)->format('d M Y'),
                'time' => Carbon::parse($classSchedule->start_time)->format('h:i A') . ' - ' . Carbon::parse($classSchedule->end_time)->format('h:i A'),
                'total_fee' => $totalFee,
                'bonus_fee' => $finalBonusFee,
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
            ->get()
            ->groupBy('class_id');

        $classIds = $classes->pluck('id')->toArray();
        $bookingCounts = Booking::whereIn('selected_class_id', $classIds)
            ->where('status', 'confirmed')
            ->selectRaw('selected_class_id, count(*) as count')
            ->groupBy('selected_class_id')
            ->pluck('count', 'selected_class_id');

        // Fetch instructor category fees
        $instructorFees = InstructorCategoryFee::where('instructor_id', $instructorId)
            ->get()
            ->keyBy('category_id');

        $reports = $classes->map(function ($cls) use ($attendances, $bookingCounts, $instructorFees) {
            $classAttendances = $attendances->get($cls->id, collect());
            $totalClients = $bookingCounts->get($cls->id, 0);

            // Dynamic Bonus Fee တွက်ချက်ခြင်း
            $feeConfig = $instructorFees->get($cls->category_id);
            $bonusFee = 0;
            if ($feeConfig && is_array($feeConfig->bonuses)) {
                $bonuses = collect($feeConfig->bonuses)->sortByDesc('threshold')->values();
                foreach ($bonuses as $b) {
                    if ($totalClients >= (int)$b['threshold']) {
                        $bonusFee = (float)$b['amount'];
                        break;
                    }
                }
            }

            $studentFees = $classAttendances->whereNotNull('client_id')->sum('fee_amount');
            $dbBonusFee = $classAttendances->filter(function ($att) {
                return empty($att->client_id);
            })->sum('fee_amount');

            $finalBonusFee = $dbBonusFee > 0 ? $dbBonusFee : $bonusFee;
            $totalFeePerClass = $studentFees + $finalBonusFee;

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
                'bonus_fee' => $finalBonusFee,
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
