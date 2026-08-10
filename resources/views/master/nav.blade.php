<div class="main-header">

    <style>
        .nav-user-btn {
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
        }

        .nav-user-btn:hover {
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }

        .hide-caret::after {
            display: none !important;
        }

        .dropdown-item {
            font-size: 0.9rem;
            transition: 0.2s;
        }

        #liveDateTime {
            font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
        }

        /* Smooth hover effect for dropdown items */
        .dropdown-item {
            transition: all 0.2s ease;
            border-radius: 12px;
            cursor: pointer;
            margin: 0 10px;
            width: auto;
        }

        .dropdown-item:hover {
            background-color: #fdfaf7;
            /* Matches your soma-cream theme */
            transform: translateX(5px);
            color: var(--soma-taupe);
        }

        /* Scrollbar styling for a cleaner look */
        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #e0d5ce;
            border-radius: 10px;
        }

        /* Ensure the button allows multi-line text */
        .dropdown-item button {
            white-space: normal !important;
            /* Forces wrapping */
            height: auto !important;
            /* Prevents height restriction */
            display: block !important;
            text-align: left !important;
        }

        /* Keep the clamp logic */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        /* Subtle animation for the bell badge */
        .badge {
            transition: transform 0.3s ease;
        }
    </style>

    <nav class="navbar navbar-expand-lg bg-white border-bottom py-2 sticky-top shadow-sm">
        <div class="container-fluid px-2 px-md-3">

            <div class="d-flex align-items-center gap-2 gap-md-3">

                <button
                    class="btn btn-light border d-inline-flex d-lg-none align-items-center justify-content-center p-2 sidenav-toggler"
                    type="button">
                    <i class="fa-solid fa-bars fs-4" style="line-height: 1;"></i>
                </button>

                <div class="bg-light border rounded-3 px-2 px-md-3 py-2 d-none d-sm-flex align-items-center shadow-sm">
                    <i class="bi bi-clock-fill text-primary me-2"></i>
                    <div id="liveDateTime" class="text-dark small fw-bold"
                        style="min-width: max-content; letter-spacing: 0.5px;">
                        <span class="text-muted">Loading...</span>
                    </div>
                </div>
            </div>

            <div class="ms-auto d-flex align-items-center gap-2">

                {{-- <div class="d-none d-md-flex align-items-center border-end pe-4 me-2">
                    <div class="bg-danger rounded-2 d-flex align-items-center justify-content-center me-2"
                        style="width: 32px; height: 32px;">
                        <span class="text-white fw-bold small">C</span>
                    </div>
                    <div class="lh-1">
                        <span class="fw-bolder text-dark d-block"
                            style="font-size: 1rem; letter-spacing: 0.5px;">CODEVERSE</span>
                        <small class="text-muted fw-semibold" style="font-size: 0.65rem;">POS SYSTEM V2</small>
                    </div>
                </div> --}}

                <div class="dropdown me-3">
                    <button class="btn btn-link nav-link position-relative p-0 border-0" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5" style="color: var(--soma-taupe);"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size: 0.6rem;">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3"
                        style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 20px;">

                        <li class="px-4 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-muted">Notifications</span>

                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.readAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none"
                                        style="color: var(--soma-beige); font-size: 0.8rem;">
                                        Mark all read
                                    </button>
                                </form>
                            @endif
                        </li>


                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <li class="dropdown-item py-3 border-bottom">
                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-link text-decoration-none text-dark w-100 text-start p-0">
                                        <div class="fw-bold small text-decoration-none">{{ $notification->data['title'] }}
                                        </div>
                                        <p class="small text-muted mb-0 line-clamp-2" style="line-height: 1.4;">
                                            {{ $notification->data['message'] }}
                                        </p>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-muted text-center small">No new notifications</li>
                        @endforelse
                    </ul>
                </div>

                <div class="dropdown">
                    <a class="nav-user-btn d-flex align-items-center gap-2 gap-md-3 text-decoration-none dropdown-toggle hide-caret px-2 px-md-3 py-1 rounded-pill"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-none d-md-block text-end">
                            <p class="mb-0 fw-bold text-dark lh-1" style="font-size: 0.9rem;">
                                </li>
                                </ul>
                        </div>

                        <div class="dropdown">
                            <a class="nav-user-btn d-flex align-items-center gap-2 gap-md-3 text-decoration-none dropdown-toggle hide-caret px-2 px-md-3 py-1 rounded-pill"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="d-none d-md-block text-end">
                                    <p class="mb-0 fw-bold text-dark lh-1" style="font-size: 0.9rem;">
                                        {{ Auth::user()->name ?? 'Admin User' }}
                                    </p>
                                    <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem; font-weight: 500;">
                                        @if(Auth::user()->hasRole('Admin'))
                                            Admin
                                        @elseif(Auth::user()->hasRole('Instructor'))
                                            Instructor
                                        @endif
                                    </p>
                                </div>
                                <div class="position-relative">
                                    <img src="{{ asset('assets/img/soma-logo.png') }}" alt="Profile"
                                        class="rounded-circle border border-2 border-primary-subtle shadow-sm"
                                        width="40" height="40" style="object-fit: cover;">
                                    <span
                                        class="position-absolute bottom-0 end-0 p-1  border border-white rounded-circle shadow-sm"></span>
                                </div>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2 rounded-4"
                                style="min-width: 240px;">
                                <li class="p-3 mb-2 bg-light rounded-3">
                                    <small class="text-muted d-block mb-1">Signed in as</small>
                                    <h6 class="mb-0 fw-bold text-dark text-truncate">{{ Auth::user()->phone }}</h6>
                                </li>
                                {{-- <li><a class="dropdown-item rounded-3 py-2 px-3"
                                        href="{{ route('profile.edit') }}"><i
                                            class="bi bi-person-circle me-2 text-primary"></i> My Profile</a></li> --}}
                                <li>
                                    <hr class="dropdown-divider mx-2">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item rounded-3 py-2 px-3 text-danger fw-bold">
                                            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                </div>
            </div>
    </nav>
</div>
<script>
    function updateDateTime() {
        const clockElement = document.getElementById('liveDateTime');
        if (!clockElement) return;

        const now = new Date();
        const dateOptions = { month: 'short', day: 'numeric', year: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };

        clockElement.innerHTML = `
            <span class="text-danger">${now.toLocaleDateString('en-US', dateOptions)}</span>
            <span class="text-muted mx-1">|</span>
            <span class="text-dark">${now.toLocaleTimeString('en-US', timeOptions)}</span>
        `;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>