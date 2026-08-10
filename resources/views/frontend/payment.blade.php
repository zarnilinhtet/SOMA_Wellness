@extends('layouts.link')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --soma-cream: #fbf9f6;
            --soma-taupe: #be9676;
            --beige: #be9676;
            --dark: #2d2a26;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.45);
            --glass-border: rgba(110, 92, 82, 0.12);
        }

        body {
            background: linear-gradient(135deg, var(--soma-cream) 0%, #ffffff 100%);
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
            min-height: 100vh;
            color: var(--dark);
        }

        .payment-wrapper {
            max-width: 1300px;
            margin: auto;
            padding: 40px 20px;
        }

        /* Master Glass Layout Dashboard Container */
        .glass-dashboard {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(110, 92, 82, 0.06);
            overflow: hidden;
        }

        /* Left Sidebar: Sticky & Scroll-Optimized */
        .gateway-sidebar {
            background: rgba(255, 255, 255, 0.4);
            border-right: 1px solid rgba(110, 92, 82, 0.08);
            padding: 30px;
        }

        .search-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            height: 50px;
            border-radius: 16px;
            border: 1px solid rgba(110, 92, 82, 0.15);
            padding: 0 15px 0 45px;
            font-size: 14px;
            background: var(--white);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--beige);
            box-shadow: 0 0 0 4px rgba(190, 150, 118, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0948d;
            font-size: 14px;
        }

        /* Clean Scrollbar Container Architecture */
        .gateway-list {
            max-height: 520px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .gateway-list::-webkit-scrollbar {
            width: 5px;
        }

        .gateway-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .gateway-list::-webkit-scrollbar-thumb {
            background: rgba(110, 92, 82, 0.2);
            border-radius: 10px;
        }

        /* Interactive Method Rows */
        .compact-method-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            background: var(--white);
            border: 1px solid rgba(224, 212, 202, 0.5);
            border-radius: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .compact-method-row:hover {
            transform: translateY(-2px) translateX(4px);
            border-color: var(--beige);
            box-shadow: 0 8px 20px rgba(190, 150, 118, 0.1);
        }

        .compact-method-row.active {
            background: linear-gradient(90deg, #fffcf7, #fff6ee);
            border-color: var(--beige);
            box-shadow: 0 6px 18px rgba(190, 150, 118, 0.14);
        }

        .row-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #f7f3ee;
            color: var(--beige);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.25s;
        }

        .compact-method-row.active .row-icon-box {
            background: var(--beige);
            color: var(--white);
        }

        .row-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
        }

        .row-indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: transparent;
            border: 2px solid #dfd9d3;
            transition: all 0.25s;
        }

        .compact-method-row.active .row-indicator-dot {
            background: var(--beige);
            border-color: var(--beige);
            transform: scale(1.2);
        }

        /* Right Panel Workspaces */
        .workspace-panel {
            padding: 40px;
        }

        .premium-input {
            width: 100%;
            height: 54px;
            border-radius: 14px;
            border: 1px solid rgba(110, 92, 82, 0.18);
            padding: 12px 16px;
            font-size: 15px;
            background: var(--white);
            transition: all 0.3s;
        }

        .premium-input:focus {
            outline: none;
            border-color: var(--beige);
            box-shadow: 0 0 0 4px rgba(190, 150, 118, 0.12);
        }

        /* Premium Digital Split Receipt & QR System Frame */
        .digital-receipt-card {
            background: linear-gradient(145deg, #24211e, #36322e);
            color: var(--white);
            border-radius: 26px;
            padding: 30px;
            box-shadow: 0 25px 50px rgba(45, 42, 38, 0.22);
            position: relative;
        }

        .qr-display-container {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            user-select: none;
            -webkit-user-select: none;
            transition: all 0.3s ease;
        }
        
        .qr-display-container:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--beige);
        }

        .qr-display-container img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            border-radius: 10px;
            background: white;
            padding: 6px;
            pointer-events: none; /* Disables dragging, saving & context menu interaction */
        }

        /* Security Watermark Overlay on QR */
        .qr-watermark-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            cursor: zoom-in; /* Changed to zoom-in cursor */
        }

        .copy-trigger {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .copy-trigger:hover {
            color: var(--beige) !important;
        }

        .btn-verify {
            background: linear-gradient(135deg, var(--beige), #a87e5c);
            border: none;
            height: 56px;
            border-radius: 14px;
            color: white !important;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 20px rgba(190, 150, 118, 0.25);
        }

        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(190, 150, 118, 0.4);
        }

        .btn-verify:disabled {
            background: #cccccc;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #8c8179;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .back-btn:hover {
            color: var(--dark);
            transform: translateX(-2px);
        }

        /* Toast Popup Feedback */
        .toast-copy-alert {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: var(--dark);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: none;
        }

        /* Custom Modern Modal Styles */
        .policy-modal-content {
            border-radius: 24px;
            border: none;
            background: var(--soma-cream);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .policy-modal-header {
            border-bottom: 1px solid rgba(110, 92, 82, 0.1);
            padding: 24px 30px;
        }

        .policy-modal-body {
            padding: 30px;
            max-height: 350px;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.6;
            color: #555;
        }

        .policy-modal-footer {
            border-top: 1px solid rgba(110, 92, 82, 0.1);
            padding: 20px 30px;
            display: flex;
            gap: 12px;
        }

        .btn-accept {
            background: var(--beige);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-accept:hover {
            background: #a87e5c;
            color: white;
        }

        .btn-decline {
            background: #e5e5e5;
            color: #666;
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
        }

        /* Responsive Breakpoints Rules Configuration */
        @media(max-width: 991px) {
            .gateway-sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(110, 92, 82, 0.1);
                padding: 25px;
            }

            .workspace-panel {
                padding: 25px;
            }
        }
    </style>

    <div class="payment-wrapper">
        <div class="mb-4 animate__animated animate__fadeInDown">
            <div class="mb-3 back-btn" onclick="window.history.back();">
                <i class="fa-solid fa-angle-left"></i> Back
            </div>
            <h1 class="h2 fw-bold text-dark m-0">
                Select Payment Method for
                <span class="fw-bold" style="color: var(--soma-beige)">"{{ $package->name }}"</span>
            </h1>
            <p class="text-secondary small mt-1">
                Select a gateway method to access payment channel details and destination QR modules.
            </p>
        </div>

        <div class="glass-dashboard container-fluid p-0 animate__animated animate__fadeInUp">
            <div class="row g-0">
                {{-- Left Column: Gateway Selection Sidebar --}}
                <div class="col-lg-4 gateway-sidebar">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="methodSearch" class="search-input" placeholder="Search targeted gateways..."
                            onkeyup="filterMethods()">
                    </div>

                    <div class="gateway-list" id="gatewayContainer">
                        @foreach ($payments as $payment)
                            <div class="compact-method-row {{ $loop->first ? 'active' : '' }}"
                                data-name="{{ $payment->method }}" onclick='selectMethodItem(
                                                        this,
                                                        "{{ addslashes($payment->method) }}",
                                                        "{{ addslashes($payment->name) }}",
                                                        "{{ addslashes($payment->account_info) }}",
                                                        "fa-solid fa-wallet",
                                                        "{{ asset($payment->image) }}"
                                                    )'>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="row-icon-box">
                                        <i class="fa-solid fa-wallet"></i>
                                    </div>
                                    <div class="row-title">
                                        {{ $payment->method }}
                                    </div>
                                </div>
                                <div class="row-indicator-dot"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: Form Workspace Panel --}}
                <div class="col-lg-8 workspace-panel">
                    <div class="row g-4 flex-column-reverse flex-md-row">

                        {{-- Form Input Section --}}
                        <div class="col-md-7">
                            <form action="{{ route('payment.submit') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                @php
                                    $isPackageDiscounted = false;
                                    $originalPrice = (float) ($package->price ?? 0);
                                    $discountValue = 0; 
                                    $calculatedDiscount = 0; 
                                    $discountExpiration = null;

                                    if (auth()->check() && !empty($discountPackage)) {
                                        $isExpired = false;
                                        if (!empty($discountPackage->expiration_date)) {
                                            $expTime = $discountPackage->expiration_time ?? '23:59:59';
                                            $expiresAt = \Carbon\Carbon::parse("{$discountPackage->expiration_date} {$expTime}");

                                            if ($expiresAt->isPast()) {
                                                $isExpired = true;
                                            } else {
                                                $discountExpiration = $expiresAt;
                                            }
                                        }

                                        if (!$isExpired) {
                                            $rawDiscount = $discountPackage->discount_amount
                                                ?? $discountPackage->discount_percentage
                                                ?? $discountPackage->package->discount_amount
                                                ?? 0;

                                            if ($rawDiscount > 0) {
                                                $isPackageDiscounted = true;
                                                $discountValue = (float) $rawDiscount;
                                                $calculatedDiscount = $originalPrice * ($discountValue / 100);
                                            }
                                        }
                                    }

                                    $discountedPrice = $originalPrice - $calculatedDiscount;
                                    $coinUsed = old('coin_used', auth()->user()->coins ?? 0);
                                    $loyaltyDeduction = (!empty($redeem)) ? (float) $coinUsed : 0;
                                    $finalTotal = max(0, $discountedPrice - $loyaltyDeduction);
                                @endphp

                                {{-- Hidden Form Fields --}}
                                <input type="hidden" name="userDiscount"
                                    value="{{ $isPackageDiscounted ? $discountValue : 0 }}">
                                <input type="hidden" name="redeem" value="{{ $redeem ?? 0 }}">
                                <input type="hidden" id="selectedMethod" name="gateway_method"
                                    value="{{ optional($payments->first())->method ?? 'KBZ Pay' }}">
                                <input type="hidden" name="package" value="{{ $package->id }}">
                                <input type="hidden" name="registered_id" value="{{ Auth::id() }}">
                                <input type="hidden" id="hiddenFormAmount" name="amount" value="{{ $finalTotal }}">

                                {{-- Header --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-uppercase tracking-wider fw-bold mb-0"
                                        style="font-size: 11px; color: var(--beige, #c5a059);">
                                        Verification Form
                                    </h6>
                                    <span class="text-danger" style="font-size: 11px;">* required</span>
                                </div>

                                {{-- Sender Name --}}
                                <div class="mb-3 mt-3">
                                    <label class="form-label text-secondary small fw-semibold mb-1" for="formName">
                                        Your Registered Name <span class="text-danger fs-6">*</span>
                                    </label>
                                    <input type="text" id="formName" name="sender_name" required
                                        class="form-control premium-input @error('sender_name') is-invalid @enderror"
                                        placeholder="Account holder name"
                                        value="{{ old('sender_name', auth()->user()->name ?? '') }}">
                                    @error('sender_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Sender Phone --}}
                                <div class="mb-3">
                                    <label class="form-label text-secondary small fw-semibold mb-1" for="formPhone">
                                        Your Mobile Line <span class="text-danger fs-6">*</span>
                                    </label>
                                    <input type="text" id="formPhone" name="sender_phone" required
                                        class="form-control premium-input @error('sender_phone') is-invalid @enderror"
                                        placeholder="09xxxxxxxx"
                                        value="{{ old('sender_phone', auth()->user()->phone ?? '') }}">
                                    @error('sender_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Transaction ID --}}
                                <div class="mb-4">
                                    <label class="form-label text-secondary small fw-semibold mb-1" for="formTxn">
                                        Transaction Proof Code <span class="text-danger fs-6 req-star">*</span>
                                    </label>
                                    <input type="text" id="formTxn" name="transaction_id" required
                                        class="form-control premium-input @error('transaction_id') is-invalid @enderror"
                                        placeholder="Enter Transaction reference ID" value="{{ old('transaction_id') }}">
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Coin Used Section --}}
                                @if(!empty($redeem))
                                    <div class="mb-4">
                                        <label class="form-label text-secondary small fw-semibold mb-1" for="formCoinUsed">
                                            Coin Used <span class="text-danger fs-6">*</span>
                                        </label>
                                        <input type="number" step="any" id="formCoinUsed" name="coin_used" min="0"
                                            max="{{ auth()->user()->coins ?? 0 }}" required
                                            class="form-control premium-input @error('coin_used') is-invalid @enderror"
                                            placeholder="Loyalty points redeemed" value="{{ $coinUsed }}"
                                            oninput="validateAndSync(this, {{ auth()->user()->coins ?? 0 }})" />

                                        <small class="text-muted d-block mt-1">
                                            Available: <strong>{{ number_format(auth()->user()->coins ?? 0) }}</strong> coins
                                        </small>

                                        @error('coin_used')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                {{-- Screenshot File Input --}}
                                <div class="mb-4">
                                    <label class="form-label text-secondary small fw-semibold mb-1" for="formScreenshot">
                                        Transaction Proof Screenshot <span class="text-danger fs-6 req-star">*</span>
                                    </label>
                                    <input type="file" id="formScreenshot" name="screenshot" accept="image/*" required
                                        class="form-control premium-input @error('screenshot') is-invalid @enderror"
                                        onchange="previewScreenshot(event)">
                                    @error('screenshot')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Screenshot Preview Area --}}
                                <div class="mb-4 text-center">
                                    <img id="screenshotPreview" src="" alt="Proof Preview"
                                        style="max-width: 100%; max-height: 220px; border-radius: 12px; display: none; border: 1px solid #ddd; padding: 5px;">
                                </div>

                                {{-- Policy Warning / Banner --}}
                                @if(optional($onboarding)->payment_policy_accepted)
                                    <div class="mb-4 text-success" id="policyWarning" style="font-size: 13px;">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Payment policy terms accepted. You can proceed with processing.</span>
                                    </div>
                                @else
                                    <div class="mb-4" id="policyWarning" style="font-size: 13px; color: #664d03;">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span>
                                            You must accept our Payment Policy terms to complete this transaction.
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#paymentPolicyModal"
                                                class="fw-bold text-decoration-underline" style="color: var(--beige, #c5a059);">
                                                Review Terms
                                            </a>
                                        </span>
                                    </div>
                                @endif

                                {{-- Submit Button --}}
                                <button type="submit" id="submitTxnBtn" @if(!optional($onboarding)->payment_policy_accepted) disabled @endif
                                    onclick="return confirm('Are you sure you want to complete this transaction?');"
                                    class="btn btn-verify w-100 d-flex align-items-center justify-content-center gap-2">
                                    Complete Process Transaction <i class="fa-solid fa-circle-check"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Digital Receipt Panel --}}
                        <div class="col-md-5">
                            <div class="digital-receipt-card d-flex flex-column justify-content-between">
                                <div>
                                    <div class="border-bottom border-secondary pb-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fs-5" style="color: var(--soma-taupe)" id="recIcon">
                                                    <i class="fa-solid fa-wallet"></i>
                                                </div>
                                                <h6 class="m-0 fw-bold">Target Gate</h6>
                                            </div>
                                            <div class="text-warning small fw-bold" id="recMethod">
                                                {{ $payments->first()->method ?? 'KBZ Pay' }}
                                            </div>
                                        </div>

                                        <div class="receipt mt-2" style="font-size: 14px;">
                                            <div class="d-flex justify-content-between">
                                                <div>Package Price</div>
                                                <div>{{ number_format($originalPrice) }} K</div>
                                            </div>

                                            @if($isPackageDiscounted)
                                                <div class="d-flex justify-content-between text-success small">
                                                    <div>Applied Discount ({{ $discountValue }}%)</div>
                                                    <div>- {{ number_format($calculatedDiscount) }} K</div>
                                                </div>
                                            @endif

                                            @if(!empty($redeem))
                                                <div class="d-flex justify-content-between text-warning small">
                                                    <div>Coins Redeemed</div>
                                                    <div id="recCoinDisplay">- {{ number_format($loyaltyDeduction) }} K</div>
                                                </div>
                                            @endif
                                        </div>

                                        <hr />

                                        <div class="d-flex justify-content-between mt-1">
                                            <div>Total</div>
                                            <div class="badge bg-primary text-white fw-bold" id="recTotalDisplay">
                                                {{ number_format($finalTotal) }} K
                                            </div>
                                        </div>
                                    </div>

                                    {{-- QR Code Section with Anti-Save Protection and Click to Zoom --}}
                                    <div class="mb-3">
                                        <span class="text-white-50 small d-block mb-2">Scan Account QR Code:</span>
                                        <div class="qr-display-container" id="qrContainer" data-bs-toggle="modal" data-bs-target="#qrZoomModal">
                                            {{-- Invisible overlay to block click/drag/save interactions but allow triggering Modal --}}
                                            <div class="qr-watermark-overlay" oncontextmenu="return false;"></div>
                                            
                                            <img id="recQrImage" src="{{ asset($payments->first()->image ?? '') }}"
                                                alt="Payment QR Code" draggable="false">
                                            
                                            <small class="text-white-50 mt-2 d-block" style="font-size: 11px;">
                                                <i class="fa-solid fa-magnifying-glass-plus me-1 text-warning"></i> Click to enlarge
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Recipient Info --}}
                                    <div class="d-flex flex-column gap-2" style="font-size: 13px;">
                                        <div>
                                            <span class="text-white-50 small d-block">Receiver Identity:</span>
                                            <strong id="recName" class="text-white fs-6">
                                                {{ $payments->first()->name ?? 'N/A' }}
                                            </strong>
                                        </div>
                                        <div>
                                            <span class="text-white-50 small d-block">Channel Destination:</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <strong id="recPhone" class="text-white fs-6">
                                                    {{ $payments->first()->account_info ?? 'N/A' }}
                                                </strong>
                                                <i class="fa-regular fa-copy text-white-50 copy-trigger" title="Copy Number"
                                                    style="cursor: pointer;" onclick="copyDestinationNumber()"></i>
                                            </div>
                                        </div>
                                        <div class="my-2" style="border-top: 1px dotted rgba(255,255,255,0.15)"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Policy Modal --}}
    <div class="modal fade" id="paymentPolicyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="paymentPolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content policy-modal-content">
                <div class="policy-modal-header p-3 border-bottom">
                    <h5 class="modal-title fw-bold" id="paymentPolicyModalLabel" style="color: var(--dark);">
                        <i class="fa-solid fa-shield-halved text-warning me-2"></i> Payment Terms & Conditions
                    </h5>
                </div>
                <div class="policy-modal-body p-3">
                    <p class="fw-bold text-dark mb-3">Please carefully read our standard processing policy before completing your purchase:</p>
                    <ul class="mb-0" style="padding-left: 20px;">
                        <li class="mb-2">All payment requests are processed manually within 10 to 30 minutes under regular processing hours.</li>
                        <li class="mb-2">You must provide a clear and authentic receipt voucher image/screenshot displaying the corresponding global Transaction Reference Identifier Code.</li>
                        <li class="mb-2">Falsified proof or multiple entries mapping a single voucher instance code will trigger immediate automated system account terminal validation locks.</li>
                        <li>Refunds are not permitted once process verification pipeline statuses settle into completed execution branches.</li>
                    </ul>
                </div>
                <div class="policy-modal-footer p-3 border-top d-flex gap-2">
                    <button type="button" class="btn btn-decline w-50" data-bs-dismiss="modal"
                        onclick="handlePolicyDecline()">Decline</button>
                    <button type="button" class="btn btn-accept w-50" data-bs-dismiss="modal"
                        onclick="handlePolicyAccept()">Accept Terms</button>
                </div>
            </div>
        </div>
    </div>

    {{-- QR Zoom Modal --}}
    <div class="modal fade" id="qrZoomModal" tabindex="-1" aria-labelledby="qrZoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <img id="modalQrImage" src="{{ asset($payments->first()->image ?? '') }}" alt="Enlarged QR Code" 
                         style="max-width: 100%; max-height: 70vh; border-radius: 16px; background: white; padding: 15px; user-select: none;" 
                         oncontextmenu="return false;" draggable="false">
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="copyToast" class="toast-copy-alert animate__animated animate__fadeInUp" style="display: none;">
        <i class="fa-solid fa-circle-check text-warning me-2"></i> Copied to Clipboard!
    </div>

    {{-- Inline JavaScript --}}
    <script>
        const basePackagePrice = parseFloat("{{ $originalPrice }}") || 0;
        const userDiscountPercentage = parseFloat("{{ $isPackageDiscounted ? $discountValue : 0 }}") || 0;

        @if(!$onboarding || !$onboarding->payment_policy_accepted)
            window.addEventListener('DOMContentLoaded', () => {
                const policyModal = new bootstrap.Modal(document.getElementById('paymentPolicyModal'));
                policyModal.show();
            });
        @endif

        document.addEventListener('DOMContentLoaded', () => {
            const defaultMethod = document.getElementById('selectedMethod').value;
            toggleProofRequirements(defaultMethod);
        });

        // Block drag events globally on the QR image
        document.addEventListener('dragstart', function (e) {
            if (e.target && (e.target.id === 'recQrImage' || e.target.id === 'modalQrImage')) {
                e.preventDefault();
            }
        });

        function validateAndSync(input, maxCoins) {
            let val = input.value.replace(/\D/g, '');
            val = val.replace(/^0+/, '');
            if (val !== '' && Number(val) > maxCoins) {
                val = String(maxCoins);
            }
            input.value = val;
            syncLiveReceipt();
        }

        function syncLiveReceipt() {
            const discountAmount = basePackagePrice * (userDiscountPercentage / 100);
            const discountedPrice = basePackagePrice - discountAmount;
            const coinInput = document.getElementById('formCoinUsed');
            let coinsToDeduct = 0;

            if (coinInput) {
                coinsToDeduct = parseFloat(coinInput.value) || 0;
            }

            const finalTotal = Math.max(0, discountedPrice - coinsToDeduct);
            const recCoinDisplay = document.getElementById('recCoinDisplay');
            const recTotalDisplay = document.getElementById('recTotalDisplay');
            const hiddenAmountInput = document.getElementById('hiddenFormAmount');

            if (recCoinDisplay) {
                recCoinDisplay.innerText = `- ${coinsToDeduct.toLocaleString()} K`;
            }
            if (recTotalDisplay) {
                recTotalDisplay.innerText = `${finalTotal.toLocaleString()} K`;
            }
            if (hiddenAmountInput) {
                hiddenAmountInput.value = finalTotal;
            }
        }

        function handlePolicyAccept() {
            const btn = document.getElementById('submitTxnBtn');
            const warn = document.getElementById('policyWarning');

            if (btn) btn.removeAttribute('disabled');
            if (warn) {
                warn.className = "mb-4 text-success";
                warn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Payment policy terms accepted. You can proceed with processing.</span>';
            }

            fetch("{{ route('policy.save') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ payment_policy_accepted: 1 })
            })
                .then(response => response.json())
                .then(data => console.log("Database updated successfully:", data))
                .catch(error => console.error("Database save failed:", error));
        }

        function handlePolicyDecline() {
            const btn = document.getElementById('submitTxnBtn');
            const warn = document.getElementById('policyWarning');

            if (btn) btn.setAttribute('disabled', 'disabled');
            if (warn) {
                warn.className = "mb-4 text-danger";
                warn.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> <span>Policy declined. Transaction disabled. <a href="#" data-bs-toggle="modal" data-bs-target="#paymentPolicyModal" class="fw-bold text-decoration-underline text-danger">Click here to review again</a></span>';
            }
        }

        function filterMethods() {
            const query = document.getElementById('methodSearch').value.toLowerCase().trim();
            const items = document.querySelectorAll('.compact-method-row');

            items.forEach(item => {
                const name = (item.dataset.name || '').toLowerCase().trim();
                item.style.display = name.includes(query) ? 'flex' : 'none';
            });
        }

        function selectMethodItem(element, name, holder, credential, iconClass, qrPathURL) {
            document.querySelectorAll('.compact-method-row').forEach(row => row.classList.remove('active'));
            element.classList.add('active');

            document.getElementById('selectedMethod').value = name;
            document.getElementById('recMethod').innerText = name;
            document.getElementById('recName').innerText = holder;
            document.getElementById('recPhone').innerText = credential;
            
            // Update Small QR Image
            document.getElementById('recQrImage').src = qrPathURL;
            // Update Large Modal QR Image
            document.getElementById('modalQrImage').src = qrPathURL;
            
            document.getElementById('recIcon').innerHTML = `<i class="${iconClass}"></i>`;

            toggleProofRequirements(name);
        }

        function toggleProofRequirements(methodName) {
            const isCash = methodName.toLowerCase().includes('cash');
            const formTxn = document.getElementById('formTxn');
            const formScreenshot = document.getElementById('formScreenshot');
            const reqStars = document.querySelectorAll('.req-star');

            if (isCash) {
                if(formTxn) formTxn.removeAttribute('required');
                if(formScreenshot) formScreenshot.removeAttribute('required');
                reqStars.forEach(star => star.style.display = 'none');
            } else {
                if(formTxn) formTxn.setAttribute('required', 'required');
                if(formScreenshot) formScreenshot.setAttribute('required', 'required');
                reqStars.forEach(star => star.style.display = 'inline');
            }
        }

        function previewScreenshot(event) {
            const reader = new FileReader();
            const output = document.getElementById('screenshotPreview');
            reader.onload = function () {
                output.src = reader.result;
                output.style.display = 'block';
            };
            if (event.target.files && event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function copyDestinationNumber() {
            const text = document.getElementById('recPhone').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>
@endsection