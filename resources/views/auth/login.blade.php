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
        <p>Soma Wellness studio Yoga • Pilates | Log in</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success rounded-pill py-2 text-center text-sm mb-4 border-0 bg-light text-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        <div class="form-group">
            <label for="phone" class="form-label-custom">{{ __('phone') }}</label>
            <input id="phone"
                   type="phone"
                   name="phone"
                   value="{{ old('phone') }}"
                   class="form-control custom-input @error('phone') is-invalid @enderror"
                   placeholder="phone"
                   required
                   autofocus
                   autocomplete="username">

            @error('phone')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="password" class="form-label-custom">{{ __('password') }}</label>
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control custom-input @error('password') is-invalid @enderror"
                   placeholder="password"
                   required
                   autocomplete="current-password">
            
            @error('password')
                <div class="invalid-feedback ps-3">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group d-flex align-items-center mb-4 ps-3">
            <input id="remember_me" 
                   type="checkbox" 
                   name="remember" 
                   class="form-check-input mt-0 me-2" 
                   style="border-color: rgba(90, 77, 66, 0.4); cursor: pointer; background-color: transparent; box-shadow: none; background-color:#67574b">
            <label for="remember_me" class="form-label-custom mb-0" style="padding-left: 0; cursor: pointer; text-transform: lowercase;">
                {{ __('remember me') }}
            </label>
           
        </div>

        <button type="submit" class="btn btn-brown shadow-sm">
            {{ __('Log in') }}
        </button>
        
    </form> 
    
    <div class="d-flex flex-column align-items-center text-center mt-3">
        <span class="text-muted small fw-medium mb-2" style="letter-spacing: 1px; color: rgba(90, 77, 66, 0.6) !important;">OR</span>
        
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none transition-all" style="color: #67574b; font-size: 0.95rem;">
            Create an account
        </a>
    </div>

    <div class="footer-text">
        powered by SOMA.
    </div>

</x-guest-layout>