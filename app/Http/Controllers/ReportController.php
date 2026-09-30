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
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // ==========================================
    // Instructor Reports (Admin View - Main List)
    // ==========================================
    public function getInstructorReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $instructors = Instructor::with(['user', 'categoryFees'])->get();
        $classes = ClassSchedule::with('category')->get();
        $instructorFees = InstructorCategoryFee::all()->groupBy('instructor_id');

        $instructors = $instructors->map(function ($instructor) use ($classes, $instructorFees, $startDate, $endDate) {
            $instructorId = $instructor->id;

            $attendances = Attendance::with(['client', 'class'])
                ->where('instructor_id', $instructorId)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            $groupedByClass = $attendances->groupBy('class_id');
            $feesConfig = $instructorFees->get($instructorId, collect());

            $classEarnings = 0;
            $totalInstructorClients = 0;
            $instructorType = $instructor->instructor_type ?? 'full_time';

            $maintenanceFee = (float)($instructor->maintenance_fees ?? 0);
            $classTeachingFee = (float)($instructor->class_teaching_fees ?? 0);

            $baseSalary = 0;
            if ($instructorType === 'full_time') {
                $feeRecord = $instructor->categoryFees->whereIn('fee_type', ['full_time_fixed', 'full_time_percentage', 'fixed'])->first();
                $baseSalary = $feeRecord ? (float) $feeRecord->fee_value : 0;
            }

            foreach ($groupedByClass as $classId => $records) {
                $firstRecord = $records->first();
                $classSchedule = $firstRecord->class;

                if (!$classSchedule) continue;

                $totalClients = Attendance::where('class_id', $classId)
                    ->where('instructor_id', $instructorId)
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->whereNotNull('client_id')
                    ->distinct('client_id')
                    ->count('client_id');

                $taughtSessionsCount = Attendance::where('class_id', $classId)
                    ->where('instructor_id', $instructorId)
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->distinct('attendance_date')
                    ->count('attendance_date');

                $totalInstructorClients += $totalClients;

                $assignedIds = is_string($classSchedule->instructor_ids) ? json_decode($classSchedule->instructor_ids, true) : (array)$classSchedule->instructor_ids;
                $assignedIds = array_map('strval', $assignedIds ?? []);
                $isSubstitute = !in_array((string)$instructorId, $assignedIds);

                $classTotalFee = $this->calculateClassFee($classSchedule, $totalClients, $taughtSessionsCount, $feesConfig, $instructor, $isSubstitute);
                $classEarnings += $classTotalFee;
            }

            $grandTotal = $baseSalary + $maintenanceFee + $classEarnings;

            return [
                'id' => $instructor->id,
                'name' => $instructor->user->name ?? 'Unknown',
                'type' => $instructorType,
                'base_salary' => $baseSalary,
                'maintenance_fees' => $maintenanceFee,
                'class_teaching_fees' => $classTeachingFee,
                'total_clients' => $totalInstructorClients,
                'class_earnings' => $classEarnings,
                'grand_total' => $grandTotal,
                'total_classes' => $groupedByClass->count(),
            ];
        });

        return view('backends.reports.instructorReport', compact('instructors', 'startDate', 'endDate'));
    }

    // ==========================================
    // Instructor Report Detail Page (Web View)
    // ==========================================
    public function showInstructorReportDetail(Request $request, $id)
    {
        $data = $this->getInstructorReportData($request, $id);
        return view('backends.reports.instructorReportDetail', $data);
    }

    // ==========================================
    // Instructor Report PDF Download
    // ==========================================
    public function downloadInstructorReportPdf(Request $request, $id)
    {
        $data = $this->getInstructorReportData($request, $id);

        $pdf = Pdf::loadView('backends.reports.instructorReportPdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Payslip_' . str_replace(' ', '_', $data['instructor']->user->name ?? 'Instructor') . '_' . Carbon::parse($data['startDate'])->format('M_Y') . '.pdf';

        return $pdf->download($fileName);
    }

    // ==========================================
    // Core Data Fetching for Detail & PDF
    // ==========================================
    private function getInstructorReportData(Request $request, $id)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $instructor = Instructor::with(['user', 'categoryFees'])->findOrFail($id);

        $attendances = Attendance::with(['client', 'class.category'])
            ->where('instructor_id', $id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $feesConfig = InstructorCategoryFee::where('instructor_id', $id)->get();

        $groupedByCategory = [];
        $totalEarningsAggregated = 0;

        $attendancesByClass = $attendances->groupBy('class_id');

        foreach ($attendancesByClass as $classId => $records) {
            $firstRecord = $records->first();
            $classSchedule = $firstRecord->class;

            if (!$classSchedule) continue;

            $categoryName = $classSchedule->category->name ?? 'Others';

            $totalClients = Attendance::where('class_id', $classId)
                ->where('instructor_id', $id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->whereNotNull('client_id')
                ->distinct('client_id')
                ->count('client_id');

            $taughtSessionsCount = Attendance::where('class_id', $classId)
                ->where('instructor_id', $id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->distinct('attendance_date')
                ->count('attendance_date');

            $assignedIds = is_string($classSchedule->instructor_ids) ? json_decode($classSchedule->instructor_ids, true) : (array)$classSchedule->instructor_ids;
            $assignedIds = array_map('strval', $assignedIds ?? []);
            $isSubstitute = !in_array((string)$id, $assignedIds);

            $breakdown = $this->calculateClassFeeBreakdown($classSchedule, $totalClients, $taughtSessionsCount, $feesConfig, $instructor, $isSubstitute);

            $totalEarningsAggregated += $breakdown['total_fee'];

            $groupedByCategory[$categoryName][] = [
                'class_name' => $classSchedule->class_name,
                'time_range' => Carbon::parse($classSchedule->start_time)->format('g:ia') . '-' . Carbon::parse($classSchedule->end_time)->format('g:ia'),
                'is_substitute' => $isSubstitute,
                'base_rate' => $breakdown['base_rate'],
                'classes_qty' => $taughtSessionsCount,
                'base_total' => $breakdown['base_total'],
                'has_bonus' => $breakdown['has_bonus'],
                'bonus_threshold' => $breakdown['bonus_threshold'],
                'bonus_rate' => $breakdown['bonus_rate'],
                'bonus_qty' => $breakdown['bonus_qty'],
                'bonus_total' => $breakdown['bonus_total'],
            ];
        }

        $grossEarnings = $totalEarningsAggregated;
        $maintenanceFee = (float)($instructor->maintenance_fees ?? 0);
        $netPay = max(0, $grossEarnings - $maintenanceFee);

        // Calculate Reference ID (Payment YYMM + Instructor Code)
        $refDate = Carbon::parse($endDate);

        // If instructor_code exists in the table, use it. Otherwise fallback to ID formatting.
        $instructorCode = $instructor->instructor_code ?? str_pad($instructor->id, 7, '0', STR_PAD_LEFT);
        $referenceId = $refDate->format('ym') . $instructorCode;

        // Calculate Payment Date based on instructor's payment_date or end of month
        $payDay = $instructor->payment_date ?? $refDate->endOfMonth()->day;
        $safeDay = min($payDay, $refDate->endOfMonth()->day);
        $paymentDateStr = Carbon::create($refDate->year, $refDate->month, $safeDay)->format('d-M-Y');

        return [
            'instructor' => $instructor,
            'groupedByCategory' => $groupedByCategory,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'grossEarnings' => $grossEarnings,
            'maintenanceFee' => $maintenanceFee,
            'netPay' => $netPay,
            'referenceId' => $referenceId,
            'paymentDate' => $paymentDateStr,
            'paymentMethod' => $instructor->payment_method ?? 'KBZPay (Digital wallet)',
        ];
    }

    // ==========================================
    // Centralized Calculation Helper (For Main List)
    // ==========================================
    private function calculateClassFee($classSchedule, $totalClients, $taughtSessionsCount, $feesConfig, $instructor, $isSubstitute = false)
    {
        $calculatedFee = 0;

        if ($isSubstitute && $instructor) {
            $sessions = max(1, $taughtSessionsCount);
            $calculatedFee += ((float) ($instructor->class_teaching_fees ?? 0) * $sessions);
        }

        $categoryId = $classSchedule->category_id ?? ($classSchedule->category->id ?? null);
        $feeConfig = collect($feesConfig)->first(function ($fee) use ($categoryId) {
            return (int)$fee->category_id === (int)$categoryId;
        });

        if (!$feeConfig && collect($feesConfig)->count() === 1) {
            $feeConfig = collect($feesConfig)->first();
        }

        if (!$feeConfig) return $calculatedFee;

        $rawBonuses = $feeConfig->bonuses;
        if (is_string($rawBonuses)) {
            $decoded = json_decode($rawBonuses, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            $rawBonuses = is_array($decoded) ? $decoded : [];
        }
        $bonuses = collect(is_array($rawBonuses) ? $rawBonuses : [])->values();

        $feeType = trim($feeConfig->fee_type);

        if (in_array($feeType, ['full_time_fixed', 'part_time_tiered', 'fixed'])) {
            if ($bonuses->isNotEmpty()) {
                foreach ($bonuses as $tier) {
                    $min = (int) ($tier['min_students'] ?? $tier['threshold'] ?? 1);
                    $max = (!empty($tier['max_students'])) ? (int)$tier['max_students'] : PHP_INT_MAX;

                    if ($totalClients >= $min) {
                        $clientsInTier = min($totalClients, $max) - $min + 1;
                        if ($clientsInTier > 0) {
                            $type = $tier['bonus_type'] ?? 'fixed';
                            $amount = (float) ($tier['bonus_amount'] ?? $tier['amount'] ?? 0);

                            if ($type === 'fixed') {
                                $calculatedFee += $amount;
                            } elseif ($type === 'per_student') {
                                $calculatedFee += ($clientsInTier * $amount);
                            }
                        }
                    }
                }
            } else {
                if (in_array($feeType, ['part_time_tiered'])) {
                    $calculatedFee += (float) $feeConfig->fee_value;
                }
            }
        } elseif (in_array($feeType, ['full_time_percentage', 'part_time_percentage', 'percentage'])) {
            $classRevenue = 0;
            $bookings = Booking::with('package')->where('selected_class_id', $classSchedule->id)->where('status', 'confirmed')->get();
            foreach ($bookings as $booking) {
                $classRevenue += (float) ($booking->package->price ?? 0);
            }

            $percentage = (float) $feeConfig->fee_value;
            if ($percentage <= 0 && $bonuses->isNotEmpty()) {
                $percentage = (float) ($bonuses->first()['bonus_amount'] ?? $bonuses->first()['amount'] ?? 0);
            }

            $gross = $classRevenue * ($percentage / 100);

            $maintenanceFee = 0;
            if (in_array($feeType, ['part_time_percentage', 'percentage'])) {
                if ($bonuses->isNotEmpty()) {
                    $maintenanceFee = (float) ($bonuses->first()['bonus_amount'] ?? $bonuses->first()['amount'] ?? 0);
                }
            }
            if ($maintenanceFee == 0) {
                $maintenanceFee = (float) ($instructor->maintenance_fees ?? 0);
            }

            $calculatedFee += max(0, $gross - $maintenanceFee);
        }

        return $calculatedFee;
    }

    // ==========================================
    // Calculate Breakdown for Detail UI (Base Rate & Bonus)
    // ==========================================
    private function calculateClassFeeBreakdown($classSchedule, $totalClients, $taughtSessionsCount, $feesConfig, $instructor, $isSubstitute = false)
    {
        $breakdown = [
            'base_rate' => 0,
            'base_total' => 0,
            'has_bonus' => false,
            'bonus_threshold' => 0,
            'bonus_rate' => 0,
            'bonus_qty' => 0,
            'bonus_total' => 0,
            'total_fee' => 0
        ];

        $categoryId = $classSchedule->category_id ?? ($classSchedule->category->id ?? null);
        $feeConfig = collect($feesConfig)->first(function ($fee) use ($categoryId) {
            return (int)$fee->category_id === (int)$categoryId;
        });

        if (!$feeConfig && collect($feesConfig)->count() === 1) {
            $feeConfig = collect($feesConfig)->first();
        }

        if ($isSubstitute && $instructor) {
            $breakdown['base_rate'] = (float) ($instructor->class_teaching_fees ?? 0);
            $breakdown['base_total'] = $breakdown['base_rate'] * max(1, $taughtSessionsCount);
            $breakdown['total_fee'] = $breakdown['base_total'];
            return $breakdown;
        }

        if (!$feeConfig) return $breakdown;

        $rawBonuses = $feeConfig->bonuses;
        if (is_string($rawBonuses)) {
            $decoded = json_decode($rawBonuses, true);
            if (is_string($decoded)) $decoded = json_decode($decoded, true);
            $rawBonuses = is_array($decoded) ? $decoded : [];
        }
        $bonuses = collect(is_array($rawBonuses) ? $rawBonuses : [])->values();
        $feeType = trim($feeConfig->fee_type);

        if (in_array($feeType, ['full_time_fixed', 'part_time_tiered', 'fixed'])) {
            $breakdown['base_rate'] = (float) $feeConfig->fee_value;
            $breakdown['base_total'] = $breakdown['base_rate'] * max(1, $taughtSessionsCount);

            if ($bonuses->isNotEmpty()) {
                foreach ($bonuses as $tier) {
                    $min = (int) ($tier['min_students'] ?? $tier['threshold'] ?? 1);
                    if ($totalClients >= $min) {
                        $max = (!empty($tier['max_students'])) ? (int)$tier['max_students'] : PHP_INT_MAX;
                        $clientsInTier = min($totalClients, $max) - $min + 1;

                        if ($clientsInTier > 0) {
                            $type = $tier['bonus_type'] ?? 'fixed';
                            $amount = (float) ($tier['bonus_amount'] ?? $tier['amount'] ?? 0);

                            $breakdown['has_bonus'] = true;
                            $breakdown['bonus_threshold'] = $min - 1;

                            if ($type === 'per_student') {
                                $breakdown['bonus_rate'] = $amount;
                                $breakdown['bonus_qty'] = $clientsInTier;
                                $breakdown['bonus_total'] = $clientsInTier * $amount;
                            } else {
                                $breakdown['bonus_rate'] = $amount;
                                $breakdown['bonus_qty'] = 1;
                                $breakdown['bonus_total'] = $amount;
                            }
                        }
                    }
                }
            }
        } elseif (in_array($feeType, ['full_time_percentage', 'part_time_percentage', 'percentage'])) {
            $classRevenue = 0;
            $bookings = Booking::with('package')->where('selected_class_id', $classSchedule->id)->where('status', 'confirmed')->get();
            foreach ($bookings as $booking) {
                $classRevenue += (float) ($booking->package->price ?? 0);
            }
            $percentage = (float) $feeConfig->fee_value;
            $gross = $classRevenue * ($percentage / 100);

            $breakdown['base_rate'] = "({$percentage}%) " . number_format($classRevenue);
            $breakdown['base_total'] = $gross;
        }

        $breakdown['total_fee'] = $breakdown['base_total'] + $breakdown['bonus_total'];

        return $breakdown;
    }
}
