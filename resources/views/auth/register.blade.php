<x-guest-layout>

    <div class="lotus-bg">
        <svg viewBox="0 0 100 100" fill="none" stroke="var(--lotus-color)" stroke-width="0.8">
            <path d="M50 20 Q65 55 50 90 Q35 55 50 20" />
            <path d="M50 90 Q30 65 20 45 Q40 45 50 90" />
            <path d="M50 90 Q70 65 80 45 Q60 45 50 90" />
            <path d="M50 90 Q15 75 5 60 Q30 55 50 90" />
            <path d="M50 90 Q85 75 95 60 Q70 55 50 90" />
            <path d="M50 90 Q10 90 5 80 Q25 75 50 90" />
            <path d="M50 90 Q90 90 95 80 Q75 75 50 90" />
        </svg>
    </div>

    <div class="form-header">
        <h1 class="serif-font">SOMA</h1>
        <p>Soma Wellness studio Yoga • Pilates | Register</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="login-form">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label-custom">{{ __('name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                class="form-control custom-input @error('name') is-invalid @enderror" placeholder="full name" required
                autofocus autocomplete="name">

            @error('name')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>


        <div class="form-group">
            <label for="age" class="form-label-custom">{{ __('age') }}</label>
            <input id="age" type="date" name="age" value="{{ old('age') }}"
                class="form-control custom-input @error('age') is-invalid @enderror" placeholder="age" required
                autofocus autocomplete="age">

            @error('age')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone" class="form-label-custom">{{ __('phone') }}</label>
            <input id="phone" type="phone" name="phone" value="{{ old('phone') }}"
                class="form-control custom-input @error('phone') is-invalid @enderror" placeholder="phone" required
                autocomplete="username">

            @error('phone')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label-custom">{{ __('password') }}</label>
            <input id="password" type="password" name="password"
                class="form-control custom-input @error('password') is-invalid @enderror" placeholder="create password"
                required autocomplete="new-password">

            @error('password')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="password_confirmation" class="form-label-custom">{{ __('confirm password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="form-control custom-input @error('password_confirmation') is-invalid @enderror"
                placeholder="repeat password" required autocomplete="new-password">

            @error('password_confirmation')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-brown shadow-sm w-100 mb-3">
            {{ __('Register') }}
        </button>
    </form>

    <div class="d-flex flex-column align-items-center text-center mt-3">
        <span class="text-muted small fw-medium mb-2"
            style="letter-spacing: 1px; color: rgba(90, 77, 66, 0.6) !important;">OR</span>

        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none"
            style="color: #67574b; font-size: 0.95rem;">
            Already registered? Log in
        </a>
    </div>

    <div class="footer-text text-center mt-5">
        powered by SOMA.
    </div>

</x-guest-layout>