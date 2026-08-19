@if($tab === 'rates')
    {{-- Google Fonts for Fahkwang (Included if not already in layouts.link, ensuring it works here) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    /* ================= BRAND VARIABLES & FONTS ================= */
    :root {
        --soma-primary: #BE9676; /* Perfect Beige */
        --soma-secondary: #8D7E71; /* Desert Taupe */
        --soma-bg: #FFF7E9; /* Soft Cream */
    }

    body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea, table, th, td, label, ul, li {
        font-family: 'Fahkwang', sans-serif !important;
    }

    /* ================= INDEPENDENT 2-COLUMN LAYOUT ================= */
    .purchase-columns-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }

    @media (min-width: 992px) {
        .purchase-columns-wrapper {
            display: flex;
            flex-direction: row;
            align-items: flex-start; 
            gap: 16px;
        }

        .purchase-column {
            flex: 1; 
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-width: 0; 
        }
    }

    .purchase-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }

    .purchase-item-container {
        width: 100%;
        box-sizing: border-box;
    }

    .purchase-empty-full-width {
        width: 100%;
    }

    /* ================= COLLAPSIBLE PURCHASE CARDS ================= */
    .purchase-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.25s ease,
                    border-color 0.2s ease;
        cursor: pointer;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    .purchase-card:active {
        transform: scale(0.99);
        background: #f8fafc;
    }

    .card-trigger-header {
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
    }

    .header-left-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .header-right-status {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .package-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--soma-secondary); /* Updated to Brand Color */
        margin-bottom: 2px;
    }

    .meta-date {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 500;
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-status.pending {
        background: #fffbeb;
        color: #b45309;
    }

    .badge-status.approved {
        background: #f0fdf4;
        color: #15803d;
    }

    .badge-status.rejected {
        background: #fee2e2;
        color: #b91c1c;
    }

    .chevron-icon {
        color: var(--soma-secondary); /* Updated to Brand Color */
        transition: transform 0.25s ease;
        font-size: 13px;
        margin-left: 10px;
    }

    /* Collapsible Drawer */
    .card-details-collapsible {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        visibility: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, visibility 0.25s;
        background: #fafafa;
        border-top: 1px solid transparent;
    }

    /* Expanded Card States */
    .purchase-card.is-expanded {
        box-shadow: 0 10px 15px -3px rgba(141, 126, 113, 0.1); /* Updated Shadow Color */
        border-color: rgba(190, 150, 118, 0.4); /* Updated Border Color */
    }

    .purchase-card.is-expanded .card-details-collapsible {
        max-height: 600px;
        opacity: 1;
        visibility: visible;
        border-top-color: #f1f5f9;
    }

    .purchase-card.is-expanded .chevron-icon {
        transform: rotate(180deg);
        color: var(--soma-primary); /* Updated to Brand Color */
    }

    .details-body {
        padding: 16px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px 12px;
    }

    .info-block {
        display: flex;
        flex-direction: column;
    }

    .label {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
        font-weight: 600;
    }

    .value {
        font-size: 13px;
        font-weight: 600;
        color: var(--soma-secondary); /* Updated to Brand Color */
        word-break: break-all;
    }

    /* UPDATED: Cancel Alert Box Color */
    .rejection-reason-box {
        background: var(--soma-bg); /* Updated to Brand Background */
        border-left: 3px solid var(--soma-primary); /* Updated to Brand Primary */
        padding: 12px 14px;
        border-radius: 4px;
        margin-top: 12px;
    }

    .rejection-label {
        font-size: 11px;
        color: var(--soma-primary); /* Updated to Brand Primary */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .empty-state {
        background: #ffffff;
        padding: 40px 24px;
        border-radius: 16px;
        border: 1px dashed rgba(141, 126, 113, 0.3); /* Updated to Brand Color */
        text-align: center;
        margin-top: 10px;
    }

    /* Pagination */
    .soma-pagination-wrap {
        display: flex;
        justify-content: center;
        margin-top: 3rem;
    }

    .soma-pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .soma-pagination li a,
    .soma-pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        border-radius: 50%;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid rgba(190, 150, 118, 0.3); /* Updated to Brand Primary */
        background: transparent;
        color: var(--soma-secondary); /* Updated to Brand Secondary */
        transition: all 0.3s ease;
    }

    .soma-pagination li a:hover,
    .soma-pagination li.active span {
        background: var(--soma-secondary); /* Updated to Brand Secondary */
        color: white;
        border-color: var(--soma-secondary);
    }

    .soma-pagination li.disabled span {
        opacity: 0.3;
        cursor: not-allowed;
        background: transparent;
        color: rgba(141, 126, 113, 0.5); /* Updated to Brand Secondary */
    }

    /* Custom Button Color for Re-join */
    .btn-outline-custom-success {
        color: var(--soma-primary);
        border-color: var(--soma-primary);
        background-color: transparent;
        transition: all 0.2s ease;
    }
    .btn-outline-custom-success:hover {
        background-color: var(--soma-primary);
        color: #ffffff;
    }
    </style>

    {{-- ================= RATES TAB CONTAINER ================= --}}
    <div id="purchasesList">
        @if($purchases->count() > 0)
            @php $categoriesWithActive = []; @endphp

            <div class="purchase-columns-wrapper">
                {{-- LEFT COLUMN (Odd Items) --}}
                <div class="purchase-column">
                    @foreach($purchases->values() as $index => $pur)
                        @if($index % 2 === 0)
                            @php
                                $pkg = $pur->package;
                                $catName = $pkg->category->name ?? 'Type';
                                $categoryId = $pkg->type ?? 'default';
                                $expiryDate = $pur->expires_at ? \Carbon\Carbon::parse($pur->expires_at) : null;
                                $fixExpiryDate = $pur->fix_expires_at ? \Carbon\Carbon::parse($pur->fix_expires_at) : null;
                                $now = now();
                                $isValidPackage = $pur->class_remaining > 0 && $pur->pay_status === 'confirmed' && $fixExpiryDate?->isFuture() && $expiryDate?->isFuture();
                                $searchableText = strtolower(implode(' ', [$pur->transaction_no ?? '', $pkg->name ?? '', $pur->payment_method ?? '', $pur->account_name ?? '', $pur->phone ?? '']));
                            @endphp

                            <div class="purchase-item-container" data-category="rates" data-searchable="{{ $searchableText }}">
                                <div class="purchase-card" onclick="toggleCard(this, event)">
                                    <div class="card-trigger-header">
                                        <!-- Header Content -->
                                        <div class="header-left-content">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="package-title">{{ $pkg->name ?? 'Package Plan' }}</div>
                                                <div class="text-muted">({{ $catName }})</div>
                                                @if(!in_array($categoryId, $categoriesWithActive) && $isValidPackage)
                                                    <span class="badge-status pending">Using</span>
                                                    @php $categoriesWithActive[] = $categoryId; @endphp
                                                @endif
                                            </div>

                                            <div class="d-flex gap-1 mt-1 align-items-center">
                                                @if($pur->pay_status === 'confirmed' && $expiryDate)
                                                    @php $diffd = $expiryDate->diff($now); @endphp
                                                    @if($expiryDate->isFuture() && $fixExpiryDate?->isFuture())
                                                        <p class="mb-0 text-muted" style="font-size: 13px;">
                                                            <i class="bi bi-calendar-check me-1"></i> Expires in
                                                            <strong>
                                                                @if($diffd->m > 0) {{ $diffd->m }} month{{ $diffd->m !== 1 ? 's' : '' }} and @endif
                                                                @if($diffd->d > 0) {{ $diffd->d }} day{{ $diffd->d !== 1 ? 's' : '' }} @else {{ $diffd->h }} hour{{ $diffd->h !== 1 ? 's' : '' }} @endif
                                                            </strong>
                                                        </p>
                                                    @else
                                                        <p class="mb-0 text-danger" style="font-size: 13px;">
                                                            <i class="bi bi-calendar-x me-1"></i> Expired on
                                                            {{ $fixExpiryDate?->isPast() ? $fixExpiryDate->format('d M Y') : $expiryDate->format('d M Y') }}
                                                        </p>
                                                    @endif
                                                    <span class="mx-1 text-muted">•</span>
                                                    <span class="fw-semibold" style="font-size: 13px; color: var(--soma-primary);">{{ $pur->class_remaining }} Classes Left</span>
                                                @endif
                                            </div>

                                            <div class="meta-date">
                                                <span>{{ $pur->created_at->format('d M Y') }}</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span class="text-secondary">{{ $pur->payment_method }}</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span class="text-secondary">{{ $pur->amount }} K</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span style="color: var(--soma-primary);">Got {{ $pkg->loyal_point ?? 0 }} Coins</span>
                                            </div>
                                        </div>

                                        <div class="header-right-status">
                                            @switch($pur->pay_status)
                                                @case('confirmed') <span class="badge-status approved">Approved</span> @break
                                                @case('rejected') <span class="badge-status rejected">Rejected</span> @break
                                                @default <span class="badge-status pending">Pending</span>
                                            @endswitch
                                            <i class="bi bi-chevron-down chevron-icon ms-2"></i>
                                        </div>
                                    </div>

                                    <div class="card-details-collapsible">
                                        <div class="details-body">
                                            @if($pur->pay_status === 'confirmed' && $fixExpiryDate)
                                                @php $diff = $fixExpiryDate->diff($now); @endphp
                                                @if ($pur->class_remaining == ($pkg->class_count ?? 0) && $fixExpiryDate->isFuture() && $expiryDate?->isFuture())
                                                    <div class="d-flex align-items-center p-2 mb-3 bg-light border-start border-4 rounded shadow-sm" style="border-color: var(--soma-primary) !important;">
                                                        <i class="bi bi-clock-history me-3 fs-5" style="color: var(--soma-primary);"></i>
                                                        <div>
                                                            <p class="mb-0 text-muted" style="font-size: 12px; line-height: 1.4;">
                                                                To keep your package active, please book a class within
                                                                <b>
                                                                    @if($diff->m > 0) {{ $diff->m }} month{{ $diff->m !== 1 ? 's' : '' }} and @endif
                                                                    @if($diff->d > 0) {{ $diff->d }} day{{ $diff->d !== 1 ? 's' : '' }} @else {{ $diff->h }} hour{{ $diff->h !== 1 ? 's' : '' }} @endif
                                                                </b> of your last session.
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            <div class="info-grid">
                                                <div class="info-block">
                                                    <div class="label">Transaction No</div>
                                                    <div class="value font-monospace" style="font-size: 12px; color: var(--soma-primary);">{{ $pur->transaction_no }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Account Name</div>
                                                    <div class="value">{{ $pur->account_name }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Phone Connected</div>
                                                    <div class="value">{{ $pur->phone ?? 'Not provided' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Payment Method</div>
                                                    <div class="value">{{ $pur->payment_method }}</div>
                                                </div>
                                            </div>

                                            @if($pur->pay_status === 'rejected' && !empty($pur->rejection_reason))
                                                <div class="rejection-reason-box mt-3">
                                                    <div class="rejection-label">Reason for Rejection</div>
                                                    <div class="value text-dark fw-medium" style="font-size: 12.5px;">{{ $pur->rejection_reason }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- RIGHT COLUMN (Even Items) --}}
                <div class="purchase-column">
                    @foreach($purchases->values() as $index => $pur)
                        @if($index % 2 !== 0)
                            @php
                                $pkg = $pur->package;
                                $catName = $pkg->category->name ?? 'Type';
                                $categoryId = $pkg->type ?? 'default';
                                $expiryDate = $pur->expires_at ? \Carbon\Carbon::parse($pur->expires_at) : null;
                                $fixExpiryDate = $pur->fix_expires_at ? \Carbon\Carbon::parse($pur->fix_expires_at) : null;
                                $now = now();
                                $isValidPackage = $pur->class_remaining > 0 && $pur->pay_status === 'confirmed' && $fixExpiryDate?->isFuture() && $expiryDate?->isFuture();
                                $searchableText = strtolower(implode(' ', [$pur->transaction_no ?? '', $pkg->name ?? '', $pur->payment_method ?? '', $pur->account_name ?? '', $pur->phone ?? '']));
                            @endphp

                            <div class="purchase-item-container" data-category="rates" data-searchable="{{ $searchableText }}">
                                <div class="purchase-card" onclick="toggleCard(this, event)">
                                    <div class="card-trigger-header">
                                        <!-- Header Content -->
                                        <div class="header-left-content">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="package-title">{{ $pkg->name ?? 'Package Plan' }}</div>
                                                <div class="text-muted">({{ $catName }})</div>
                                                @if(!in_array($categoryId, $categoriesWithActive) && $isValidPackage)
                                                    <span class="badge-status pending">Using</span>
                                                    @php $categoriesWithActive[] = $categoryId; @endphp
                                                @endif
                                            </div>

                                            <div class="d-flex gap-1 mt-1 align-items-center">
                                                @if($pur->pay_status === 'confirmed' && $expiryDate)
                                                    @php $diffd = $expiryDate->diff($now); @endphp
                                                    @if($expiryDate->isFuture() && $fixExpiryDate?->isFuture())
                                                        <p class="mb-0 text-muted" style="font-size: 13px;">
                                                            <i class="bi bi-calendar-check me-1"></i> Expires in
                                                            <strong>
                                                                @if($diffd->m > 0) {{ $diffd->m }} month{{ $diffd->m !== 1 ? 's' : '' }} and @endif
                                                                @if($diffd->d > 0) {{ $diffd->d }} day{{ $diffd->d !== 1 ? 's' : '' }} @else {{ $diffd->h }} hour{{ $diffd->h !== 1 ? 's' : '' }} @endif
                                                            </strong>
                                                        </p>
                                                    @else
                                                        <p class="mb-0 text-danger" style="font-size: 13px;">
                                                            <i class="bi bi-calendar-x me-1"></i> Expired on
                                                            {{ $fixExpiryDate?->isPast() ? $fixExpiryDate->format('d M Y') : $expiryDate->format('d M Y') }}
                                                        </p>
                                                    @endif
                                                    <span class="mx-1 text-muted">•</span>
                                                    <span class="fw-semibold" style="font-size: 13px; color: var(--soma-primary);">{{ $pur->class_remaining }} Classes Left</span>
                                                @endif
                                            </div>

                                            <div class="meta-date">
                                                <span>{{ $pur->created_at->format('d M Y') }}</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span class="text-secondary">{{ $pur->payment_method }}</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span class="text-secondary">{{ $pur->amount }} K</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span style="color: var(--soma-primary);">Got {{ $pkg->loyal_point ?? 0 }} Coins</span>
                                            </div>
                                        </div>

                                        <div class="header-right-status">
                                            @switch($pur->pay_status)
                                                @case('confirmed') <span class="badge-status approved">Approved</span> @break
                                                @case('rejected') <span class="badge-status rejected">Rejected</span> @break
                                                @default <span class="badge-status pending">Pending</span>
                                            @endswitch
                                            <i class="bi bi-chevron-down chevron-icon ms-2"></i>
                                        </div>
                                    </div>

                                    <div class="card-details-collapsible">
                                        <div class="details-body">
                                            @if($pur->pay_status === 'confirmed' && $fixExpiryDate)
                                                @php $diff = $fixExpiryDate->diff($now); @endphp
                                                @if ($pur->class_remaining == ($pkg->class_count ?? 0) && $fixExpiryDate->isFuture() && $expiryDate?->isFuture())
                                                    <div class="d-flex align-items-center p-2 mb-3 bg-light border-start border-4 rounded shadow-sm" style="border-color: var(--soma-primary) !important;">
                                                        <i class="bi bi-clock-history me-3 fs-5" style="color: var(--soma-primary);"></i>
                                                        <div>
                                                            <p class="mb-0 text-muted" style="font-size: 12px; line-height: 1.4;">
                                                                To keep your package active, please book a class within
                                                                <b>
                                                                    @if($diff->m > 0) {{ $diff->m }} month{{ $diff->m !== 1 ? 's' : '' }} and @endif
                                                                    @if($diff->d > 0) {{ $diff->d }} day{{ $diff->d !== 1 ? 's' : '' }} @else {{ $diff->h }} hour{{ $diff->h !== 1 ? 's' : '' }} @endif
                                                                </b> of your last session.
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            <div class="info-grid">
                                                <div class="info-block">
                                                    <div class="label">Transaction No</div>
                                                    <div class="value font-monospace" style="font-size: 12px; color: var(--soma-primary);">{{ $pur->transaction_no }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Account Name</div>
                                                    <div class="value">{{ $pur->account_name }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Phone Connected</div>
                                                    <div class="value">{{ $pur->phone ?? 'Not provided' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Payment Method</div>
                                                    <div class="value">{{ $pur->payment_method }}</div>
                                                </div>
                                            </div>

                                            @if($pur->pay_status === 'rejected' && !empty($pur->rejection_reason))
                                                <div class="rejection-reason-box mt-3">
                                                    <div class="rejection-label">Reason for Rejection</div>
                                                    <div class="value text-dark fw-medium" style="font-size: 12.5px;">{{ $pur->rejection_reason }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="purchase-empty-full-width">
                <div class="empty-state text-center py-4">
                    <div class="fs-2 mb-2">🎁</div>
                    <div class="fw-bold" style="color: var(--soma-secondary);">No purchase rates records yet</div>
                </div>
            </div>
        @endif

        @if (method_exists($purchases, 'hasPages') && $purchases->hasPages())
            @include('partials.pagination', ['paginator' => $purchases])
        @endif
    </div>
@else
    {{-- ================= CLASSES TAB CONTAINER ================= --}}
    
    {{-- Google Fonts for Fahkwang (Included for Classes Tab) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    /* Ensure styles load when tab toggled */
    :root {
        --soma-primary: #BE9676; 
        --soma-secondary: #8D7E71; 
        --soma-bg: #FFF7E9; 
    }
    body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea, table, th, td, label, ul, li {
        font-family: 'Fahkwang', sans-serif !important;
    }
    .package-title, .value { color: var(--soma-secondary); }
    .chevron-icon { color: var(--soma-secondary); }
    .purchase-card.is-expanded { box-shadow: 0 10px 15px -3px rgba(141, 126, 113, 0.1); border-color: rgba(190, 150, 118, 0.4); }
    .purchase-card.is-expanded .chevron-icon { color: var(--soma-primary); }
    .rejection-reason-box { background: var(--soma-bg); border-left: 3px solid var(--soma-primary); }
    .rejection-label { color: var(--soma-primary); }
    .empty-state { border: 1px dashed rgba(141, 126, 113, 0.3); }
    .soma-pagination li a, .soma-pagination li span { border: 1px solid rgba(190, 150, 118, 0.3); color: var(--soma-secondary); }
    .soma-pagination li a:hover, .soma-pagination li.active span { background: var(--soma-secondary); border-color: var(--soma-secondary); color: white; }
    .soma-pagination li.disabled span { color: rgba(141, 126, 113, 0.5); }
    .btn-outline-custom-success { color: var(--soma-primary); border-color: var(--soma-primary); background-color: transparent; transition: all 0.2s ease; }
    .btn-outline-custom-success:hover { background-color: var(--soma-primary); color: #ffffff; }
    </style>

    <div id="classesList">
        @if($classes->count() > 0)
            <div class="purchase-columns-wrapper">
                {{-- LEFT COLUMN --}}
                <div class="purchase-column">
                    @foreach($classes->values() as $index => $class)
                        @if($index % 2 === 0)
                            @php
                                $classDetails = $class->class;
                                $instructors = $classDetails->instructors ?? [];
                                $instructorNames = collect($instructors)->pluck('user.name')->filter()->implode(' • ');
                                $searchableText = strtolower(($classDetails->class_name ?? '') . ' ' . $instructorNames . ' ' . ($class->status ?? ''));
                                
                                // Check if class starts in more than 24 hours
                                $canCancel = false;
                                if(isset($classDetails->start_date) && isset($classDetails->start_time)) {
                                    $startDateTime = \Carbon\Carbon::parse($classDetails->start_date . ' ' . $classDetails->start_time);
                                    $canCancel = $startDateTime->copy()->subHours(24)->isFuture();
                                }
                            @endphp

                            <div class="purchase-item-container" data-category="class" data-searchable="{{ $searchableText }}">
                                <div class="purchase-card" onclick="toggleCard(this, event)">
                                    <div class="card-trigger-header">
                                        <div class="header-left-content">
                                            <div class="package-title">{{ $classDetails->class_name ?? 'Class Plan' }}</div>
                                            <div class="meta-date">
                                                <span>{{ isset($classDetails->start_date) ? \Carbon\Carbon::parse($classDetails->start_date)->format('d M Y') : $class->created_at->format('d M Y') }}</span>
                                                @if($instructorNames)
                                                    <span class="mx-1 text-muted">•</span>
                                                    <span class="text-secondary">{{ $instructorNames }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="header-right-status">
                                            @if(in_array($class->status, ['confirmed', 'approved']))
                                                <span class="badge-status approved">Joined</span>
                                            @elseif(in_array($class->status, ['cancelled', 'rejected']))
                                                <span class="badge-status rejected">Cancelled</span>
                                            @elseif($class->status === 'waitlisted')
                                                <span class="badge-status pending">Waitlisted</span>
                                            @endif
                                            <i class="bi bi-chevron-down chevron-icon ms-2"></i>
                                        </div>
                                    </div>

                                    <div class="card-details-collapsible">
                                        <div class="details-body">
                                            <div class="info-grid">
                                                <div class="info-block">
                                                    <div class="label">Class Name</div>
                                                    <div class="value text-dark">{{ $classDetails->class_name ?? '-' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Instructor</div>
                                                    <div class="value">{{ collect($instructors)->pluck('user.name')->implode(' | ') ?: '-' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Start Time</div>
                                                    <div class="value text-success">{{ isset($classDetails->start_date) ? \Carbon\Carbon::parse($classDetails->start_date . ' ' . $classDetails->start_time)->format('d M Y, h:i A') : '-' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">End Time</div>
                                                    <div class="value text-danger">{{ isset($classDetails->end_date) ? \Carbon\Carbon::parse($classDetails->end_date . ' ' . $classDetails->end_time)->format('d M Y, h:i A') : '-' }}</div>
                                                </div>
                                            </div>

                                            {{-- Clean Re-join UI --}}
                                            @if(in_array($class->status, ['cancelled', 'rejected']))
                                                <div class="rejection-reason-box mt-3 mb-0">
                                                    <div class="rejection-label">{{ $class->status === 'cancelled' ? 'Cancellation Reason' : 'Reason Notes' }}</div>
                                                    <div class="value text-dark fw-medium" style="font-size: 12.5px; margin-bottom: {{ $class->status === 'cancelled' ? '12px' : '0' }};">
                                                        {{ $class->cancellation_reason ?? 'No reason provided.' }}
                                                    </div>
                                                    
                                                    @if($class->status === 'cancelled')
                                                        <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center" style="border-color: rgba(190, 150, 118, 0.3) !important;">
                                                            <span style="font-size: 12px; color: var(--soma-primary);"><i class="bi bi-info-circle me-1"></i> Changed your mind?</span>
                                                            <a href="{{ route('join.class', $class->selected_class_id) }}" class="btn btn-sm btn-outline-custom-success py-1 px-3" onclick="return confirm('Are you sure you want to re-join this class?');">
                                                                <i class="bi bi-arrow-repeat me-1"></i> Re-join
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- CANCEL BUTTON (Show if Confirmed or Waitlisted) --}}
                                            @if(in_array($class->status, ['confirmed', 'waitlisted']))
                                                <hr style="border-color: rgba(141, 126, 113, 0.2); margin: 16px 0 12px 0;">
                                                @if($canCancel)
                                                    <!-- Fixed Modal Target ID to use $class->id -->
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#cancelModal_{{ $class->id }}" style="color: #dc3545; border-color: #dc3545;">
                                                        <i class="bi bi-x-circle me-1"></i> Cancel Booking
                                                    </button>
                                                @else
                                                    <div class="text-center fw-medium" style="color: var(--soma-secondary); font-size: 11.5px; background: rgba(141, 126, 113, 0.05); padding: 10px; border-radius: 6px; border: 1px dashed rgba(141, 126, 113, 0.3);">
                                                        <i class="bi bi-exclamation-circle me-1" style="color: var(--soma-primary);"></i> 
                                                        Cancellation is only allowed 24 hours prior to class start.
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="purchase-column">
                    @foreach($classes->values() as $index => $class)
                        @if($index % 2 !== 0)
                            @php
                                $classDetails = $class->class;
                                $instructors = $classDetails->instructors ?? [];
                                $instructorNames = collect($instructors)->pluck('user.name')->filter()->implode(' • ');
                                $searchableText = strtolower(($classDetails->class_name ?? '') . ' ' . $instructorNames . ' ' . ($class->status ?? ''));
                                
                                // Check if class starts in more than 24 hours
                                $canCancel = false;
                                if(isset($classDetails->start_date) && isset($classDetails->start_time)) {
                                    $startDateTime = \Carbon\Carbon::parse($classDetails->start_date . ' ' . $classDetails->start_time);
                                    $canCancel = $startDateTime->copy()->subHours(24)->isFuture();
                                }
                            @endphp

                            <div class="purchase-item-container" data-category="class" data-searchable="{{ $searchableText }}">
                                <div class="purchase-card" onclick="toggleCard(this, event)">
                                    <div class="card-trigger-header">
                                        <div class="header-left-content">
                                            <div class="package-title">{{ $classDetails->class_name ?? 'Class Plan' }}</div>
                                            <div class="meta-date">
                                                <span>{{ isset($classDetails->start_date) ? \Carbon\Carbon::parse($classDetails->start_date)->format('d M Y') : $class->created_at->format('d M Y') }}</span>
                                                @if($instructorNames)
                                                    <span class="mx-1 text-muted">•</span>
                                                    <span class="text-secondary">{{ $instructorNames }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="header-right-status">
                                            @if(in_array($class->status, ['confirmed', 'approved']))
                                                <span class="badge-status approved">Joined</span>
                                            @elseif(in_array($class->status, ['cancelled', 'rejected']))
                                                <span class="badge-status rejected">Cancelled</span>
                                            @elseif($class->status === 'waitlisted')
                                                <span class="badge-status pending">Waitlisted</span>
                                            @endif
                                            <i class="bi bi-chevron-down chevron-icon ms-2"></i>
                                        </div>
                                    </div>

                                    <div class="card-details-collapsible">
                                        <div class="details-body">
                                            <div class="info-grid">
                                                <div class="info-block">
                                                    <div class="label">Class Name</div>
                                                    <div class="value text-dark">{{ $classDetails->class_name ?? '-' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Instructor</div>
                                                    <div class="value">{{ collect($instructors)->pluck('user.name')->implode(' | ') ?: '-' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">Start Time</div>
                                                    <div class="value text-success">{{ isset($classDetails->start_date) ? \Carbon\Carbon::parse($classDetails->start_date . ' ' . $classDetails->start_time)->format('d M Y, h:i A') : '-' }}</div>
                                                </div>
                                                <div class="info-block">
                                                    <div class="label">End Time</div>
                                                    <div class="value text-danger">{{ isset($classDetails->end_date) ? \Carbon\Carbon::parse($classDetails->end_date . ' ' . $classDetails->end_time)->format('d M Y, h:i A') : '-' }}</div>
                                                </div>
                                            </div>

                                            {{-- Clean Re-join UI --}}
                                            @if(in_array($class->status, ['cancelled', 'rejected']))
                                                <div class="rejection-reason-box mt-3 mb-0">
                                                    <div class="rejection-label">{{ $class->status === 'cancelled' ? 'Cancellation Reason' : 'Reason Notes' }}</div>
                                                    <div class="value text-dark fw-medium" style="font-size: 12.5px; margin-bottom: {{ $class->status === 'cancelled' ? '12px' : '0' }};">
                                                        {{ $class->cancellation_reason ?? 'No reason provided.' }}
                                                    </div>
                                                    
                                                    @if($class->status === 'cancelled')
                                                        <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center" style="border-color: rgba(190, 150, 118, 0.3) !important;">
                                                            <span style="font-size: 12px; color: var(--soma-primary);"><i class="bi bi-info-circle me-1"></i> Changed your mind?</span>
                                                            <a href="{{ route('join.class', $class->selected_class_id) }}" class="btn btn-sm btn-outline-custom-success py-1 px-3" onclick="return confirm('Are you sure you want to re-join this class?');">
                                                                <i class="bi bi-arrow-repeat me-1"></i> Re-join
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- CANCEL BUTTON (Show if Confirmed or Waitlisted) --}}
                                            @if(in_array($class->status, ['confirmed', 'waitlisted']))
                                                <hr style="border-color: rgba(141, 126, 113, 0.2); margin: 16px 0 12px 0;">
                                                @if($canCancel)
                                                    <!-- Fixed Modal Target ID to use $class->id -->
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#cancelModal_{{ $class->id }}" style="color: #dc3545; border-color: #dc3545;">
                                                        <i class="bi bi-x-circle me-1"></i> Cancel Booking
                                                    </button>
                                                @else
                                                    <div class="text-center fw-medium" style="color: var(--soma-secondary); font-size: 11.5px; background: rgba(141, 126, 113, 0.05); padding: 10px; border-radius: 6px; border: 1px dashed rgba(141, 126, 113, 0.3);">
                                                        <i class="bi bi-exclamation-circle me-1" style="color: var(--soma-primary);"></i> 
                                                        Cancellation is only allowed 24 hours prior to class start.
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="purchase-empty-full-width">
                <div class="empty-state text-center py-4">
                    <div class="fs-2 mb-2">🎁</div>
                    <div class="fw-bold" style="color: var(--soma-secondary);">No class bookings found</div>
                </div>
            </div>
        @endif

        {{-- Modals --}}
     @foreach($classes as $class)
    <!-- Fixed Modal ID to use $class->id to match target -->
    <div class="modal fade" id="cancelModal_{{ $class->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Note: Action url အဟောင်းအတိုင်း (selected_class_id) ပဲထားပေးထားပါတယ်။ Backend က အဲ့ဒါလိုလို့ထင်လို့ပါ။ -->
            <form action="{{ route('remove.class', $class->selected_class_id) }}" method="POST" class="modal-content" style="border-color: var(--soma-primary); font-family: 'Fahkwang', sans-serif;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                
                <!-- Applied var(--soma-secondary) to the header -->
                <div class="modal-header text-white" style="background-color: var(--soma-secondary);">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-ban me-2"></i> Cancel Transaction
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <label class="fw-bold mb-2" style="color: var(--soma-secondary);">Reason for Cancellation <span style="color: var(--soma-primary);">*</span></label>
                    <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Please provide a reason for cancellation." style="border-color: rgba(190, 150, 118, 0.4);"></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #cbd5e1; border: none; color: var(--soma-secondary);">Cancel</button>
                    <!-- Applied var(--soma-secondary) to the submit button -->
                    <button type="submit" class="btn text-white" style="background-color: var(--soma-secondary);">Submit Cancellation</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

       @if (method_exists($classes, 'hasPages') && $classes->hasPages())
             @include('partials.pagination', ['paginator' => $classes])
       @endif
    </div>
@endif

<script>
function toggleCard(cardElement, event) {
    if (!cardElement) return;

    if (event && event.target.closest('.card-details-collapsible, button, a, input, select, textarea')) {
        return;
    }

    const isExpanded = cardElement.classList.contains('is-expanded');

    document.querySelectorAll('.purchase-card.is-expanded').forEach(openCard => {
        if (openCard !== cardElement) {
            openCard.classList.remove('is-expanded');
        }
    });

    cardElement.classList.toggle('is-expanded', !isExpanded);
}
</script>