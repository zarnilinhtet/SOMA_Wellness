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
            font-weight: 500; /* Medium weight from Fahkwang */
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
            box-shadow: 0 30px 60px rgba(141, 126, 113, 0.15); /* Using Desert Taupe RGB */
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

        /* Premium Studio Closure Notice Banner */
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
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            font-weight: 700; /* Bold weight from Fahkwang */
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

        .closure-message p {
            margin-bottom: 0;
        }

        .closure-message p:not(:last-child) {
            margin-bottom: 8px;
        }

        .closure-message strong,
        .closure-message b {
            color: #2c2c2c;
            font-weight: 600; /* Semibold weight from Fahkwang */
        }

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

        /* --- About Section --- */
        .about-section {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .tagline {
            font-size: 0.8rem;
            font-weight: 600; /* Semibold */
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

        /* --- Classes Section (Modern Cards) --- */
        .classes-section {
            padding: 80px 0;
            background-color: var(--soma-bg); /* Soft Cream applied here */
        }

        .section-heading-title {
            font-size: clamp(2.2rem, 4vw, 3.5rem);
        }

        .modern-card {
            background: #ffffff;
            border-radius: 24px;
            border: none;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(141, 126, 113, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .modern-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(141, 126, 113, 0.15);
        }

        .card-img-container {
            overflow: hidden;
            height: 260px;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .modern-card:hover .card-img-container img {
            transform: scale(1.08);
        }

        .card-body {
            padding: 28px 24px;
            flex-grow: 1;
        }

        .card-category {
            font-size: 0.75rem;
            font-weight: 600; /* Semibold */
            letter-spacing: 2px;
            color: var(--soma-primary);
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
        }

        .card-title {
            font-size: 1.75rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        /* --- Tablet & Mobile Queries --- */
        @media (min-width: 768px) {
            .hero-section {
                padding: 60px 0 100px;
            }

            .about-section,
            .classes-section {
                padding: 120px 0;
            }

            .closure-notice-card {
                padding: 20px 24px;
            }

            .closure-icon-wrapper {
                width: 48px;
                height: 48px;
                min-width: 48px;
                font-size: 1.25rem;
            }

            .closure-message {
                font-size: 0.95rem;
            }

            .image-stack-top {
                border-width: 12px;
                bottom: -50px;
            }

            .card-img-container {
                height: 320px;
            }

            .card-body {
                padding: 40px 30px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-img-wrapper {
                border-radius: 20px 60px 20px 20px;
            }

            .image-stack-bottom {
                width: 100%;
            }

            .closure-notice-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .btn-close-notice {
                position: absolute;
                top: 12px;
                right: 12px;
            }
        }
    </style>

    <section id="home" class="hero-section">
        <div class="container">
            <div>
                <!-- Display Banner if closeDate exists and description is not empty -->
                @if(isset($closeDate) && !empty($closeDate->description))
                    <div id="studioCloseBanner" class="closure-notice-card" role="alert">
                        <div class="closure-icon-wrapper">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="closure-content">
                            <span class="closure-badge">Studio Announcement</span>
                            <div class="closure-message">
                                <!-- Render Summernote HTML Output Directly -->
                                {!! $closeDate->description !!}
                            </div>
                        </div>
                        <button type="button" class="btn-close-notice" aria-label="Close" onclick="dismissBanner()">
                            <i class="fas fa-times"></i>
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
                            <a href="#classes" class="btn btn-modern btn-primary-modern" style="background-color: var(--soma-primary); border-color: var(--soma-primary); color: white;">Explore Schedule</a>
                            <a href="#about" class="btn btn-modern btn-outline-modern border-0 text-decoration-underline"
                                style="background: transparent; color: var(--soma-secondary);">Discover More</a>
                        </div>
                    </div>
                    <div class="col-lg-6 z-1 mt-4 mt-lg-0">
                        <div class="hero-img-wrapper">
                            <!-- Modern, artistic yoga image -->
                            <img src="https://images.unsplash.com/photo-1599901860904-17e6ed7083a0?q=80&w=1400&auto=format&fit=crop"
                                alt="Yoga Woman" class="hero-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <i>awaken</i>, the mind.
                    </h2>
                    <p class="text-muted mb-4" style="line-height: 1.8; color: var(--soma-secondary) !important;">
                        At SOMA, we offer a thoughtful fusion of Yoga and Pilates to build physical resilience while
                        fostering inner stillness. Every mindful movement is an invitation to reconnect with yourself.
                    </p>
                    <p class="text-muted mb-4 mb-lg-5" style="line-height: 1.8; color: var(--soma-secondary) !important;">
                        Whether you are looking to strengthen your core with Pilates or find your grounding flow through
                        Yoga, our space and expert instructors offer the perfect sanctuary for your personal growth.
                    </p>
                    <a href="#contact" class="btn btn-modern btn-outline-modern" style="border-color: var(--soma-primary); color: var(--soma-primary);">Visit Our Studio</a>
                </div>
            </div>
        </div>
    </section>

    @include('frontend.about')

    <!-- Classes Section -->
    @if(!$workshops)
        <section id="classes" class="classes-section">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 pb-2">
                    <div>
                        <span class="tagline">Curated Experiences</span>
                        <h2 class="hero-title section-heading-title m-0">Our WorkShops</h2>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach ($workshops as $workshop)
                        <div class="col-lg-4 col-md-6">
                            <div class="modern-card">
                                <div class="card-img-container">
                                    <img src="{{ asset($workshop->image ?? 'assets/img/doyoga_about_2.jpg') }}"
                                        alt="{{ $workshop->workshop_name }}">
                                </div>
                                <div class="card-body">
                                    <span class="card-category">{{ $workshop->workshop_name }}</span>
                                    <p class="text-muted mb-0" style="color: var(--soma-secondary) !important;">
                                        {{ !empty(trim($workshop->description))
                        ? Str::limit(strip_tags($workshop->description), 120)
                        : 'At SOMA, we provide the space, the community, and the expert guidance to help you craft a lifestyle that nourishes both your body and mind.' 
                                                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection

@include('master.footer')

<script>
    function dismissBanner() {
        const banner = document.getElementById('studioCloseBanner');
        if (banner) {
            banner.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-10px)';
            setTimeout(() => banner.remove(), 300);
        }
    }
</script>