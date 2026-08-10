@auth
    <!-- Scope the custom styles cleanly -->
    <style>
        :root {
            --brand-bg: rgba(255, 255, 255, 0.96);
            --alert-red: #C05C50;
            --shadow-premium: 0 16px 40px rgba(58, 51, 44, 0.06), 0 4px 12px rgba(58, 51, 44, 0.02);
            --font-stack: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
        }

        .profile-dropdown-wrapper {
            position: relative;
            display: inline-block;
            font-family: var(--font-stack);
            -webkit-font-smoothing: antialiased;
        }

        .profile-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid var(--soma-beige);
            cursor: pointer;
            padding: 0;
            background: transparent;
            transition: transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1), border-color 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-btn:hover {
            transform: scale(1.04);
            border-color: var(--soma-taupe);
            box-shadow: 0 0 0 4px rgba(190, 150, 118, 0.15);
        }

        .profile-btn:active {
            transform: scale(0.98);
        }

        /* Avatar image styling to fit the 42px button properly */
        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Fallback styling if no image is uploaded */
        .profile-avatar-fallback {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--soma-taupe);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            /* Adjusted down slightly to fit 42px nicely */
            font-weight: 600;
        }

        .profile-dropdown {
            position: absolute;
            top: 54px;
            right: 0;
            width: 320px;
            background: var(--brand-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(141, 126, 113, 0.2);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow-premium);
            z-index: 1050;
            /* Higher than Bootstrap fixed header elements */
            box-sizing: border-box;
            max-height: calc(100vh - 90px);
            overflow-y: auto;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px) scale(0.98);
            transform-origin: top right;
            transition: opacity 0.2s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.2s;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .dropdown-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(141, 126, 113, 0.15);
        }

        .profile-avatar-fallback {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--soma-taupe);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
        }

        .header-meta {
            text-align: left;
        }

        .dropdown-header h3 {
            color: var(--text-dark);
            font-size: 15px;
            font-weight: 600;
            margin: 0 0 2px 0;
        }

        .dropdown-header p {
            color: var(--soma-taupe);
            font-size: 13px;
            margin: 0;
        }

        .profile-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(141, 126, 113, 0.15);
        }

        .stat-card {
            background: var(--soma-cream);
            border: 1px solid rgba(190, 150, 118, 0.25);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info {
            text-align: left;
        }

        .stat-info h4 {
            color: var(--soma-taupe);
            margin: 0 0 2px 0;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-count {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .stat-action-btn {
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: var(--soma-beige);
            color: var(--text-light);
        }

        .btn-primary:hover {
            background: var(--soma-taupe);
        }

        .btn-secondary {
            background: #FFFFFF;
            border: 1px solid var(--soma-beige);
            color: var(--text-dark);
        }

        .btn-secondary:hover {
            background: var(--soma-cream);
            border-color: var(--soma-taupe);
        }

        .profile-menu {
            padding-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .profile-menu a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--text-dark);
            padding: 10px 12px;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .menu-link-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-menu a svg.icon {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: var(--soma-taupe);
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.2s ease;
        }

        .profile-menu a svg.chevron {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: transparent;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transform: translateX(-4px);
            opacity: 0;
            transition: all 0.2s ease;
        }

        .profile-menu a:hover {
            background: var(--soma-cream);
        }

        .profile-menu a:hover svg.icon {
            stroke: var(--text-dark);
        }

        .profile-menu a:hover svg.chevron {
            transform: translateX(0);
            opacity: 1;
            stroke: var(--soma-beige);
        }

        .logout-form {
            margin-top: 6px;
        }

        .logout-link {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: none;
            background: rgba(192, 92, 80, 0.04);
            color: var(--alert-red);
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .logout-link:hover {
            background: rgba(192, 92, 80, 0.08);
        }

        .logout-link .icon {
            stroke: var(--alert-red);
        }

        @media(max-width: 480px) {
            .profile-dropdown {
                width: calc(100vw - 24px);
                right: -6px;
                padding: 16px;
                max-height: calc(100vh - 80px);
            }
        }
    </style>

    <div class="profile-dropdown-wrapper">
        <!-- Unique Trigger Buttons for Mobile and Desktop visibility -->
        <button class="profile-btn profileToggle" aria-haspopup="true" aria-expanded="false" aria-label="Open profile menu">
            @if(auth()->user()->avatar)
                <img src="{{ asset('uploads/' . auth()->user()->avatar) }}" class="profile-avatar" alt="Avatar">
            @else
                <div class="profile-avatar-fallback">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            @endif
        </button>

        <!-- Dropdown Content Card -->
        <div class="profile-dropdown profileDropdown">
            <!-- User Identity Section -->
            <div class="dropdown-header">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('uploads/' . auth()->user()->avatar) }}" class="profile-avatar-fallback" alt="Avatar">
                @else
                    <div class="profile-avatar-fallback"> {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
                <div class="header-meta">
                    <h3>{{ auth()->user()->name }}</h3>
                    <p>{{ auth()->user()->phone }}</p>
                </div>
            </div>

            <!-- Ecosystem Metrics Segment -->
            <div class="profile-stats">

                <div class="stat-card">
                    <div class="stat-info">
                        <h4>Points</h4>
                        <span class="stat-count">{{ auth()->user()->coins }}</span>
                    </div>
                    <a href="/rates">
                        <button class="stat-action-btn btn-secondary">Redeem</button>
                    </a>
                </div>
            </div>


            <div class="profile-menu">
                <a href="{{ url('my-class-history') }}">
                    <span class="menu-link-content">
                        <i class="fa-regular fa-calendar"></i>
                       Class History
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>

            <!-- System Navigation Paths -->
            <div class="profile-menu">
                <a href="{{ route('history.page') }}">
                    <span class="menu-link-content">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        My Library
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="/edit/profile">
                    <span class="menu-link-content">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Edit Profile
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="/terms">
                    <span class="menu-link-content">
                        <i class="fa-regular fa-copyright" style="color: #BE9676;"></i>
                        Terms & Conditions
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-link">
                        <span class="menu-link-content">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Logout
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endauth