@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container py-5">
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-primary text-white py-3 mb-2">
            <h5 class="fw-bold mb-0">Purchase Package For {{ $user->name ?? 'User' }}</h5>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Validation Error Message --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-body p-4">
            <form action="{{ route('buy.package') }}" method="POST">
                @csrf
                
                {{-- Pass the target User ID to the backend --}}
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="user_discount" value="{{ $user->discount }}">

                <div class="row g-3">

                    {{-- 1. CHOOSE PACKAGE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Choose Package <span class="text-danger">*</span></label>
                        <select name="package_id" id="package_select" class="form-select @error('package_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('package_id') ? '' : 'selected' }}>Choose Package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}" 
                                        data-price="{{ $package->price }}" 
                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PACKAGE PRICE (Original / Base Price) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Base Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="price_input" value="{{ old('price') }}" class="form-control" placeholder="0.00" readonly>
                    </div>

                    {{-- 2. CHOOSE PAYMENT --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Choose Payment <span class="text-danger">*</span></label>
                        <select name="payment_id" id="payment_select" class="form-select @error('payment_id') is-invalid @enderror" {{ old('package_id') ? '' : 'disabled' }} required>
                            <option value="" disabled {{ old('payment_id') ? '' : 'selected' }}>Choose Payment</option>
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->name }}" 
                                        data-receiver="{{ $payment->name ?? $payment->receiver_name }}" 
                                        data-account="{{ $payment->account_info ?? $payment->account_no }}" 
                                        {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                    {{ $payment->method }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- RECEIVER NAME (Auto-populated & Readonly) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Receiver Name <span class="text-danger">*</span></label>
                        <input type="text" id="receiver_name_input" value="{{ old('receiver_name') }}" name="receiver_name" class="form-control" placeholder="Receiver Name" readonly>
                    </div>

                    {{-- ACCOUNT NO. (Auto-populated & Readonly) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Account No. <span class="text-danger">*</span></label>
                        <input type="text" id="account_no_input" value="{{ old('account_info') }}" name="account_info" class="form-control" placeholder="Account No." readonly>
                    </div>

                    {{-- CLIENT ACCOUNT NAME --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Client Account Name <span class="text-danger">*</span></label>
                        <input type="text" value="{{ old('account_name') }}" name="account_name" class="form-control dynamic-field @error('account_name') is-invalid @enderror" placeholder="Client Account Name" {{ old('payment_id') ? '' : 'disabled' }} required>
                        @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CLIENT PHONE NUMBER --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Client Phone Number <span class="text-danger">*</span></label>
                        <input type="number" value="{{ old('phone_no') }}" name="phone_no" class="form-control dynamic-field @error('phone_no') is-invalid @enderror" placeholder="Client Phone Number" {{ old('payment_id') ? '' : 'disabled' }} required>
                        @error('phone_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TRANSACTION NO. --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Transaction No. <span class="text-danger">*</span></label>
                        <input type="text" value="{{ old('transaction_no') }}" name="transaction_no" class="form-control dynamic-field @error('transaction_no') is-invalid @enderror" placeholder="Enter Transaction No." {{ old('payment_id') ? '' : 'disabled' }} required>
                        @error('transaction_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- USER DISCOUNT ELIGIBLE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold d-block">User Discount Eligible</label>
                        <span class="badge {{ $user->discount ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $user->discount ? 'Yes (' . $user->discount . '% Active)' : 'No' }}
                        </span>
                    </div>

                    {{-- COIN USED INPUT FIELD --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Coin Used</label>
                        <input type="number" 
                               min="0" 
                               max="{{ $user->coins ?? $user->coin ?? 0 }}" 
                               value="{{ old('coins_used', 0) }}" 
                               name="coin_used" 
                               id="coins_used_input" 
                               class="form-control dynamic-field @error('coins_used') is-invalid @enderror" 
                               placeholder="0" 
                               {{ old('payment_id') ? '' : 'disabled' }}>
                        <div class="form-text mt-1 fw-semibold text-muted">
                            Available Coins: <span id="available_coins" class="text-primary">{{ $user->coins ?? $user->coin ?? 0 }}</span>
                        </div>
                        @error('coins_used')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- FINAL REMAINING PRICE PREVIEW --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-success">Final Payable Price</label>
                        <input type="text" name="price" id="final_price_input" class="form-control bg-light fw-bold text-success" placeholder="0.00" readonly>
                    </div>

                </div>

                <div class="mt-4 text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" id="submit_btn" class="btn btn-primary px-4" {{ old('payment_id') ? '' : 'disabled' }}>
                        Purchase Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Dynamic Control Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const discountPercent = {{ (float) ($user->discount ?? 0) }};
    const hasDiscount = discountPercent > 0;
    const discountRate = discountPercent / 100;
    const maxAvailableCoins = {{ (float) ($user->coins ?? $user->coin ?? 0) }};

    const packageSelect = document.getElementById('package_select');
    const priceInput = document.getElementById('price_input');
    const coinsUsedInput = document.getElementById('coins_used_input');
    const finalPriceInput = document.getElementById('final_price_input');
    
    const paymentSelect = document.getElementById('payment_select');
    const receiverInput = document.getElementById('receiver_name_input');
    const accountInput = document.getElementById('account_no_input');
    
    const dynamicFields = document.querySelectorAll('.dynamic-field');
    const submitBtn = document.getElementById('submit_btn');

    // Calculate final price taking into account package price, discount, and coins used
    function calculatePrices() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        
        if (!packageSelect.value) {
            priceInput.value = '';
            finalPriceInput.value = '';
            return;
        }

        let originalPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        let discountedPrice = hasDiscount ? (originalPrice - (originalPrice * discountRate)) : originalPrice;
        
        // Show the base price (after % discount if applicable)
        priceInput.value = discountedPrice.toFixed(2);

        // Handle Coin deduction bounds
        let coinsEntered = parseFloat(coinsUsedInput.value) || 0;

        // Cap 1: Cannot enter less than 0
        if (coinsEntered < 0) {
            coinsEntered = 0;
            coinsUsedInput.value = 0;
        }

        // Cap 2: Cannot enter more than user's total available coins
        if (coinsEntered > maxAvailableCoins) {
            coinsEntered = maxAvailableCoins;
            coinsUsedInput.value = maxAvailableCoins;
        }

        // Cap 3: Cannot enter more coins than the discounted price itself
        if (coinsEntered > discountedPrice) {
            coinsEntered = discountedPrice;
            coinsUsedInput.value = discountedPrice;
        }

        let finalPrice = discountedPrice - coinsEntered;
        finalPriceInput.value = finalPrice.toFixed(2);
    }

    // Helper: Update Payment info fields
    function updatePaymentDetails() {
        const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
        if (paymentSelect.value) {
            receiverInput.value = selectedOption.getAttribute('data-receiver') || '';
            accountInput.value = selectedOption.getAttribute('data-account') || '';
        } else {
            receiverInput.value = '';
            accountInput.value = '';
        }
    }

    // Step 1: Package Selection Change
    packageSelect.addEventListener('change', function () {
        if (this.value) {
            calculatePrices();
            paymentSelect.disabled = false;
        } else {
            resetPaymentAndInputs();
        }
    });

    // Step 2: Payment Selection Change
    paymentSelect.addEventListener('change', function () {
        if (this.value) {
            updatePaymentDetails();
            dynamicFields.forEach(field => field.disabled = false);
            submitBtn.disabled = false;
        } else {
            disableRemainingInputs();
        }
    });

    // Step 3: Coin Input Change/Typing
    coinsUsedInput.addEventListener('input', calculatePrices);

    function resetPaymentAndInputs() {
        priceInput.value = '';
        finalPriceInput.value = '';
        coinsUsedInput.value = 0;
        paymentSelect.selectedIndex = 0;
        paymentSelect.disabled = true;
        disableRemainingInputs();
    }

    function disableRemainingInputs() {
        receiverInput.value = '';
        accountInput.value = '';
        dynamicFields.forEach(field => {
            if (field !== coinsUsedInput) field.value = '';
            field.disabled = true;
        });
        submitBtn.disabled = true;
    }

    // Restore state if returning with old inputs (e.g., after a validation error)
    if (packageSelect.value) {
        calculatePrices();
    }
    if (paymentSelect.value) {
        updatePaymentDetails();
    }
});
</script>