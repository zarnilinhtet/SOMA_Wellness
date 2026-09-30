<?php echo view('master.header'); ?>


<style>
    .page-inner { padding-top: 2rem; padding-bottom: 2rem; background-color: #fcfbf9; }
    
    /* Soma Studio Styles */
    .soma-title { color: #8b7355; font-weight: 700; font-size: 1.8rem; border-left: 3px solid #8b7355; padding-left: 10px; margin-bottom: 1.5rem; }
    
    /* Info Table */
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; background-color: #fdfaf4; }
    .info-table td, .info-table th { border: 1px solid #c2dce6; padding: 12px 15px; vertical-align: middle; }
    .info-table th { font-weight: bold; color: #000; width: 20%; background-color: #fdfaf4; }
    .info-table td { color: #333; width: 30%; }

    /* Earning Breakdown Table */
    .earning-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; background-color: #fff; }
    .earning-table thead th { background-color: #8c7b6c; color: #fff; padding: 12px 15px; font-weight: 600; text-align: center; border: 1px solid #fff;}
    .earning-table thead th:first-child { text-align: left; }
    
    .category-row { background-color: #c09d7e; color: #fff; font-weight: 600; }
    .category-row td { padding: 8px 15px; border: 1px solid #fff; }
    
    .earning-table tbody td { padding: 12px 15px; border: 1px solid #e9ecef; border-bottom: 1px solid #ddd; }
    .earning-table tbody tr td:not(:first-child) { text-align: right; }
    
    /* Summary Table */
    .summary-table { width: 100%; border-collapse: collapse; margin-top: 1rem; border: 1px solid #000; }
    .summary-table td { padding: 12px 15px; border: 1px solid #000; color: #000; }
    .summary-table .label-col { font-weight: bold; }
    .summary-table .amount-col { text-align: right; font-weight: 600; width: 25%; }
    .summary-table .net-pay { font-weight: 700; font-size: 1.1rem; }

    /* =========================================
       Print CSS (For Print & Browser Save as PDF)
       ========================================= */
    @media print {
        @page { margin: 0.5cm; size: A4 portrait; }
        
        /* Hide everything else on the page */
        body * { visibility: hidden; }
        
        /* Make only the report visible */
        #printableArea, #printableArea * { visibility: visible; }
        
        /* Reset position to top left */
        #printableArea { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
            padding: 0 !important; 
            margin: 0 !important; 
            box-shadow: none !important; 
            border: none !important; 
        }
        
        /* Hide action buttons when printing */
        .no-print { display: none !important; }
        
        /* Force browser to print background colors */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="container">
    <div class="page-inner">
        
        <!-- Action Buttons (Hidden during Print) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('instructor.report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-arrow-left me-2"></i> Back to Report
            </a>
            
            <div>
              

                <!-- Existing Backend PDF Download Button -->
                <a href="{{ route('instructor.report.pdf', ['id' => $instructor->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-primary rounded-pill px-4 shadow-sm" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Printable Area Wrapper -->
        <div id="printableArea" class="card shadow-sm border-0 rounded-0 p-4 p-md-5 bg-white">
            <h2 class="soma-title">Soma Wellness Studio</h2>

            <!-- Info Section -->
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>Instructor Name:</th>
                        <td>{{ $instructor->user->name ?? 'Unknown' }}</td>
                        <th>Pay Period:</th>
                        <td>{{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Employment status:</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $instructor->instructor_type ?? 'Unknown')) }}</td>
                        <th>Payment Date:</th>
                        <td>{{ $paymentDate }}</td>
                    </tr>
                    <tr>
                        <th>Reference ID:</th>
                        <td>{{ $referenceId }}</td>
                        <th>Payment Method:</th>
                        <td>{{ $paymentMethod }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Earning Breakdown Section -->
            <h5 class="fw-bold mb-3" style="color: #000;">Earning Breakdown</h5>
            <div class="table-responsive">
                <table class="earning-table">
                    <thead>
                        <tr>
                            <th>Description / Session type</th>
                            <th width="15%">Rate (MMK)</th>
                            <th width="15%">Qty / Hours</th>
                            <th width="20%">Total Amount (MMK)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedByCategory as $category => $classes)
                            <tr class="category-row">
                                <td colspan="4">{{ $category }}</td>
                            </tr>
                            
                            @foreach($classes as $cls)
                                <!-- Base Rate Row -->
                                <tr>
                                    <td>{{ $cls['class_name'] }} ({{ $cls['time_range'] }}) @if($cls['is_substitute']) <small class="text-danger">(Sub)</small> @endif</td>
                                    <td>{{ is_numeric($cls['base_rate']) ? number_format($cls['base_rate']) : $cls['base_rate'] }}</td>
                                    <td>{{ $cls['classes_qty'] }} classes</td>
                                    <td>{{ number_format($cls['base_total']) }}</td>
                                </tr>
                                
                                <!-- Bonus Rate Row -->
                                @if($cls['has_bonus'])
                                <tr>
                                    <td>{{ $cls['class_name'] }} Headcount bonus (>{{ $cls['bonus_threshold'] }} ppl)</td>
                                    <td>{{ number_format($cls['bonus_rate']) }}</td>
                                    <td>{{ $cls['bonus_qty'] }} students</td>
                                    <td>{{ number_format($cls['bonus_total']) }}</td>
                                </tr>
                                @endif
                            @endforeach
                            
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No class earnings found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <table class="summary-table mt-4">
                <tbody>
                    <tr>
                        <td class="label-col">Gross Earnings:</td>
                        <td class="amount-col">MMK {{ number_format($grossEarnings) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">
                            Deductions: <span style="font-weight: normal; margin-left: 20px;">Studio Maintenance fees (if applicable)</span>
                        </td>
                        <td class="amount-col">MMK {{ number_format($maintenanceFee) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col net-pay">Net Pay Deposited:</td>
                        <td class="amount-col net-pay">MMK {{ number_format($netPay) }}</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </div>
</div>

<?php echo view('master.footer'); ?>