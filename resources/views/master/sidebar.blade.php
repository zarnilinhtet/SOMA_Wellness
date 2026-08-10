<body>
    <div class="wrapper">

        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="{{ url('dashboard') }}" class="logo text-white"
                        style="font-family: 'Montserrat', sans-serif; font-size: 1.25rem; text-decoration: none;">
                        SOMA DASHBOARD
                    </a>

                    <div class="nav-toggle d-flex align-items-center">
                        <button
                            class="btn btn-toggle toggle-sidebar d-none d-lg-flex align-items-center justify-content-center border-0 shadow-sm rounded-circle p-2"
                            style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); transition: all 0.3s;">
                            <i class="gg-menu-right text-white" style="transform: scale(1.2);"></i>
                        </button>

                        <button
                            class="btn btn-toggle sidenav-toggler d-flex d-lg-none align-items-center justify-content-center border-0 shadow-sm rounded-circle p-2 ms-2"
                            style="width: 45px; height: 45px; background-color: rgba(255,255,255,0.15); transition: all 0.3s;">
                            <i class="gg-menu-left text-white" style="transform: scale(1.4);"></i>
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
                        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Receptionist'))

                            @if(Auth::user()->hasRole('Admin'))
                                <li class="nav-item active">
                                    <a href="{{ url('dashboard') }}">
                                        <i class="fas fa-home"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item ">
                                <a href="{{ url('workshops') }}">
                                    <i class="fa-solid fa-industry"></i>
                                    <p>Workshop</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('close-dates') }}">
                                    <i class="fa-solid fa-circle-minus"></i>
                                    <p>Close Date</p>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="{{ url('comments/approve') }}">
                                    <i class="fa-solid fa-comment"></i>
                                    <p>Comments</p>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="{{ url('gallery') }}">
                                    <i class="fa-regular fa-images"></i>
                                    <p>Gallery</p>
                                </a>
                            </li>
                        @endif
                       
                         <li class="nav-item">
                                <a data-bs-toggle="collapse" data-bs-target="#classesModule" href="javascript:void(0);">
                                    <i class="far fa-credit-card"></i>
                                    <p>Classes</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="classesModule">
                                    <ul class="nav nav-collapse">
                                        {{-- <li><a href="{{ route('user_types.index') }}"><span class="sub-item">User Type</span></a></li> --}}
                                        <li><a href="{{ url('class-schedules') }}"><span class="sub-item">Class Schedules</span></a></li>
                                        <li><a href="{{ route('bookings.index') }}"><span class="sub-item">Cancel</span></a></li>
                                        <li><a href="{{ route('waitlist.index') }}"><span class="sub-item">Waiting Lists</span></a></li>
                                    </ul>
                                </div>
                            </li>
                        <li class="nav-item">
                            <a href="{{ url('attendances') }}">
                                <i class="fa-solid fa-user-clock"></i>
                                <p>Attendance</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ url('record') }}">
                                <i class="fa-solid fa-grip"></i>
                                <p>Attendacne Record</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item ">
                            <a href="{{ url('/get/instructor/earnings') }}">
                                <i class="fa-solid fa-coins"></i>
                                <p>Instructor Earnings</p>
                            </a>
                        </li> -->
                        @if(Auth::user()->hasRole('Instructor'))
                            <li class="nav-item ">
                                <a href="{{ route('bookings.index') }}">
                                    <i class="far fa-credit-card"></i>
                                    <p>Booking Requests</p>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="{{ url('get/instructor/it/report') }}">
                                    <i class="fa-regular fa-file-lines"></i>
                                    <p>Instructor Report</p>
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->hasRole('Admin'))
                            <li class="nav-item ">
                                <a href="{{ url('payments') }}">
                                    <i class="fa-solid fa-dollar-sign"></i>
                                    <p>Payments</p>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Receptionist'))

                            <li class="nav-item ">
                                <a href="{{ url('instructors') }}">
                                    <i class="fa-solid fa-chalkboard-user"></i>
                                    <p>Instructors</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('categories.index') }}">
                                    <i class="fa-solid fa-list"></i>
                                    <p>Categories</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('packages.index') }}">
                                    <i class="fas fa-box-open"></i>
                                    <p>Package</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a data-bs-toggle="collapse" data-bs-target="#usersModule" href="javascript:void(0);">
                                    <i class="fas fa-users-cog"></i>
                                    <p>Users</p>
                                    <span class="caret"></span>
                                </a>
                                
                                <div class="collapse" id="usersModule">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('user.with_packages') }}"><span class="sub-item">User With Packages</span></a></li>
                                        <li><a href="{{ route('user_register.index') }}"><span class="sub-item">User Register</span></a></li>
                                    </ul>
                                </div>
                                
                            </li>
                        @endif
                        @if (Auth::user()->hasRole('Admin'))
                            <li class="nav-item">
                                <a data-bs-toggle="collapse" data-bs-target="#transactionsModule" href="javascript:void(0);">
                                    <i class="far fa-credit-card"></i>
                                    <p>Transactions</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="transactionsModule">
                                    <ul class="nav nav-collapse">
                                        {{-- <li><a href="{{ route('user_types.index') }}"><span class="sub-item">User Type</span></a></li> --}}
                                        <li><a href="{{ route('purchases.manage.page') }}"><span class="sub-item">Package Purchases</span></a></li>
                                        <li><a href="{{ route('bookings.index') }}"><span class="sub-item">Cancel</span></a></li>
                                        <li><a href="{{ route('waitlist.index') }}"><span class="sub-item">Waiting Lists</span></a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a data-bs-toggle="collapse" data-bs-target="#reportsModule" href="javascript:void(0);">
                                    <i class="fa-regular fa-file-lines"></i>
                                    <p>Reports</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="reportsModule">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('customer.report') }}"><span class="sub-item">Customer Report</span></a></li>
                                        <li><a href="{{ route('instructor.report') }}"><span class="sub-item">Instructor Report</span></a></li>
                                        <!--<li><a href="{{ route('top.packages') }}"><span class="sub-item">Top Packages</span></a></li>-->
                                        <li><a href="{{ route('montly.sales') }}"><span class="sub-item">Monthly Sales Analysis</span></a></li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
            </div>