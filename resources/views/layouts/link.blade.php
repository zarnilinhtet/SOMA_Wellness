<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/soma-logo.png') }}">
    <title>SOMA Yoga - Modern Wellness</title>

    <!-- External Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Styles -->
    <style>
        :root {
            /* Brand Palette */
            --soma-cream: #FFF7E9;   /* Soft Cream */
            --soma-beige: #BE9676;   /* Perfect Beige */
            --soma-taupe: #8D7E71;   /* Desert Taupe */
            --text-dark: #8D7E71;    /* Mapped to Desert Taupe to unify brand colors */
            --text-light: #FFF7E9;   /* Mapped to Soft Cream */
        }

        /* --- Global Font Settings --- */
        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea, table, th, td, label, ul, li {
            font-family: 'Fahkwang', sans-serif !important;
        }

        body {
            color: var(--text-dark);
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
            padding-top: 130px;
            /* Offset for fixed header */
        }

        /* --- Header Wrapper --- */
        .header-wrapper {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: #ffffff;
            border-bottom: 1px solid rgba(190, 150, 118, 0.15);
        }

        .header-wrapper.scrolled {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 10px 30px rgba(141, 126, 113, 0.08);
            border-bottom: transparent;
        }

        /* --- Top Social Bar --- */
        .top-bar {
            background-color: var(--soma-taupe);
            padding: 8px 0;
            transition: height 0.3s ease, padding 0.3s ease, opacity 0.3s ease;
        }

        .header-wrapper.scrolled .top-bar {
            height: 0;
            padding: 0;
            opacity: 0;
            overflow: hidden;
        }

        .top-bar-contact {
            color: var(--soma-cream);
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .top-bar-contact i {
            color: var(--soma-beige);
            font-size: 0.9rem;
        }

        .top-bar-icon {
            color: var(--soma-cream);
            font-size: 0.85rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .top-bar-icon:hover {
            color: var(--soma-beige);
        }

        /* --- Modern Navbar --- */
        .navbar-custom {
            padding: 15px 0;
            transition: padding 0.4s ease;
        }

        .header-wrapper.scrolled .navbar-custom {
            padding: 8px 0;
        }

        /* --- Logo Styles --- */
        .logo-img {
            height: 70px;
            width: auto;
            object-fit: contain;
            transition: height 0.4s ease;
        }

        .header-wrapper.scrolled .logo-img {
            height: 50px;
        }

        /* --- Navigation Links --- */
        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0 15px;
            padding: 8px 0 !important;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--soma-beige);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--soma-beige) !important;
        }

        /* --- Buttons --- */
        .btn-modern {
            border-radius: 50px;
            padding: 10px 28px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: all 0.4s ease;
        }

        .btn-primary-modern {
            background-color: var(--soma-taupe);
            color: #fff;
            border: 1px solid var(--soma-taupe);
        }

        .btn-primary-modern:hover {
            background-color: var(--soma-beige);
            border-color: var(--soma-beige);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(190, 150, 118, 0.25);
        }

        .btn-outline-modern {
            border: 1px solid var(--soma-taupe);
            color: var(--soma-taupe);
            background: transparent;
        }

        .btn-outline-modern:hover {
            background-color: var(--soma-taupe);
            color: #fff;
            transform: translateY(-2px);
        }

        /* --- Dropdown Cleanups --- */
        .dropdown-menu .dropdown-item:active,
        .dropdown-menu .dropdown-item:focus,
        .dropdown-menu .dropdown-item:hover {
            background-color: var(--soma-cream) !important;
            color: inherit !important;
        }

        .dropdown-menu button:focus,
        .dropdown-menu button:active {
            outline: none !important;
            box-shadow: none !important;
            background-color: var(--soma-cream) !important;
        }

        /* --- Mobile Responsive Fixes --- */
        @media (max-width: 991px) {
            body {
                padding-top: 100px;
                padding-bottom: 70px;
                /* Space for fixed bottom bar */
            }

            .logo-img {
                height: 50px;
            }

            .header-wrapper.scrolled .logo-img {
                height: 40px;
            }

            .nav-link {
                margin: 8px 0;
            }
        }

        /* --- Mobile Tab Bar --- */
        .mobile-tab-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 65px;
            background: #ffffff;
            border-top: 1px solid rgba(141, 126, 113, 0.2);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1050;
        }

        @media (max-width: 991px) {
            .mobile-tab-bar {
                display: flex;
                justify-content: space-around;
                align-items: center;
            }
        }

        .tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .tab-item i {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .tab-item.active {
            color: var(--soma-beige);
        }

        /* --- Footer --- */
        .modern-footer {
            background-color: var(--soma-taupe); /* Deepened to match Desert Taupe brand */
            color: var(--soma-cream);
            padding: 80px 0 40px;
        }

        .modern-footer .navbar-brand {
            color: var(--soma-cream) !important;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .footer-link {
            color: rgba(255, 247, 233, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
            margin-bottom: 12px;
            cursor: pointer;
        }

        .footer-link:hover {
            color: var(--soma-cream);
        }

        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(255, 247, 233, 0.2);
            color: var(--soma-cream);
            margin-right: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-icons a:hover {
            background-color: var(--soma-beige);
            border-color: var(--soma-beige);
            transform: translateY(-3px);
        }
        
        .policy-link {
            color: var(--soma-beige); 
            font-size: 0.85rem; 
            text-decoration: none; 
            transition: color 0.3s ease;
            cursor: pointer;
        }
        
        .policy-link:hover {
            color: var(--soma-cream);
        }

        /* --- Custom Tab Styles for T&Cs Modal --- */
        .soma-nav-tabs {
            border-bottom: 2px solid #e9ecef;
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }
        .soma-nav-tabs::-webkit-scrollbar {
            display: none;
        }
        .soma-nav-tabs .nav-link {
            color: var(--soma-taupe);
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 600;
            padding: 12px 16px;
            margin-bottom: -2px;
            background: transparent;
            white-space: nowrap;
            transition: color 0.3s ease, border-color 0.3s ease;
        }
        .soma-nav-tabs .nav-link:hover {
            color: var(--soma-beige);
        }
        .soma-nav-tabs .nav-link.active {
            color: var(--soma-beige);
            border-bottom: 2px solid var(--soma-beige);
            background: transparent;
        }
        .soma-tab-content {
            padding: 24px 0 10px 0;
            font-size: 0.95rem;
            line-height: 1.8;
            color: var(--text-dark);
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <header class="fixed-top header-wrapper" id="headerWrapper">

        <!-- Top Announcement/Contact Bar -->
        <div class="top-bar">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="top-bar-contact">
                    <i class="fas fa-phone-alt me-2"></i> 092001407, 092002407
                </div>

                <div class="d-flex gap-4">
                    <a href="https://www.facebook.com/somawellness.mm" target="_blank" rel="noopener noreferrer" class="top-bar-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/somawellness.mm/" class="top-bar-icon" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@somawellness.mm?_r=1" class="top-bar-icon" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://www.youtube.com/@somawellness_mm" class="top-bar-icon" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">

                <!-- Logo (Left-aligned) -->
                <a class="navbar-brand m-0 p-0" href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/soma-logo.png') }}" alt="SOMA Logo" class="logo-img">
                </a>

                <!-- Collapsible Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center text-lg-start pt-3 pt-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home.page') ? 'active' : '' }}"
                                href="{{ route('home.page') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('class.page') ? 'active' : '' }}"
                                href="{{ route('class.page') }}">Classes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rates.page') ? 'active' : '' }}"
                                href="{{ route('rates.page') }}">Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact.page') ? 'active' : '' }}"
                                href="{{ route('contact.page') }}">Contact</a>
                        </li>
                    </ul>
                </div>

                <!-- User Controls (Unified Desktop & Mobile Header) -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    @auth
                        <!-- Notification Dropdown -->
                        <div class="dropdown me-2 me-lg-3">
                            <button class="btn btn-link nav-link position-relative p-0 border-0" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fs-5" style="color: var(--soma-taupe);"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.6rem;">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3"
                                style="width: 300px; max-height: 400px; overflow-y: auto; border-radius: 15px;">
                                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted small">Notifications</span>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.readAll') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none"
                                                style="color: var(--soma-beige); font-size: 0.75rem;">
                                                Mark all read
                                            </button>
                                        </form>
                                    @endif
                                </li>

                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <li class="dropdown-item py-2 border-bottom bg-transparent">
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-link text-decoration-none text-dark w-100 text-start p-0 shadow-none">
                                                <div class="fw-bold small">{{ $notification->data['title'] ?? 'Notification' }}
                                                </div>
                                                <p class="small text-muted mb-0" style="line-height: 1.3; font-size: 0.75rem;">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                            </button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="px-3 py-3 text-muted text-center small">No new notifications</li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- User Profile Include -->
                        <div class="profile-dropdown-wrapper" style="position: relative; display: inline-block;">
                            @include('layouts.profile')
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-modern btn-outline-modern shadow-sm">Log In</a>
                    @endauth
                </div>

            </div>
        </nav>
    </header>

    <!-- Mobile Bottom Tab Navigation -->
    <nav class="mobile-tab-bar">
        <a href="{{ route('home.page') }}" class="tab-item {{ request()->routeIs('home.page') ? 'active' : '' }}">
            <i class="fas fa-home"></i> <span>Home</span>
        </a>
        <a href="{{ route('class.page') }}" class="tab-item {{ request()->routeIs('class.page') ? 'active' : '' }}">
            <i class="fas fa-spa"></i> <span>Classes</span>
        </a>
        <a href="{{ route('rates.page') }}" class="tab-item {{ request()->routeIs('rates.page') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> <span>Packages</span>
        </a>
        <a href="{{ route('contact.page') }}" class="tab-item {{ request()->routeIs('contact.page') ? 'active' : '' }}">
            <i class="fa-solid fa-phone"></i> <span>Contact</span>
        </a>
    </nav>

    <!-- Main Dynamic Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Section -->
    <footer id="contact" class="modern-footer">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <a class="navbar-brand mb-4 d-block" href="#">SOMA</a>
                    <p class="pe-lg-5" style="color: rgba(255, 247, 233, 0.8) !important;">
                        Elevating your physical and mental wellbeing through mindful movement and community connection.
                    </p>
                    <div class="social-icons mt-4">
                        <a href="https://www.facebook.com/somawellness.mm" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/somawellness.mm/" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@somawellness.mm?_r=1" target="_blank" rel="noopener noreferrer"><i class="fab fa-tiktok"></i></a>
                        <a href="https://www.youtube.com/@somawellness_mm" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-5 mb-lg-0">
                    <h5 class="text-white mb-4" style="font-size: 1.25rem;">Explore</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home.page') }}" class="footer-link">Home</a></li>
                        <li><a href="{{ route('class.page') }}" class="footer-link">Classes</a></li>
                        <li><a href="{{ route('rates.page') }}" class="footer-link">Packages</a></li>
                        <li><a href="{{ route('contact.page') }}" class="footer-link">Contact</a></li>
                        <!-- Updated T&C Trigger Link -->
                        <li><a data-bs-toggle="modal" data-bs-target="#termsModal" class="footer-link">T&Cs</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4" style="font-size: 1.25rem;">Contact</h5>
                    <ul class="list-unstyled" style="color: rgba(255, 247, 233, 0.8);">
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt me-3" style="color: var(--soma-beige);"></i> No.110, 27th
                            Street, Between 76th & 77th, Mandalay, Myanmar
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope me-3" style="color: var(--soma-beige);"></i>
                            somawellness.mm@gmail.com
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-phone me-3" style="color: var(--soma-beige);"></i> 092001407, 092002407
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
                <div class="col-12 text-center">
                    <p class="mb-2" style="color: rgba(255, 247, 233, 0.6); font-size: 0.85rem;">
                        &copy; {{ date('Y') }} SOMA Yoga Management System. Crafted with mindfulness.
                    </p>
                   
                </div>
            </div>
        </div>
    </footer>

    <!-- Comprehensive T&Cs Modal (Tabbed - Only 2 Tabs) -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0" style="background-color: var(--soma-cream);">
                    <h5 class="modal-title fw-bold" id="termsModalLabel" style="color: var(--text-dark);">Terms & Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-0 pt-3">
                    
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs soma-nav-tabs" id="tcTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="rules-tc-tab" data-bs-toggle="tab" data-bs-target="#rules-tc" type="button" role="tab" aria-controls="rules-tc" aria-selected="true">Studio Rules & Purchase Regulations</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payment-tc-tab" data-bs-toggle="tab" data-bs-target="#payment-tc" type="button" role="tab" aria-controls="payment-tc" aria-selected="false">Standard Payment Policy</button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content soma-tab-content" id="tcTabsContent">
                        
                        <!-- Studio Rules Tab (Active) -->
                        <div class="tab-pane fade show active" id="rules-tc" role="tabpanel" aria-labelledby="rules-tc-tab">
                            <ul style="list-style-type: none; padding-left: 0;">
                                <li class="mb-3">
                                    <i class="fas fa-calendar-check me-2" style="color: var(--soma-beige);"></i> <strong>Validity:</strong> Packages are valid for 30 days starting from the payment date. Validity begins from your first attended class.
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-ban me-2" style="color: var(--soma-beige);"></i> <strong>Non-Refundable:</strong> Class packs are non-refundable, non-transferable between individuals or studios, and cannot be extended under any circumstances (including injury or illness).
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-clock me-2" style="color: var(--soma-beige);"></i> <strong>Cancellation Policy:</strong> Cancellations or rescheduling must be made at least 24 hours prior to your class. Late cancellations or no-shows will result in the loss of that class credit with no refund provided.
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-mobile-alt me-2" style="color: var(--soma-beige);"></i> <strong>Studio Etiquette:</strong> Please keep mobile devices in silent mode before entering the studio.
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-door-open me-2" style="color: var(--soma-beige);"></i> <strong>Room Access:</strong> Please wait in the designated area until your scheduled session time. Kindly exit promptly upon conclusion to allow for space preparation.
                                </li>
                            </ul>
                        </div>

                        <!-- Payment Policy Tab -->
                        <div class="tab-pane fade" id="payment-tc" role="tabpanel" aria-labelledby="payment-tc-tab">
                            <p class="mb-4 fw-medium" style="color: var(--soma-taupe);">
                                Please carefully read our standard payment policy before completing your purchase:
                            </p>
                            <ul style="list-style-type: disc; padding-left: 20px;">
                                <li class="mb-3">All payment requests are processed manually within <strong>10 to 30 minutes</strong> under regular processing hours.</li>
                                <li class="mb-3">You must provide a clear and authentic receipt voucher image/screenshot displaying the corresponding global <strong>Transaction Reference Identifier Code</strong>.</li>
                                <li class="mb-3">Falsified proof or multiple entries mapping a single voucher instance code will trigger immediate automated system account terminal validation locks.</li>
                                <li class="mb-0">Refunds are <strong>not permitted</strong> once process verification pipeline statuses settle into completed execution branches.</li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 justify-content-center pt-2">
                    <button type="button" class="btn btn-modern btn-primary-modern px-5" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Additional View Scripts -->
    @yield('scriptSource')

    <!-- Custom Interaction JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const header = document.getElementById('headerWrapper');

            // Floating Header Scroll Detection
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Profile Dropdown Toggle Logic
            const toggleButtons = document.querySelectorAll('.profileToggle');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.stopPropagation();
                    const parent = this.closest('.profile-dropdown-wrapper');
                    if (!parent) return;

                    const menu = parent.querySelector('.profileDropdown');
                    if (!menu) return;

                    // Close all other dropdowns
                    document.querySelectorAll('.profileDropdown').forEach(item => {
                        if (item !== menu) item.classList.remove('active');
                    });

                    const isActive = menu.classList.toggle('active');
                    this.setAttribute('aria-expanded', isActive);
                });
            });

            // Close dropdowns on outside click
            window.addEventListener('click', function (event) {
                document.querySelectorAll('.profile-dropdown-wrapper').forEach(wrapper => {
                    const menu = wrapper.querySelector('.profileDropdown');
                    const btn = wrapper.querySelector('.profileToggle');
                    if (menu && !wrapper.contains(event.target)) {
                        menu.classList.remove('active');
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            // Escape Key Close Handler
            window.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    document.querySelectorAll('.profileDropdown').forEach(menu => {
                        menu.classList.remove('active');
                    });
                    toggleButtons.forEach(btn => btn.setAttribute('aria-expanded', 'false'));
                }
            });
        });
    </script>
</body>

</html>