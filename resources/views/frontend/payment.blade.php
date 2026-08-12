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
            --glass-bg: rgba(255, 255, 255, 0.65);
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
            padding: 20px 15px;
        }

        @media (min-width: 768px) {
            .payment-wrapper {
                padding: 40px 20px;
            }
        }

        /* Master Glass Layout Dashboard Container */
        .glass-dashboard {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 40px rgba(110, 92, 82, 0.06);
            overflow: hidden;
        }

        /* Left Sidebar: Sticky & Scroll-Optimized */
        .gateway-sidebar {
            background: rgba(255, 255, 255, 0.4);
            border-right: 1px solid rgba(110, 92, 82, 0.08);
            padding: 20px;
        }

        @media (min-width: 992px) {
            .gateway-sidebar {
                padding: 30px;
            }
        }

        .search-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        .search-input {
            width: 100%;
            height: 48px;
            border-radius: 14px;
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
            max-height: 280px;
            overflow-y: auto;
            padding-right: 4px;
        }

        @media (min-width: 992px) {
            .gateway-list {
                max-height: 520px;
            }
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
            padding: 14px 16px;
            background: var(--white);
            border: 1px solid rgba(224, 212, 202, 0.5);
            border-radius: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .compact-method-row:hover {
            transform: translateY(-2px);
            border-color: var(--beige);
            box-shadow: 0 8px 20px rgba(190, 150, 118, 0.1);
        }

        .compact-method-row.active {
            background: linear-gradient(90deg, #fffcf7, #fff6ee);
            border-color: var(--beige);
            box-shadow: 0 6px 18px rgba(190, 150, 118, 0.14);
        }

        .row-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f7f3ee;
            color: var(--beige);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.25s;
        }

        .compact-method-row.active .row-icon-box {
            background: var(--beige);
            color: var(--white);
        }

        .row-title {
            font-size: 14px;
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
            padding: 20px;
        }

        @media (min-width: 992px) {
            .workspace-panel {
                padding: 40px;
            }
        }

        .premium-input {
            width: 100%;
            height: 50px;
            border-radius: 12px;
            border: 1px solid rgba(110, 92, 82, 0.18);
            padding: 10px 14px;
            font-size: 14px;
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
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 15px 35px rgba(45, 42, 38, 0.18);
            position: relative;
        }

        @media (min-width: 768px) {
            .digital-receipt-card {
                border-radius: 26px;
                padding: 30px;
            }
        }

        .qr-display-container {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 12px;
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
            width: 130px;
            height: 130px;
            object-fit: contain;
            border-radius: 10px;
            background: #ffffff;
            padding: 6px;
            pointer-events: none;
        }

        /* Security Watermark Overlay on QR */
        .qr-watermark-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            cursor: zoom-in;
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
            height: 52px;
            border-radius: 12px;
            color: #ffffff !important;
            font-size: 15px;
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
            bottom: 20px;
            right: 20px;
            left: 20px;
            margin: auto;
            max-width: 300px;
            text-align: center;
            background: var(--dark);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 13px;
            z-index: 1050;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: none;
        }

        @media (min-width: 576px) {
            .toast-copy-alert {
                left: auto;
                bottom: 25px;
                right: 25px;
            }
        }

        /* Custom Modern Modal Styles */
        .policy-modal-content {
            border-radius: 20px;
            border: none;
            background: var(--soma-cream);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .policy-modal-header {
            border-bottom: 1px solid rgba(110, 92, 82, 0.1);
            padding: 20px 24px;
        }

        .policy-modal-body {
            padding: 24px;
            max-height: 350px;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.6;
            color: #555555;
        }

        /* Responsive Breakpoints Rules Configuration */
        @media (max-width: 991px) {
            .gateway-sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(110, 92, 82, 0.1);
            }
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
    </style>

    <div class="payment-wrapper">
        <div class="mb-4 animate__animated animate__fadeInDown">
            <div class="mb-2 back-btn" onclick="window.history.back();">
                <i class="fa-solid fa-angle-left"></i> Back
            </div>
            <h1 class="h3 h2-md fw-bold text-dark m-0">
                Select Payment Method for
                <span class="fw-bold" style="color: var(--soma-taupe)">"{{ $package->name }}"</span>
            </h1>
            <p class="text-secondary small mt-1 mb-0">
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
                        {{-- 1. Manual Static Cash Option --}}
                        <div class="compact-method-row active" data-name="Cash Payment" onclick='selectMethodItem(
                                                    this,
                                                    "Cash Payment",
                                                    "Over-the-Counter / Office Desk",
                                                    "Pay In-Person with Cash",
                                                    "fa-solid fa-money-bill-wave",
                                                    ""
                                                )'>
                            <div class="d-flex align-items-center gap-3">
                                <div class="row-icon-box">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>
                                <div class="row-title">
                                    Cash Payment
                                </div>
                            </div>
                            <div class="row-indicator-dot"></div>
                        </div>

                        {{-- 2. Dynamic Database Payments --}}
                        @foreach ($payments as $payment)
                            <div class="compact-method-row" data-name="{{ $payment->method }}" onclick='selectMethodItem(
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
                    {{-- Flex-column-reverse renders Digital Receipt on TOP on mobile viewports (<768px) --}} <div
                        class="row g-4 flex-column-reverse flex-md-row">

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
                                <input type="hidden" id="selectedMethod" name="gateway_method" value="Cash Payment">
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
                                        placeholder="Account holder name">
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
                                        placeholder="09xxxxxxxx">
                                    @error('sender_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Receiver Name Field (Shown ONLY when Cash Payment is selected) --}}
                                <div class="mb-3" id="receiver_wrapper">
                                    <label class="form-label text-secondary small fw-semibold mb-1"
                                        for="receiver_name_input">
                                        Receiver Name <span class="text-danger fs-6">*</span>
                                    </label>
                                    <input type="text" id="receiver_name_input" name="receiver_name"
                                        class="form-control premium-input @error('receiver_name') is-invalid @enderror"
                                        placeholder="Receiver Name">
                                    @error('receiver_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Transaction ID Field (Hidden when Cash Payment is selected) --}}
                                <div class="mb-4" id="txn_wrapper">
                                    <label class="form-label text-secondary small fw-semibold mb-1" for="formTxn">
                                        Transaction Proof Code <span class="text-danger fs-6 req-star">*</span>
                                    </label>
                                    <input type="text" id="formTxn" name="transaction_id"
                                        class="form-control premium-input @error('transaction_id') is-invalid @enderror"
                                        placeholder="Enter Transaction reference ID">
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

                                {{-- Screenshot File Input Field (Hidden when Cash Payment is selected) --}}
                                <div class="mb-4" id="screenshot_wrapper">
                                    <label class="form-label text-secondary small fw-semibold mb-1" for="formScreenshot">
                                        Transaction Proof Screenshot <span class="text-danger fs-6 req-star">*</span>
                                    </label>
                                    <input type="file" id="formScreenshot" name="screenshot" accept="image/*"
                                        class="form-control premium-input @error('screenshot') is-invalid @enderror"
                                        onchange="previewScreenshot(event)">
                                    @error('screenshot')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    {{-- Screenshot Preview Area --}}
                                    <div class="mt-3 text-center">
                                        <img id="screenshotPreview" src="" alt="Proof Preview"
                                            style="max-width: 100%; max-height: 220px; border-radius: 12px; display: none; border: 1px solid #ddd; padding: 5px;">
                                    </div>
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
                                <button type="submit" id="submitTxnBtn" @if(!optional($onboarding)->payment_policy_accepted)
                                disabled @endif
                                    onclick="return confirm('Are you sure you want to complete this transaction?');"
                                    class="btn btn-verify w-100 d-flex align-items-center justify-content-center gap-2">
                                    Complete Process Transaction <i class="fa-solid fa-circle-check"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Digital Receipt Panel --}}
                        <div class="col-md-5">
                            <div class="digital-receipt-card d-flex flex-column justify-content-between h-100">
                                <div>
                                    <div class="border-bottom border-secondary pb-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fs-5" style="color: var(--soma-taupe)" id="recIcon">
                                                    <i class="fa-solid fa-money-bill-wave"></i>
                                                </div>
                                                <h6 class="m-0 fw-bold">Target Gate</h6>
                                            </div>
                                            <div class="text-warning small fw-bold" id="recMethod">
                                                Cash Payment
                                            </div>
                                        </div>

                                        <div class="receipt mt-3" style="font-size: 13px;">
                                            <div class="d-flex justify-content-between mb-1">
                                                <div class="text-white-50">Package Price</div>
                                                <div>{{ number_format($originalPrice) }} K</div>
                                            </div>

                                            @if($isPackageDiscounted)
                                                <div class="d-flex justify-content-between text-success small mb-1">
                                                    <div>Applied Discount ({{ $discountValue }}%)</div>
                                                    <div>- {{ number_format($calculatedDiscount) }} K</div>
                                                </div>
                                            @endif

                                            @if(!empty($redeem))
                                                <div class="d-flex justify-content-between text-warning small mb-1">
                                                    <div>Coins Redeemed</div>
                                                    <div id="recCoinDisplay">- {{ number_format($loyaltyDeduction) }} K</div>
                                                </div>
                                            @endif
                                        </div>

                                        <hr class="my-2 border-secondary" />

                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="fw-semibold">Total</div>
                                            <div class="badge bg-primary text-white fw-bold px-2 py-2" id="recTotalDisplay"
                                                style="font-size: 14px;">
                                                {{ number_format($finalTotal) }} K
                                            </div>
                                        </div>
                                    </div>

                                    {{-- QR Code Section --}}
                                    <div class="mb-3" id="qrSection" style="display: none;">
                                        <span class="text-white-50 small d-block mb-2">Scan Account QR Code:</span>
                                        <div class="qr-display-container" id="qrContainer" data-bs-toggle="modal"
                                            data-bs-target="#qrZoomModal">
                                            <div class="qr-watermark-overlay" oncontextmenu="return false;"></div>

                                            <img id="recQrImage" src="" alt="Payment QR Code" draggable="false"
                                                style="display: none;">

                                            <small class="text-white-50 mt-2 d-block" style="font-size: 11px;">
                                                <i class="fa-solid fa-magnifying-glass-plus me-1 text-warning"></i> Tap to
                                                enlarge
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Recipient Info --}}
                                    <div class="d-flex flex-column gap-2" style="font-size: 13px;">
                                        <div>
                                            <span class="text-white-50 small d-block">Receiver Identity:</span>
                                            <strong id="recName" class="text-white fs-6">
                                                Over-the-Counter / Office Desk
                                            </strong>
                                        </div>
                                        <div>
                                            <span class="text-white-50 small d-block">Channel Destination:</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <strong id="recPhone" class="text-white fs-6">
                                                    Pay In-Person with Cash
                                                </strong>
                                                <i class="fa-regular fa-copy text-white-50 copy-trigger" title="Copy Number"
                                                    style="cursor: pointer;" onclick="copyDestinationNumber()"></i>
                                            </div>
                                        </div>
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
                    <p class="fw-bold text-dark">Please carefully read our standard processing policy before completing your
                        purchase:</p>
                    <ul>
                        <li>All payment requests are processed manually within 10 to 30 minutes under regular processing
                            hours.</li>
                        <li>You must provide a clear and authentic receipt voucher image/screenshot displaying the
                            corresponding global Transaction Reference Identifier Code.</li>
                        <li>Falsified proof or multiple entries mapping a single voucher instance code will trigger
                            immediate automated system account terminal validation locks.</li>
                        <li>Refunds are not permitted once process verification pipeline statuses settle into completed
                            execution branches.</li>
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
@endsection

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

        // Dynamic Icon Updating
        const recIcon = document.getElementById('recIcon');
        if (recIcon) {
            recIcon.innerHTML = `<i class="${iconClass}"></i>`;
        }

        // Handle QR Visibility for Cash vs Online
        const qrSection = document.getElementById('qrSection');
        const recQr = document.getElementById('recQrImage');
        const modalQr = document.getElementById('modalQrImage');

        if (qrPathURL && qrPathURL.trim() !== "" && !qrPathURL.endsWith('/')) {
            if (qrSection) qrSection.style.display = 'block';
            if (recQr) { recQr.src = qrPathURL; recQr.style.display = 'inline-block'; }
            if (modalQr) modalQr.src = qrPathURL;
        } else {
            if (qrSection) qrSection.style.display = 'none';
            if (recQr) { recQr.src = ""; recQr.style.display = 'none'; }
            if (modalQr) modalQr.src = "";
        }

        toggleProofRequirements(name);
    }

    function toggleProofRequirements(methodName) {
        const txnWrapper = document.getElementById('txn_wrapper');
        const screenshotWrapper = document.getElementById('screenshot_wrapper');
        const receiverWrapper = document.getElementById('receiver_wrapper');

        const txnInput = document.getElementById('formTxn');
        const screenshotInput = document.getElementById('formScreenshot');
        const receiverInput = document.getElementById('receiver_name_input');

        const isCash = (methodName === 'Cash Payment');

        if (isCash) {
            // 1. Hide Transaction Code & Screenshot inputs
            if (txnWrapper) txnWrapper.style.display = 'none';
            if (screenshotWrapper) screenshotWrapper.style.display = 'none';
            if (txnInput) txnInput.removeAttribute('required');
            if (screenshotInput) screenshotInput.removeAttribute('required');

            // 2. Show Receiver Name input
            if (receiverWrapper) receiverWrapper.style.display = 'block';
            if (receiverInput) receiverInput.setAttribute('required', 'required');
        } else {
            // 1. Show Transaction Code & Screenshot inputs
            if (txnWrapper) txnWrapper.style.display = 'block';
            if (screenshotWrapper) screenshotWrapper.style.display = 'block';
            if (txnInput) txnInput.setAttribute('required', 'required');
            if (screenshotInput) screenshotInput.setAttribute('required', 'required');

            // 2. Hide Receiver Name input
            if (receiverWrapper) receiverWrapper.style.display = 'none';
            if (receiverInput) receiverInput.removeAttribute('required');
        }
    }

    function previewScreenshot(event) {
        const input = event.target;
        const preview = document.getElementById('screenshotPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }

    function copyDestinationNumber() {
        const recPhone = document.getElementById('recPhone');
        if (!recPhone) return;

        const textToCopy = recPhone.innerText.trim();
        navigator.clipboard.writeText(textToCopy).then(() => {
            const toast = document.getElementById('copyToast');
            if (toast) {
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 2500);
            }
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
</script>