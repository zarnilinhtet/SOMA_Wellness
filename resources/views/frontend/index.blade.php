@extends('layouts.link')
@section('content')
    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* --- Brand Variables --- */
        :root {
            --soma-primary: #BE9676; /* Perfect Beige */
            --soma-secondary: #8D7E71; /* Desert Taupe */
            --soma-bg: #FFF7E9; /* Soft Cream */
            --soma-dark: #2C2C2C;
        }

        /* --- Global Font Settings --- */
        body, h1, h2, h3, h4, h5, h6, p, span, a, div {
            font-family: 'Fahkwang', sans-serif;
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 40px 0 60px;
            background: linear-gradient(135deg, var(--soma-bg) 0%, #ffffff 100%);
            min-height: auto;
            display: flex;
            align-items: center;
            overflow: visible;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            line-height: 1.15;
            font-weight: 500; 
            margin-bottom: 24px;
        }

        .hero-title i {
            font-style: italic;
            color: var(--soma-primary);
        }

        .hero-img-wrapper {
            position: relative;
            border-radius: 30px 100px 30px 30px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(141, 126, 113, 0.15); 
        }

        .hero-img {
            width: 100%;
            height: clamp(380px, 50vh, 700px);
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .hero-img-wrapper:hover .hero-img {
            transform: scale(1.05);
        }

        /* --- Closure Notice Banner --- */
        .closure-notice-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(252, 250, 248, 0.95) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(141, 126, 113, 0.15);
            border-left: 5px solid var(--soma-secondary);
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 12px 35px rgba(141, 126, 113, 0.08);
            position: relative;
            margin-bottom: 2rem !important;
            animation: slideDownFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        @keyframes slideDownFade {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .closure-icon-wrapper {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;
            background: rgba(141, 126, 113, 0.1);
            color: var(--soma-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.5);
        }

        .closure-content {
            flex-grow: 1;
            padding-top: 2px;
        }

        .closure-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700; 
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--soma-secondary);
            margin-bottom: 6px;
        }

        .closure-message {
            font-size: 0.9rem;
            color: #4a4a4a;
            line-height: 1.6;
        }

        .closure-message p { margin-bottom: 8px; }
        .closure-message p:last-child { margin-bottom: 0; }
        .closure-message strong, .closure-message b { color: #2c2c2c; font-weight: 600; }

        .btn-close-notice {
            background: transparent;
            border: none;
            color: #a0a0a0;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            margin-top: -4px;
        }

        .btn-close-notice:hover {
            color: #333;
            background: rgba(0, 0, 0, 0.05);
            transform: rotate(90deg);
        }

        /* --- Sections Shared --- */
        .about-section, .workshop-section {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .workshop-section {
            background-color: var(--soma-bg);
        }

        .tagline {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 3px;
            color: var(--soma-secondary);
            text-transform: uppercase;
            margin-bottom: 15px;
            display: block;
        }

        .image-stack {
            position: relative;
            margin-bottom: 30px;
        }

        .image-stack-bottom {
            width: 85%;
            border-radius: 20px;
            object-fit: cover;
            height: clamp(300px, 40vh, 500px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }

        .image-stack-top {
            width: 55%;
            position: absolute;
            bottom: -30px;
            right: 0;
            border-radius: 20px;
            border: 8px solid #ffffff;
            box-shadow: 0 20px 40px rgba(141, 126, 113, 0.1);
            object-fit: cover;
            height: clamp(200px, 30vh, 350px);
        }

        /* --- Workshop Slider Specifics --- */
        .workshop-image-main {
            width: 100%;
            height: clamp(350px, 50vh, 550px);
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(141, 126, 113, 0.15);
        }

        .carousel-indicators {
            bottom: -50px;
        }

        .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--soma-primary);
            opacity: 0.4;
            border: none;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .carousel-indicators .active {
            opacity: 1;
            width: 25px;
            border-radius: 10px;
        }

        .slider-nav-btn {
            width: 45px;
            height: 45px;
            background: #fff;
            color: var(--soma-primary);
            border: 1px solid rgba(190, 150, 118, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
  .workshop-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        padding: 8px 14px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(141, 126, 113, 0.08);
        border: 1px solid rgba(190, 150, 118, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .workshop-meta-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(141, 126, 113, 0.12);
    }

    .meta-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--soma-bg);
        color: var(--soma-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .meta-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--soma-secondary);
        font-weight: 600;
        line-height: 1;
        margin-bottom: 2px;
    }

    .meta-value {
        font-size: 0.85rem;
        color: #2c2c2c;
        font-weight: 600;
        line-height: 1.2;
    }
        .slider-nav-btn:hover {
            background: var(--soma-primary);
            color: #fff;
            transform: translateY(-2px);
        }

        /* --- Tablet & Mobile Queries --- */
        @media (min-width: 768px) {
            .hero-section { padding: 60px 0 100px; }
            .about-section, .workshop-section { padding: 100px 0; }
            .closure-notice-card { padding: 20px 24px; }
            .closure-icon-wrapper {
                width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem;
            }
            .image-stack-top { border-width: 12px; bottom: -50px; }
        }

        @media (max-width: 575.98px) {
            .hero-img-wrapper { border-radius: 20px 60px 20px 20px; }
            .image-stack-bottom { width: 100%; }
            .closure-notice-card { flex-direction: column; align-items: flex-start; gap: 12px; }
            .btn-close-notice { position: absolute; top: 12px; right: 12px; }
            .carousel-indicators { bottom: -35px; }
        }
    </style>

    <section id="home" class="hero-section">
        <div class="container">
            <div>
                <!-- Studio Closure / Announcement Banner -->
                @if(isset($closeDate) && !empty($closeDate->description))
                    <div id="studioCloseBanner" class="closure-notice-card" role="alert">
                        <div class="closure-icon-wrapper">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <div class="closure-content">
                            <span class="closure-badge">Studio Announcement</span>
                            <div class="closure-message">
                                {!! $closeDate->description !!}
                            </div>
                        </div>
                        <button type="button" class="btn-close-notice" aria-label="Close" onclick="dismissBanner()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-lg-6 z-2">
                        <span class="tagline">Welcome to SOMA</span>
                        <h1 class="hero-title">Find your <i style="color: var(--soma-primary);">Soma.</i><br>Find your <i
                                style="color: var(--soma-primary);">Peace.</i></h1>
                        <p class="lead mb-4 mt-3 text-muted pe-lg-4"
                            style="font-size: 1.05rem; line-height: 1.8; color: var(--soma-secondary) !important;">
                            A premium yoga & pilates wellness studio dedicated to supporting your body, mind and wellbeing.
                            Step into our sanctuary and discover your ultimate potential.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#classes" class="btn btn-modern px-4 py-2" style="background-color: var(--soma-primary); border-color: var(--soma-primary); color: white; border-radius: 30px;">Explore Schedule</a>
                            <a href="#about" class="btn btn-modern px-4 py-2 text-decoration-underline"
                                style="background: transparent; color: var(--soma-secondary); border: none;">Discover More</a>
                        </div>
                    </div>
                    <div class="col-lg-6 z-1 mt-4 mt-lg-0">
                        <div class="hero-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1599901860904-17e6ed7083a0?q=80&w=1400&auto=format&fit=crop"
                                alt="Yoga Woman" class="hero-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workshop Slider Section -->
    @if(isset($workshops) && count($workshops) > 0)
    <section id="workshops" class="workshop-section position-relative pb-5">
        <div class="container pb-4">
            <div class="row mb-5">
                <div class="col-12 text-center text-md-start">
                    <span class="tagline">Exclusive Sessions</span>
                    <h2 class="hero-title section-heading-title m-0">Our <i>Workshops</i></h2>
                </div>
            </div>

            <!-- Bootstrap Carousel Slider -->
            <div id="workshopCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                
                <!-- Indicators -->
                @if(count($workshops) > 1)
                <div class="carousel-indicators">
                    @foreach ($workshops as $index => $workshop)
                        <button type="button" data-bs-target="#workshopCarousel" data-bs-slide-to="{{ $index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                @endif

                <div class="carousel-inner">
                    @foreach ($workshops as $workshop)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row align-items-center g-4 g-lg-5">
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <div class="position-relative">
                                        <img src="{{ $workshop->image_1 ? asset($workshop->image_1) : asset('assets/img/doyoga_about_2.jpg') }}"
                                            alt="{{ $workshop->class_name }}" 
                                            class="workshop-image-main">
                                    </div>
                                </div>
                                <div class="col-lg-6 ps-lg-5 mt-4 mt-lg-0">
                                    <span class="tagline d-inline-flex align-items-center gap-2 mb-3">
                                        <i class="bi bi-star-fill text-warning" style="font-size: 0.9rem;"></i> Special Event
                                    </span>
                                    
                                    <h2 class="hero-title section-heading-title mb-4" style="font-size: clamp(2rem, 3.5vw, 3rem);">{{ $workshop->class_name }}</h2>
                                    
                                    <div class="text-muted mb-4" style="line-height: 1.8; color: var(--soma-secondary) !important; font-size: 1.05rem;">
                                        {!! !empty(trim($workshop->description)) ? Str::limit(strip_tags($workshop->description), 250) : 'Join us for this exclusive workshop designed to deepen your practice and elevate your wellness journey.' !!}
                                    </div>

                                    <!-- Date/Time details -->
                                  <!-- Date & Time Details (Single Row Layout) -->
<div class="d-flex flex-wrap align-items-center gap-2 mb-4 pt-1">
    @if($workshop->start_date)
    <div class="workshop-meta-badge">
        <div class="meta-icon-box">
            <i class="bi bi-calendar3"></i>
        </div>
        <div class="d-flex flex-column">
            <span class="meta-label">Date</span>
            <span class="meta-value">{{ \Carbon\Carbon::parse($workshop->start_date)->format('d M Y') }}</span>
        </div>
    </div>
    @endif
    
    @if($workshop->start_time)
    <div class="workshop-meta-badge">
        <div class="meta-icon-box">
            <i class="bi bi-clock"></i>
        </div>
        <div class="d-flex flex-column">
            <span class="meta-label">Start Time</span>
            <span class="meta-value">{{ \Carbon\Carbon::parse($workshop->start_time)->format('h:i A') }}</span>
        </div>
    </div>
    @endif

    @if($workshop->end_time)
    <div class="workshop-meta-badge">
        <div class="meta-icon-box">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="d-flex flex-column">
            <span class="meta-label">End Time</span>
            <span class="meta-value">{{ \Carbon\Carbon::parse($workshop->end_time)->format('h:i A') }}</span>
        </div>
    </div>
    @endif
</div>


                                    <!-- Join Button & Slider Navigation -->
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-2">
                                        <a href="{{ route('class.details', ['id' => $workshop->id]) }}" class="btn btn-primary px-4 py-3 fw-medium" style="background-color: var(--soma-primary); border-color: var(--soma-primary); border-radius: 30px; box-shadow: 0 8px 20px rgba(190, 150, 118, 0.3);">
                                            View & Join Workshop <i class="bi bi-arrow-right ms-2"></i>
                                        </a>

                                        <!-- Custom Nav Buttons (Desktop Right side aligned) -->
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
              
            </div>
        </div>
    </section>
    @endif

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="image-stack">
                        <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=1000&auto=format&fit=crop"
                            alt="Studio" class="image-stack-bottom">
                        <img src="{{ asset('assets/img/reformer.jpg') }}" alt="Details"
                            class="image-stack-top d-none d-md-block">
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5 mt-4 mt-lg-0">
                    <span class="tagline">The Soma Approach</span>
                    <h2 class="hero-title section-heading-title mb-4">Strengthen the body,
                        <i>awaken</i> the mind.
                    </h2>
                    <p class="text-muted mb-4" style="line-height: 1.8; color: var(--soma-secondary) !important;">
                        At SOMA, we offer a thoughtful fusion of Yoga and Pilates to build physical resilience while
                        fostering inner stillness. Every mindful movement is an invitation to reconnect with yourself.
                    </p>
                    <p class="text-muted mb-4 mb-lg-5" style="line-height: 1.8; color: var(--soma-secondary) !important;">
                        Whether you are looking to strengthen your core with Pilates or find your grounding flow through
                        Yoga, our space and expert instructors offer the perfect sanctuary for your personal growth.
                    </p>
                    <a href="#contact" class="btn px-4 py-2" style="border: 1px solid var(--soma-primary); color: var(--soma-primary); border-radius: 30px;">Visit Our Studio</a>
                </div>
            </div>
        </div>
    </section>

    @include('frontend.about')

@endsection

@include('master.footer')

<script>
    function dismissBanner() {
        const banner = document.getElementById('studioCloseBanner');
        if (banner) {
            banner.style.transition = 'all 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-15px)';
            setTimeout(() => banner.remove(), 400);
        }
    }
</script>