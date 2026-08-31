@extends('layouts.link')

@section('content')

    <!-- Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* --- Brand Variables --- */
        :root {
            --soma-primary: #BE9676; 
            --soma-secondary: #8D7E71; 
            --soma-bg: #FFF7E9; 
        }

        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, textarea, table, th, td {
            font-family: 'Fahkwang', sans-serif !important;
        }
        body { background-color: var(--soma-bg) !important; }

        /* --- General Section Styling --- */
        .section-bg { padding-bottom: 1rem; padding-top: 4rem; }
        .section-title { color: var(--soma-secondary); font-size: 2.5rem; margin-bottom: 0.5rem; font-weight: 700; }
        
        /* --- Custom Table Theming --- */
        .color-dark { color: var(--soma-secondary) !important; }
        .color-light { color: var(--soma-primary) !important; }

        .schedule-table-card { 
            background: #ffffff; 
            border-radius: 16px; 
            border: 1px solid rgba(190, 150, 118, 0.4); 
            box-shadow: 0 6px 16px rgba(141, 126, 113, 0.12); 
            padding: 24px 0 0 0; 
            margin-bottom: 40px; 
            overflow: hidden; 
        }
        
        .schedule-table { margin-bottom: 0; }
        .schedule-table thead th { 
            background-color: var(--soma-secondary); 
            color: #ffffff; font-size: 13px; font-weight: 700; 
            padding: 18px 14px; text-transform: uppercase; 
            letter-spacing: 0.5px; border: none;
        }
        .schedule-table tbody td { 
            padding: 18px 14px; vertical-align: middle; 
            border-bottom: 1px solid rgba(190, 150, 118, 0.25); 
            color: var(--soma-secondary); font-size: 14px; font-weight: 500;
        }
        .schedule-table tbody tr:hover td { background-color: rgba(190, 150, 118, 0.08); }
        .schedule-table tbody tr:last-child td { border-bottom: none; }

        /* Typography & Icons */
        .duration-text { font-size: 12px; color: rgba(141, 126, 113, 0.85); margin-top: 2px; }
        .class-main-title { font-size: 15px; font-weight: 700; color: var(--soma-secondary); margin: 0 0 4px 0; letter-spacing: -0.3px; }
        .badge-status-active-text { color: var(--soma-primary); font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .time-text { font-weight: 700; color: var(--soma-secondary); font-size: 14px; display: flex; align-items: center; }

        /* Status Pills */
        .status-pill { font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        
        /* Updated Approved Pill Colors */
        .pill-approved { background-color: var(--soma-secondary); color: var(--soma-bg); border: 1px solid var(--soma-secondary); }
        
        .pill-pending { background-color: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .pill-rejected { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .pill-queued { background-color: #fffbeb; color: #856404; border: 1px solid #ffeeba; }
        .pill-active { background-color: var(--soma-secondary); color: #ffffff; }
        
        /* Filter Toggle Buttons */
        .btn-outline-custom {
            color: var(--soma-secondary);
            border-color: rgba(141, 126, 113, 0.3);
            background-color: #ffffff;
            font-weight: 600;
            padding: 8px 24px;
            transition: all 0.3s ease;
        }
        .btn-check:checked + .btn-outline-custom { background-color: var(--soma-secondary); color: #ffffff; border-color: var(--soma-secondary); }
        .btn-outline-custom:hover { background-color: rgba(141, 126, 113, 0.1); color: var(--soma-secondary); }

        /* DataTables Customization */
        div.dataTables_wrapper { padding: 0 24px 24px 24px; }
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid rgba(190, 150, 118, 0.4);
            border-radius: 8px; padding: 6px 12px; outline: none; color: var(--soma-secondary);
        }
        div.dataTables_wrapper div.dataTables_filter input:focus { border-color: var(--soma-primary); box-shadow: 0 0 0 3px rgba(190, 150, 118, 0.1); }
        div.dataTables_wrapper .dataTables_paginate .paginate_button { 
            color: var(--soma-secondary) !important; border-radius: 6px; background: #F8F8F8 !important; border: 1px solid rgba(141, 126, 113, 0.2) !important; margin: 0 3px;
        }
        div.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: rgba(190, 150, 118, 0.1) !important; border: 1px solid transparent !important; color: var(--soma-secondary) !important; }
        div.dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--soma-secondary) !important; color: white !important; border: 1px solid var(--soma-secondary) !important; }
    </style>

    <div class="section-bg">
        <div class="container text-center">
            <h1 class="section-title">My Package History</h1>
            <p class="fs-5 lh-lg mx-auto mb-4" style="color: rgba(141, 126, 113, 0.85); max-width: 600px;">
                Review all your purchased packages, active classes left, and pending orders.
            </p>
        </div>
    </div>

    <div class="container pb-5">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="schedule-table-card">
            <!-- Filter Options -->
            <div class="d-flex justify-content-center mb-4 px-4">
                <div class="btn-group shadow-sm" role="group">
                    <input type="radio" class="btn-check package-status-filter" name="pkgFilter" id="pkgAll" value="" checked>
                    <label class="btn btn-outline-custom" for="pkgAll">All</label>

                    <input type="radio" class="btn-check package-status-filter" name="pkgFilter" id="pkgActive" value="ActiveStatus">
                    <label class="btn btn-outline-custom" for="pkgActive">Active & Queued</label>

                    <input type="radio" class="btn-check package-status-filter" name="pkgFilter" id="pkgPending" value="PendingStatus">
                    <label class="btn btn-outline-custom" for="pkgPending">Pending</label>
                    
                    <input type="radio" class="btn-check package-status-filter" name="pkgFilter" id="pkgFinished" value="FinishedStatus">
                    <label class="btn btn-outline-custom" for="pkgFinished">Finished</label>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="packageDataTable" class="table schedule-table w-100">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 20%;">Transaction Info</th>
                            <th style="width: 25%;">Package Details</th>
                            <th style="width: 20%;">Validity & Usage</th>
                            <th style="width: 15%;">Payment</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $pur)
                            @php
                                $pkg = $pur->package;
                                $now = now();
                                $expiryDate = $pur->expires_at ? \Carbon\Carbon::parse($pur->expires_at) : null;
                                $fixExpiryDate = $pur->fix_expires_at ? \Carbon\Carbon::parse($pur->fix_expires_at) : null;
                                
                                // Text specific for DataTable exact filtering
                                $filterText = "ActiveStatus"; 
                                if($pur->is_finished) $filterText = "FinishedStatus";
                                elseif(strtolower($pur->pay_status) == 'pending') $filterText = "PendingStatus";
                                elseif(strtolower($pur->pay_status) == 'rejected') $filterText = "RejectedStatus";
                                elseif($pur->is_queued) $filterText = "ActiveStatus";
                            @endphp
                            <tr>
                                <td class="color-dark fw-bold">{{ $loop->iteration }}</td>
                                
                                <!-- Transaction -->
                                <td>
                                    <div class="class-main-title font-monospace" style="font-size: 13px;">{{ $pur->transaction_no }}</div>
                                    <div class="duration-text mt-1"><i class="fas fa-calendar-alt me-1"></i> {{ $pur->created_at->format('d M Y, h:i A') }}</div>
                                    <div class="duration-text mt-1"><i class="fas fa-user-circle me-1"></i> {{ $pur->account_name }}</div>
                                </td>

                                <!-- Package Details -->
                                <td>
                                    <h4 class="class-main-title">{{ $pkg->name ?? 'Package Plan' }}</h4>
                                    <div class="badge-status-active-text">{{ $pkg->category->name ?? 'Type' }}</div>
                                    <div class="duration-text mt-2 fw-bold color-dark">Amount: {{ number_format($pur->amount) }} MMK</div>
                                    <div class="duration-text mt-1" style="color: var(--soma-primary);"><i class="fas fa-coins me-1"></i> Earned {{ $pkg->loyal_point ?? 0 }} Coins</div>
                                </td>
                                
                                <!-- Validity & Usage -->
                                <td>
                                    <div class="time-text"><i class="fas fa-dumbbell me-1 color-light"></i> {{ $pur->class_remaining }} Classes Left</div>
                                    @if($pur->pay_status === 'confirmed' && $expiryDate)
                                        @if($pur->is_queued)
                                            <div class="duration-text mt-2"><i class="fas fa-hourglass-start me-1 text-warning"></i> Starts on first use</div>
                                        @elseif($expiryDate->isFuture() && $fixExpiryDate?->isFuture())
                                            @php $diffd = $expiryDate->diff($now); @endphp
                                            <div class="duration-text mt-2"><i class="fas fa-clock me-1 text-success"></i> Expires in {{ $diffd->m > 0 ? $diffd->m.'m ' : '' }}{{ $diffd->d > 0 ? $diffd->d.'d' : $diffd->h.'h' }}</div>
                                        @else
                                            <!-- Updated Expired Color -->
                                            <div class="duration-text mt-2" style="color: var(--soma-secondary);"><i class="fas fa-calendar-times me-1"></i> Expired</div>
                                        @endif
                                    @else
                                        <div class="duration-text mt-2">-</div>
                                    @endif
                                </td>

                                <!-- Payment Status -->
                                <td>
                                    @if(strtolower($pur->pay_status) == 'confirmed')
                                        <span class="status-pill pill-approved"><i class="fas fa-check-circle me-1"></i> Approved</span>
                                    @elseif(strtolower($pur->pay_status) == 'rejected')
                                        <span class="status-pill pill-rejected"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                    @else
                                        <span class="status-pill pill-pending"><i class="fas fa-hourglass-half me-1"></i> Pending</span>
                                    @endif
                                    <div class="duration-text mt-2">{{ $pur->payment_method }}</div>
                                </td>

                                <!-- Final Package Status -->
                                <td>
                                    <!-- Hidden text for DataTable filter matching -->
                                    <span class="d-none">{{ $filterText }}</span>
                                    
                                    @if($pur->is_finished)
                                        <!-- Updated Finished Color -->
                                        <div class="fw-bold" style="color: var(--soma-secondary); font-size: 13px;"><i class="fas fa-flag-checkered me-1"></i> Finished</div>
                                    @elseif(strtolower($pur->pay_status) == 'pending')
                                        <div class="fw-bold color-dark" style="font-size: 13px;"><i class="fas fa-spinner fa-spin me-1 color-light"></i> Wait Approval</div>
                                    @elseif(strtolower($pur->pay_status) == 'rejected')
                                        <div class="fw-bold text-danger" style="font-size: 13px;"><i class="fas fa-ban me-1"></i> Rejected</div>
                                    @elseif($pur->is_active_now)
                                        <span class="status-pill pill-active"><i class="fas fa-play-circle me-1"></i> Active Now</span>
                                    @elseif($pur->is_queued)
                                        <span class="status-pill pill-queued"><i class="fas fa-list-ol me-1"></i> Queued</span>
                                    @else
                                        <span class="status-pill pill-active"><i class="fas fa-check me-1"></i> Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Init Package Table
            var pkgTable = $('#packageDataTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "order": [], // Keeps chronological order from Backend
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "emptyTable": "No package purchases found."
                },
                "columnDefs": [
                    { "orderable": false, "targets": [4, 5] } // Disable sorting on status cols
                ],
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });

            // Status Filter Logic
            $('.package-status-filter').on('change', function() {
                var filterValue = $(this).val();
                
                // Column 5 is "Status"
                if (filterValue) {
                    pkgTable.column(5).search(filterValue).draw();
                } else {
                    pkgTable.column(5).search('').draw();
                }
            });
        });
    </script>
@endsection