<body>
<div class="wrapper">

    <div class="sidebar" data-background-color="dark">

        <div class="sidebar-logo">

            <div class="logo-header" data-background-color="dark">

                <a href="{{ url('dashboard') }}"
                   class="logo text-white"
                   style="font-family: 'Montserrat', sans-serif; font-size: 1.25rem; text-decoration: none;">
                    SOMA DASHBOARD
                </a>

                <div class="nav-toggle d-flex align-items-center">

                    <button
                        class="btn btn-toggle toggle-sidebar d-none d-lg-flex align-items-center justify-content-center border-0 shadow-sm rounded-circle p-2"
                        style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); transition: all 0.3s;">

                        <i class="gg-menu-right text-white"
                           style="transform: scale(1.2);">
                        </i>

                    </button>

                    <button
                        class="btn btn-toggle sidenav-toggler d-flex d-lg-none align-items-center justify-content-center border-0 shadow-sm rounded-circle p-2 ms-2"
                        style="width: 45px; height: 45px; background-color: rgba(255,255,255,0.15); transition: all 0.3s;">

                        <i class="gg-menu-left text-white"
                           style="transform: scale(1.4);">
                        </i>

                    </button>

                </div>

                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>

            </div>

        </div>


        <div class="sidebar-wrapper scrollbar scrollbar-inner">

            <div class="sidebar-content">

                <ul class="nav nav-secondary">


                    {{-- ===================================================== --}}
                    {{-- DASHBOARD --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('dashboard.view')

                        <li class="nav-item active">

                            <a href="{{ url('dashboard') }}">

                                <i class="fas fa-home"></i>

                                <p>
                                    Dashboard
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- WORKSHOP --}}
                    {{-- ADMIN + RECEPTIONIST --}}
                    {{-- ===================================================== --}}

                    @can('workshops.view')

                        <li class="nav-item">

                            <a href="{{ url('workshops') }}">

                                <i class="fa-solid fa-industry"></i>

                                <p>
                                    Workshop
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- CLOSE DATE --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('close_dates.view')

                        <li class="nav-item">

                            <a href="{{ url('close-dates') }}">

                                <i class="fa-solid fa-circle-minus"></i>

                                <p>
                                    Close Date
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- COMMENTS --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('comments.view')

                        <li class="nav-item">

                            <a href="{{ url('comments/approve') }}">

                                <i class="fa-solid fa-comment"></i>

                                <p>
                                    Comments
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- GALLERY --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('gallery.view')

                        <li class="nav-item">

                            <a href="{{ url('gallery') }}">

                                <i class="fa-regular fa-images"></i>

                                <p>
                                    Gallery
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- CLASSES --}}
                    {{-- ADMIN + RECEPTIONIST + INSTRUCTOR --}}
                    {{-- ===================================================== --}}

                    @if(
                        Auth::user()->can('class_schedules.view') ||
                        Auth::user()->can('bookings.view') ||
                        Auth::user()->can('waitlists.view')
                    )

                        <li class="nav-item">

                            <a data-bs-toggle="collapse"
                               data-bs-target="#classesModule"
                               href="javascript:void(0);">

                                <i class="far fa-credit-card"></i>

                                <p>
                                    Classes
                                </p>

                                <span class="caret"></span>

                            </a>


                            <div class="collapse" id="classesModule">

                                <ul class="nav nav-collapse">


                                    {{-- CLASS SCHEDULES --}}

                                    @can('class_schedules.view')

                                        <li>

                                            <a href="{{ url('class-schedules') }}">

                                                <span class="sub-item">
                                                    Class Schedules
                                                </span>

                                            </a>

                                        </li>

                                    @endcan


                                    {{-- CANCEL --}}

                                    @can('bookings.view')

                                        <li>

                                            <a href="{{ route('bookings.index') }}">

                                                <span class="sub-item">
                                                    Class Booking
                                                </span>

                                            </a>

                                        </li>

                                    @endcan


                                    {{-- WAITING LISTS --}}

                                    @can('waitlists.view')

                                        <li>

                                            <a href="{{ route('waitlist.index') }}">

                                                <span class="sub-item">
                                                    Waiting Lists
                                                </span>

                                            </a>

                                        </li>

                                    @endcan


                                </ul>

                            </div>

                        </li>

                    @endif



                    {{-- ===================================================== --}}
                    {{-- ATTENDANCE --}}
                    {{-- ADMIN + RECEPTIONIST + INSTRUCTOR --}}
                    {{-- ===================================================== --}}

                    @can('attendance.view')

                        <li class="nav-item">

                            <a href="{{ url('attendances') }}">

                                <i class="fa-solid fa-user-clock"></i>

                                <p>
                                    Attendance
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- ATTENDANCE RECORD --}}
                    {{-- ===================================================== --}}

                    @can('attendance.record')

                        <li class="nav-item">

                            <a href="{{ url('record') }}">

                                <i class="fa-solid fa-grip"></i>

                                <p>
                                    Attendance Record
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- INSTRUCTOR ONLY --}}
                    {{-- BOOKING REQUESTS --}}
                    {{-- ===================================================== --}}

                    @if(Auth::user()->hasRole('Instructor'))

                        @can('bookings.view')

                            <li class="nav-item">

                                <a href="{{ route('bookings.index') }}">

                                    <i class="far fa-credit-card"></i>

                                    <p>
                                        Booking Requests
                                    </p>

                                </a>

                            </li>

                        @endcan


                        @can('instructor_reports.record')

                            <li class="nav-item">

                                <a href="{{ url('get/instructor/it/report') }}">

                                    <i class="fa-regular fa-file-lines"></i>

                                    <p>
                                        Instructor Report
                                    </p>

                                </a>

                            </li>

                        @endcan

                    @endif



                    {{-- ===================================================== --}}
                    {{-- PAYMENTS --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('payments.view')

                        <li class="nav-item">

                            <a href="{{ url('payments') }}">

                                <i class="fa-solid fa-dollar-sign"></i>

                                <p>
                                    Payments
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- INSTRUCTORS --}}
                    {{-- ADMIN + RECEPTIONIST --}}
                    {{-- ===================================================== --}}

                    @can('instructors.view')

                        <li class="nav-item">

                            <a href="{{ url('instructors') }}">

                                <i class="fa-solid fa-chalkboard-user"></i>

                                <p>
                                    Instructors
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- CATEGORIES --}}
                    {{-- ADMIN + RECEPTIONIST --}}
                    {{-- ===================================================== --}}

                    @can('categories.view')

                        <li class="nav-item">

                            <a href="{{ route('categories.index') }}">

                                <i class="fa-solid fa-list"></i>

                                <p>
                                    Categories
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- PACKAGES --}}
                    {{-- ADMIN + RECEPTIONIST --}}
                    {{-- ===================================================== --}}

                    @can('packages.view')

                        <li class="nav-item">

                            <a href="{{ route('packages.index') }}">

                                <i class="fas fa-box-open"></i>

                                <p>
                                    Package
                                </p>

                            </a>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- USERS --}}
                    {{-- ADMIN + RECEPTIONIST --}}
                    {{-- ===================================================== --}}

                    @can('users.view')

                        <li class="nav-item">

                            <a data-bs-toggle="collapse"
                               data-bs-target="#usersModule"
                               href="javascript:void(0);">

                                <i class="fas fa-users-cog"></i>

                                <p>
                                    Users
                                </p>

                                <span class="caret"></span>

                            </a>


                            <div class="collapse" id="usersModule">

                                <ul class="nav nav-collapse">

                                    <li>

                                        <a href="{{ route('user.with_packages') }}">

                                            <span class="sub-item">
                                                User With Packages
                                            </span>

                                        </a>

                                    </li>


                                    <li>

                                        <a href="{{ route('user_register.index') }}">

                                            <span class="sub-item">
                                                User Register
                                            </span>

                                        </a>

                                    </li>

                                </ul>

                            </div>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- TRANSACTIONS --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('transactions.view')

                        <li class="nav-item">

                            <a data-bs-toggle="collapse"
                               data-bs-target="#transactionsModule"
                               href="javascript:void(0);">

                                <i class="far fa-credit-card"></i>

                                <p>
                                    Transactions
                                </p>

                                <span class="caret"></span>

                            </a>


                            <div class="collapse" id="transactionsModule">

                                <ul class="nav nav-collapse">


                                    <li>

                                        <a href="{{ route('purchases.manage.page') }}">

                                            <span class="sub-item">
                                                Package Purchases
                                            </span>

                                        </a>

                                    </li>


                                    {{-- <li>

                                        <a href="{{ route('bookings.index') }}">

                                            <span class="sub-item">
                                                Cancel
                                            </span>

                                        </a>

                                    </li> --}}

{{-- 
                                    <li>

                                        <a href="{{ route('waitlist.index') }}">

                                            <span class="sub-item">
                                                Waiting Lists
                                            </span>

                                        </a>

                                    </li> --}}


                                </ul>

                            </div>

                        </li>

                    @endcan



                    {{-- ===================================================== --}}
                    {{-- REPORTS --}}
                    {{-- ADMIN ONLY --}}
                    {{-- ===================================================== --}}

                    @can('reports.view')

                        <li class="nav-item">

                            <a data-bs-toggle="collapse"
                               data-bs-target="#reportsModule"
                               href="javascript:void(0);">

                                <i class="fa-regular fa-file-lines"></i>

                                <p>
                                    Reports
                                </p>

                                <span class="caret"></span>

                            </a>


                            <div class="collapse" id="reportsModule">

                                <ul class="nav nav-collapse">


                                    <li>

                                        <a href="{{ route('customer.report') }}">

                                            <span class="sub-item">
                                                Customer Report
                                            </span>

                                        </a>

                                    </li>


                                    <li>

                                        <a href="{{ route('instructor.report') }}">

                                            <span class="sub-item">
                                                Instructor Report
                                            </span>

                                        </a>

                                    </li>


                                    {{-- TOP PACKAGES --}}

                                    {{--
                                    <li>

                                        <a href="{{ route('top.packages') }}">

                                            <span class="sub-item">
                                                Top Packages
                                            </span>

                                        </a>

                                    </li>
                                    --}}


                                    <li>

                                        <a href="{{ route('montly.sales') }}">

                                            <span class="sub-item">
                                                Monthly Sales Analysis
                                            </span>

                                        </a>

                                    </li>


                                </ul>

                            </div>

                        </li>

                    @endcan


                </ul>

            </div>

        </div>

    </div>


    <div class="main-panel">

        <div class="main-header">

        </div>
