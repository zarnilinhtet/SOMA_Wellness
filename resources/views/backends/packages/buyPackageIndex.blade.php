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
                
                {{-- Hidden Input Values --}}
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="user_discount" id="user_discount_input" value="0">

                <div class="row g-3">

                    {{-- 1. CHOOSE PACKAGE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Choose Package <span class="text-danger">*</span></label>
                        <select name="package_id" id="package_select" class="form-select @error('package_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('package_id') ? '' : 'selected' }}>Choose Package</option>
                            
                            @foreach ($packages as $package)
                                @php
                                    // Safe lookup for userPackageDiscount per package
                                    $discountAmt = 0;
                                    $nowStr = date('Y-m-d H:i:s');

                                    if (isset($userPackageDiscounts)) {
                                        foreach ($userPackageDiscounts as $d) {
                                            $pId = is_array($d) ? ($d['package_id'] ?? null) : ($d->package_id ?? null);
                                            
                                            if ((int)$pId === (int)$package->id) {
                                                $expDate = is_array($d) ? ($d['expiration_date'] ?? '') : ($d->expiration_date ?? '');
                                                $expTime = is_array($d) ? ($d['expiration_time'] ?? '23:59:59') : ($d->expiration_time ?? '23:59:59');
                                                
                                                $expDateTime = trim($expDate . ' ' . $expTime);
                                                
                                                // Verify expiration boundary
                                                if (strtotime($nowStr) <= strtotime($expDateTime)) {
                                                    $discountAmt = (float) (is_array($d) ? ($d['discount_amount'] ?? 0) : ($d->discount_amount ?? 0));
                                                }
                                                break;
                                            }
                                        }
                                    }
                                @endphp

                                <option value="{{ $package->id }}" 
                                        data-price="{{ $package->price }}" 
                                        data-discount="{{ $discountAmt }}"
                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} 
                                    @if($discountAmt > 0)
                                        ({{ $discountAmt }}% Off Special Discount)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- BASE PRICE (Remains constant for original price) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Base Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="price_input" value="{{ old('price') }}" class="form-control" placeholder="0.00" readonly>
                    </div>

                    {{-- 2. CHOOSE PAYMENT --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Choose Payment <span class="text-danger">*</span></label>
                        <select name="payment_id" id="payment_select" class="form-select @error('payment_id') is-invalid @enderror" {{ old('package_id') ? '' : 'disabled' }} required>
                            <option value="" disabled {{ old('payment_id') ? '' : 'selected' }}>Choose Payment</option>
                            
                            {{-- Explicit Cash Option --}}
                            <option value="Cash" {{ old('payment_id') == 'Cash' ? 'selected' : '' }}>Cash</option>

                            @foreach ($payments as $payment)
                                <option value="{{ $payment->method }}" 
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

                    {{-- RECEIVER NAME (Editable when Cash selected) --}}
                    <div class="col-md-6 mb-3" id="receiver_wrapper">
                        <label class="form-label fw-bold">Receiver Name <span class="text-danger">*</span></label>
                        <input type="text" id="receiver_name_input" value="{{ old('receiver_name') }}" name="receiver_name" class="form-control" placeholder="Receiver Name" readonly>
                    </div>

                    {{-- ACCOUNT NO. (Hidden when Cash selected) --}}
                    <div class="col-md-6 mb-3" id="account_no_wrapper">
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

                    {{-- TRANSACTION NO. (Hidden when Cash selected) --}}
                    <div class="col-md-6 mb-3" id="transaction_no_wrapper">
                        <label class="form-label fw-bold">Transaction No. <span class="text-danger">*</span></label>
                        <input type="text" id="transaction_no_input" value="{{ old('transaction_no') }}" name="transaction_no" class="form-control dynamic-field @error('transaction_no') is-invalid @enderror" placeholder="Enter Transaction No." {{ old('payment_id') ? '' : 'disabled' }} required>
                        @error('transaction_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- USER DISCOUNT ELIGIBLE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold d-block">Package Discount Applied</label>
                        <span id="discount_badge" class="badge bg-secondary fs-6">
                            None Applied
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
    const maxAvailableCoins = {{ (float) ($user->coins ?? $user->coin ?? 0) }};

    const packageSelect = document.getElementById('package_select');
    const priceInput = document.getElementById('price_input');
    const coinsUsedInput = document.getElementById('coins_used_input');
    const finalPriceInput = document.getElementById('final_price_input');
    
    const paymentSelect = document.getElementById('payment_select');
    const receiverInput = document.getElementById('receiver_name_input');
    const accountInput = document.getElementById('account_no_input');
    const transactionInput = document.getElementById('transaction_no_input');

    const accountWrapper = document.getElementById('account_no_wrapper');
    const transactionWrapper = document.getElementById('transaction_no_wrapper');
    
    const discountBadge = document.getElementById('discount_badge');
    const userDiscountInput = document.getElementById('user_discount_input');

    const dynamicFields = document.querySelectorAll('.dynamic-field');
    const submitBtn = document.getElementById('submit_btn');

    // Calculate prices (Base Price, Discount, Coins, Final Price)
    function calculatePrices() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        
        if (!packageSelect.value) {
            priceInput.value = '';
            finalPriceInput.value = '';
            discountBadge.className = 'badge bg-secondary fs-6';
            discountBadge.textContent = 'None Applied';
            userDiscountInput.value = 0;
            return;
        }

        let originalPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        let discountPercent = parseFloat(selectedOption.getAttribute('data-discount')) || 0;

        // 1. Base Price display stays unchanged (Original Price)
        priceInput.value = originalPrice.toFixed(2);

        // 2. Update Badge & Form Value
        userDiscountInput.value = discountPercent;
        if (discountPercent > 0) {
            discountBadge.className = 'badge bg-success fs-6';
            discountBadge.textContent = 'Yes (' + discountPercent + '% Active)';
        } else {
            discountBadge.className = 'badge bg-secondary fs-6';
            discountBadge.textContent = 'No Discount';
        }

        // 3. Discount Amount calculation
        let discountRate = discountPercent / 100;
        let discountedPrice = discountPercent > 0 ? (originalPrice - (originalPrice * discountRate)) : originalPrice;

        // 4. Coin validation & limits
        let coinsEntered = parseFloat(coinsUsedInput.value) || 0;

        if (coinsEntered < 0) {
            coinsEntered = 0;
            coinsUsedInput.value = 0;
        }

        if (coinsEntered > maxAvailableCoins) {
            coinsEntered = maxAvailableCoins;
            coinsUsedInput.value = maxAvailableCoins;
        }

        if (coinsEntered > discountedPrice) {
            coinsEntered = discountedPrice;
            coinsUsedInput.value = discountedPrice;
        }

        // 5. Final Payable Calculation
        let finalPrice = discountedPrice - coinsEntered;
        finalPriceInput.value = finalPrice.toFixed(2);
    }

    // Toggle Payment Method view (Cash vs Online Methods)
    function updatePaymentDetails() {
        const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
        const selectedValue = paymentSelect.value ? paymentSelect.value.trim().toLowerCase() : '';
        const selectedText = selectedOption ? selectedOption.text.trim().toLowerCase() : '';

        const isCash = selectedValue === 'cash' || selectedText === 'cash';

        if (isCash) {
            receiverInput.readOnly = false;
            receiverInput.placeholder = "Enter Receiver Name";
            
            accountWrapper.style.display = 'none';
            transactionWrapper.style.display = 'none';

            accountInput.removeAttribute('required');
            transactionInput.removeAttribute('required');

            accountInput.value = '';
            transactionInput.value = '';
        } else if (paymentSelect.value) {
            receiverInput.readOnly = true;
            receiverInput.placeholder = "Receiver Name";
            receiverInput.value = selectedOption.getAttribute('data-receiver') || '';
            accountInput.value = selectedOption.getAttribute('data-account') || '';

            accountWrapper.style.display = '';
            transactionWrapper.style.display = '';

            accountInput.setAttribute('required', 'required');
            transactionInput.setAttribute('required', 'required');
        } else {
            resetPaymentFields();
        }
    }

    function resetPaymentFields() {
        receiverInput.value = '';
        receiverInput.readOnly = true;
        accountInput.value = '';
        transactionInput.value = '';

        accountWrapper.style.display = '';
        transactionWrapper.style.display = '';

        accountInput.setAttribute('required', 'required');
        transactionInput.setAttribute('required', 'required');
    }

    // Dynamic Event Listeners
    packageSelect.addEventListener('change', function () {
        if (this.value) {
            calculatePrices();
            paymentSelect.disabled = false;
        } else {
            resetPaymentAndInputs();
        }
    });

    paymentSelect.addEventListener('change', function () {
        if (this.value) {
            updatePaymentDetails();
            dynamicFields.forEach(field => field.disabled = false);
            submitBtn.disabled = false;
        } else {
            disableRemainingInputs();
        }
    });

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
        resetPaymentFields();
        dynamicFields.forEach(field => {
            if (field !== coinsUsedInput) field.value = '';
            field.disabled = true;
        });
        submitBtn.disabled = true;
    }

    // Initialization on Page Load / Validation Restore
    if (packageSelect.value) {
        calculatePrices();
    }
    if (paymentSelect.value) {
        updatePaymentDetails();
    }
});
</script>