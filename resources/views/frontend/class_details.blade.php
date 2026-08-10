@extends('layouts.link')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom layout layers */
        .custom-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .custom-modal-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            outline: 0;
            z-index: 1050;
            display: block;
        }

        /* --- Innovative Detail Layout Configuration --- */
        .details-page-wrapper {
            background-color: #FAF8F5;
            /* Fluid organic luxury cream tint */
            min-height: 100vh;
            padding: 120px 0 20px 0;
        }

        /* Minimal Floating Back Anchor Button */
        .soma-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--soma-taupe, #706e6b);
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            margin-bottom: 2.5rem;
        }

        .soma-back-link:hover {
            color: var(--soma-beige, #c49a72);
            transform: translateX(-4px);
        }

        /* Split Architecture Frame */
        .showcase-grid-canvas {
            background: #ffffff;
            border: 1px solid rgba(190, 150, 118, 0.12);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(190, 150, 118, 0.05);
        }

        /* Media Immersive Gallery Column Container */
        .gallery-hero-panel {
            position: relative;
            height: 100%;
            min-height: 580px;
            background: var(--soma-cream, #FAF7F2);
        }

        /* Slider Engine Component Framework */
        .soma-carousel,
        .carousel-inner,
        .carousel-item {
            height: 100%;
            min-height: 580px;
        }

        .primary-showcase-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Minimalist Underpart Pagination Dots Reset */
        .carousel-indicators.soma-carousel-indicators {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            list-style: none;
            padding: 0;
            margin: 0;
            z-index: 10;
            width: auto;
            /* Overrides Bootstrap's default 100% width stretch */
        }

        /* Force standard round dots instead of Bootstrap's default dash bars */
        .carousel-indicators.soma-carousel-indicators button {
            box-sizing: content-box;
            flex: 0 1 auto;
            width: 8px !important;
            height: 8px !important;
            padding: 0;
            margin-right: 0;
            margin-left: 0;
            text-indent: -999px;
            cursor: pointer;
            background-color: #ffffff !important;
            border-radius: 50% !important;
            border: none !important;
            opacity: 0.4 !important;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Scale & illuminate the active state indicator seamlessly */
        .carousel-indicators.soma-carousel-indicators button.active {
            opacity: 1 !important;
            transform: scale(1.4) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
        }

        /* Information Sheet Composition */
        .meta-content-panel {
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .category-premium-pill {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--soma-beige, #c49a72);
            margin-bottom: 1.25rem;
        }

        .meta-content-panel h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.2rem;
            font-weight: 500;
            color: var(--text-dark, #2b2b2b);
            line-height: 1.15;
            margin-bottom: 1.5rem;
        }

        .class-narrative {
            font-size: 1rem;
            line-height: 1.75;
            color: var(--soma-taupe, #64625f);
            margin-bottom: 2.5rem;
        }

        /* Non-traditional Meta Parameters Grid */
        .parameter-matrix-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            border-top: 1px solid rgba(190, 150, 118, 0.15);
            padding-top: 30px;
            margin-bottom: 2.5rem;
        }

        .matrix-node {
            display: flex;
            flex-direction: column;
        }

        .matrix-node .node-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a19f9c;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .matrix-node .node-value {
            font-size: 0.95rem;
            color: var(--text-dark, #2b2b2b);
            font-weight: 500;
        }

        /* Sticky Bottom Checkout Hub */
        .checkout-action-deck {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FDFDFD;
            border: 1px solid rgba(190, 150, 118, 0.15);
            padding: 16px 24px;
            border-radius: 16px;
        }

        .financial-metric {
            display: flex;
            flex-direction: column;
        }

        .financial-metric span {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--soma-taupe, #706e6b);
        }

        .financial-metric strong {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            color: var(--text-dark, #2b2b2b);
            line-height: 1;
        }

        .btn-premium-action {
            background-color: var(--soma-dark, #2B2927);
            color: #ffffff;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border: none;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-premium-action:hover {
            background-color: var(--soma-beige, #c49a72);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(196, 154, 114, 0.25);
        }

        .btn-premium-action.btn-waitlist {
            background-color: #64625f;
        }

        .btn-premium-action.btn-waitlist:hover {
            background-color: #2b2b2b;
        }

        .btn-cancel {
            background: #e74c3c;
            color: #ffffff;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border: none;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-joined {
            background: #cbd5e1;
            color: #ffffff;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border: none;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-cancel:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {

            .gallery-hero-panel,
            .soma-carousel,
            .carousel-inner,
            .carousel-item {
                min-height: 400px;
            }

            .meta-content-panel {
                padding: 35px 24px;
            }

            .meta-content-panel h1 {
                font-size: 2.4rem;
            }

            .parameter-matrix-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .status-pill {
                font-size: 10px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .pill-approved {
                background-color: #e6f9f3;
                color: #10b981;
            }

            .pill-waitlist {
                background-color: #fef3c7;
                color: #d97706;
            }

            .pill-cancelled {
                background-color: #fee2e2;
                color: #ef4444;
            }
        }
    </style>

    <div class="details-page-wrapper">
        <div class="container">

            <!-- NAVIGATIONAL ESCAPE ANCHOR -->
            <a href="{{ url()->previous() }}" class="soma-back-link">
                <i class="fas fa-arrow-left small"></i> Return to Directory
            </a>

            <!-- MAIN SPLIT WORKSPACE INTERFACE -->
            <div class="showcase-grid-canvas">
                <div class="row g-0">

                    <!-- ASYMMETRICAL LEFT IMAGERY ENGINE PANEL -->
                    <div class="col-12 col-lg-6">
                        <div class="gallery-hero-panel">

                            @if(!empty($class->image_2))
                                <div id="somaImageSlider" class="carousel slide soma-carousel" data-bs-ride="carousel"
                                    data-bs-interval="4000">

                                    <!-- FIXED PAGINATION DOTS WITH ACCURATE BOOTSTRAP ATTRIBUTES AND RESET CLASSES -->
                                    <div class="carousel-indicators soma-carousel-indicators">
                                        <button type="button" data-bs-target="#somaImageSlider" data-bs-slide-to="0"
                                            class="active" aria-current="true" aria-label="Slide 1"></button>
                                        <button type="button" data-bs-target="#somaImageSlider" data-bs-slide-to="1"
                                            aria-label="Slide 2"></button>
                                    </div>

                                    <!-- IMAGES COVERS CAROUSEL -->
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="{{ asset($class->image_1) }}" class="primary-showcase-img"
                                                alt="{{ $class->class_name }}">
                                        </div>
                                        <div class="carousel-item">
                                            <img src="{{ asset($class->image_2) }}" class="primary-showcase-img"
                                                alt="Studio Space LOOK">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Fallback static frame rendering if single image layout exists -->
                                <img src="{{ asset($class->image_1 ?? 'assets/img/doyoga_about_2.jpg') }}"
                                    class="primary-showcase-img" alt="{{ $class->class_name }}">
                            @endif

                        </div>
                    </div>

                    <!-- RIGHT CONTENT MATRIX SHEET -->
                    <div class="col-12 col-lg-6">
                        <div class="meta-content-panel">

                            <span class="category-premium-pill">
                                {{ $class->category->name }}
                            </span>

                            <h1>{{ $class->class_name }}</h1>

                            <div class="class-narrative">
                                {!! $class->description !!}
                            </div>

                            <!-- INNOVATIVE GRID ATTRIBUTE METER -->
                            <div class="parameter-matrix-grid">

                                <div class="matrix-node">
                                    <span class="node-label">Studio Guide</span>
                                    @foreach ($class->instructor as $inst)
                                        <span class="node-value">{{ $inst['user']['name'] }}</span>
                                    @endforeach
                                </div>

                                <div class="matrix-node">
                                    <span class="node-label">Daily Window</span>
                                    <span class="node-value">{{ $class->start_time }} - {{ $class->end_time }}</span>
                                </div>

                                <div class="matrix-node">
                                    <span class="node-label">Calendar Block</span>
                                    <span class="node-value">
                                        {{ \Carbon\Carbon::parse($class->start_date)->format('d M') }} —
                                        {{ \Carbon\Carbon::parse($class->end_date)->format('d M Y') }}
                                    </span>
                                </div>

                                <div class="matrix-node">
                                    <span class="node-label">Registration</span>
                                    <span class="node-value d-flex align-items-center gap-2">
                                        <span class="d-inline-block rounded-circle"
                                            style="width: 7px; height: 7px; background-color: #1e7e34;"></span>
                                        {{ $class->status == 'ongoing' ? 'Open for Enrollment' : ($class->status == 'completed' ? 'Completed' : 'Closed') }}
                                    </span>
                                </div>

                                <!-- INTEGRATED PROGRESS BAR OCCUPANCY TRACKER -->
                                <div class="matrix-node"
                                    style="grid-column: span 2; background: #FAF8F5; padding: 16px; border-radius: 12px; border: 1px dashed rgba(190, 150, 118, 0.25); margin-top: 10px;">

                                    @php
                                        // Math computation variables for real-time calculation
                                        $totalCapacity = $class->capacity;
                                        $bookedSlots = $bookingCount; // This variable is passed from the controller as the count of confirmed bookings
                                        $remainingSlots = max(0, $totalCapacity - $bookedSlots);

                                        // Smooth percentage conversion computation handling zero variables gracefully
                                        $percentFilled = $totalCapacity > 0 ? ($bookedSlots / $totalCapacity) * 100 : 0;
                                    @endphp

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="node-label mb-0" style="font-size: 0.68rem;">Studio Occupancy
                                            Matrix</span>

                                        @if($remainingSlots <= 3 && $remainingSlots > 0)
                                            <span class="badge"
                                                style="background: #FFF0ED; color: #D9381E; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                Filling Fast </span>
                                        @elseif($remainingSlots == 0)
                                            <span class="badge"
                                                style="background: #F4F4F4; color: #777777; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                Fully Booked </span>
                                        @else
                                            <span class="badge"
                                                style="background: rgba(196, 154, 114, 0.15); color: var(--soma-dark, #2B2927); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                Open Space </span>
                                        @endif
                                    </div>

                                    <!-- Visual Progress Meter Frame -->
                                    <div class="progress"
                                        style="height: 5px; background-color: rgba(190, 150, 118, 0.15); border-radius: 10px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $percentFilled }}%; background-color: {{ $remainingSlots <= 3 ? '#D9381E' : 'var(--soma-beige, #c49a72)' }}; border-radius: 10px; transition: width 0.6s ease;">
                                        </div>
                                    </div>

                                    <!-- Metric Parameters Counters -->
                                    <div class="d-flex justify-content-between mt-2"
                                        style="font-size: 11px; font-weight: 500;">
                                        <span style="color: var(--soma-taupe, #706e6b);">
                                            <strong>{{ $bookedSlots }}</strong> spaces already booked
                                        </span>
                                        <span
                                            style="color: {{ $remainingSlots <= 3 ? '#D9381E' : '#1e7e34' }}; font-weight: 600;">
                                            @if($remainingSlots == 0)
                                                Join waitlist
                                            @else
                                                Only {{ $remainingSlots }} left of {{ $totalCapacity }} capacity spots
                                            @endif
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <!-- ACTION TRANSACTION HUBS -->
@auth
    @php
        $confirmedBooking = $bookings->where('selected_class_id', $class->id)->where('status', 'confirmed')->first();
        $waitlistedBooking = $bookings->where('selected_class_id', $class->id)->where('status', 'waitlisted')->first();
        $cancelledBooking = $bookings->where('selected_class_id', $class->id)->where('status', 'cancelled')->first();

        // Parse class start date and time to check 48-hour cancellation threshold
        $classStart = \Carbon\Carbon::parse($class->start_date . ' ' . $class->start_time);
        $canCancel = now()->diffInHours($classStart, false) >= 48;
    @endphp

    <div class="checkout-action-deck">
        @if ($class->status == 'ongoing')
            @if ($confirmedBooking)
                {{-- USER IS JOINED: Check if cancellation window is open --}}
                @if ($canCancel)
                    <button class="btn-premium-action btn-cancel" 
                            title="Cancel Class" 
                            data-bs-toggle="modal"
                            data-bs-target="#cancelModal{{ $class->id }}" 
                            onClick="cancelClass({{ $confirmedBooking->id }})">
                        Cancel Class
                    </button>
                @endif

                <span class="status-pill pill-approved">
                    Joined
                </span>

                {{-- CANCELLATION MODAL --}}
                <!-- <div class="modal fade" id="cancelModal{{ $class->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('remove.class', $class->id) }}" method="POST" class="modal-content text-start">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fw-bold">
                                    <i class="fas fa-ban me-2"></i> Cancel Class Reservation
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p class="small text-muted mb-3">
                                    Are you sure you want to cancel your spot for <strong>{{ $class->class_name }}</strong>?
                                </p>
                                <label class="fw-bold mb-2">
                                    Reason for Cancellation <span class="text-danger">*</span>
                                </label>
                                <textarea name="cancellation_reason" class="form-control" rows="3" required
                                    placeholder="Please provide a reason for cancellation."></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                            </div>
                        </form>
                    </div>
                </div> -->

            @elseif ($waitlistedBooking)
                {{-- USER IS WAITLISTED --}}
                <span class="status-pill pill-waitlist">Waitlisted</span>

            @elseif ($cancelledBooking)
                {{-- USER CANCELLED PREVIOUSLY --}}
                <span class="status-pill pill-cancelled">
                    Cancelled By {{ $cancelledBooking->byWho ?? 'User' }}
                </span>

            @elseif ($remainingSlots == 0)
                {{-- CLASS IS FULL: SHOW WAITLIST --}}
                <a href="#" class="btn-premium-action btn-waitlist">
                    Join Waitlist
                </a>

            @else
                {{-- AVAILABLE: SHOW JOIN BUTTON --}}
                <a onClick="joinClass({{ $class->id }})" class="btn-premium-action">
                    Join Class <i class="fas fa-chevron-right ms-2 small"></i>
                </a>
            @endif
        @endif
    </div>
@endauth

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="container" x-data="replyModalHandler()">

            <div class="border-0 p-5  mx-auto my-4" style=" background-color: #ffffff; border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold border-bottom pb-2 mb-3 text-dark" style="font-size: 15px;">
                        All Comments ({{ $comments->count() }})
                    </h5>

                    @auth
                        <div class="d-flex gap-2 mb-4">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle text-muted d-flex align-items-center justify-content-center fw-bold"
                                    style="width: 40px; height: 40px; background-color: #e4e6eb;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <form action="{{ route('post.comment') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $class->id }}">
                                    <div class="position-relative border-0 rounded-4 p-2" style="background-color: #f0f2f5;">
                                        <textarea name="content"
                                            class="form-control bg-transparent border-0 pt-1 pb-5 px-2 shadow-none text-dark"
                                            placeholder="Write a comment..." rows="2" style="resize: none; font-size: 14px;"
                                            required></textarea>
                                        <div class="position-absolute bottom-0 end-0 p-2">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold"
                                                style="font-size: 13px; background-color: #1877f2; border: none;">Comment</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div>
                            
                            </div>
                        </div>
                    @if($waitingApproval)
    @foreach($waitingApproval as $approval)
        <div class="card border-warning-subtle bg-light mb-3 shadow-sm rounded-3">
            {{-- Status Banner --}}
            <div class="card-header bg-warning-subtle border-0 py-2 d-flex align-items-center justify-content-between">
                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                    <i class="fas fa-hourglass-half me-1"></i> Pending Review
                </span>
                <small class="text-warning-emphasis fw-semibold">Waiting for admin approval</small>
            </div>

            {{-- Comment Content --}}
            <div class="card-body py-3">
                <p class="mb-0 text-secondary fst-italic">"{{ $approval->content }}"</p>
            </div>
        </div>
    @endforeach
@endif
                    @endauth

                    <div class="comments-list">
                        @foreach($comments as $comment)
                            @include('frontend.comment', ['comment' => $comment])
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="custom-backdrop" x-show="isOpen" x-transition.opacity x-cloak></div>

            <div class="custom-modal-wrapper" x-show="isOpen" x-cloak>

                <div class="modal-dialog modal-dialog-centered mx-auto"
                    style="max-width: 500px; margin-top: 10% !important;" @click.away="isOpen = false">
                    <div class="modal-content border-0 shadow" style="border-radius: 14px; background: #ffffff;">

                        <div class="modal-header border-bottom-0 pb-0 pt-3 px-3">
                            <h6 class="modal-title fw-bold text-dark" style="font-size: 15px;">
                                Reply to <span x-text="replyingToUser" class="text-primary"></span>
                            </h6>
                            <button type="button" class="btn-close shadow-none" @click="isOpen = false"></button>
                        </div>

                        <form action="{{ route('post.comment') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $class->id }}">
                            <input type="hidden" name="parent_id" :value="parentCommentId">

                            <div class="modal-body pt-2 px-3">
                                <div class="p-2 rounded-3 mb-3 text-muted border-start border-3"
                                    style="background-color: #f8f9fa; font-size: 13px; border-color: #1877f2 !important;">
                                    <em x-text="'&ldquo;' + parentContentPreview + '&rdquo;'"></em>
                                </div>

                                <div class="d-flex gap-2 align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle text-muted d-flex align-items-center justify-content-center fw-semibold"
                                            style="width: 32px; height: 32px; font-size: 12px; background-color: #e4e6eb;">
                                            You
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <textarea name="content" class="form-control border p-2 text-dark shadow-none"
                                            placeholder="Write a public reply..." rows="3"
                                            style="font-size: 14px; background-color: #f0f2f5; border-radius: 10px; resize: none;"
                                            required x-ref="replyInput"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 mt-2 pt-0 pb-3 px-3 justify-content-end">
                                <button type="button" class="btn btn-light btn-sm fw-bold rounded-pill px-3"
                                    @click="isOpen = false" style="font-size: 13px;">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-pill px-4"
                                    style="font-size: 13px; background-color: #1877f2; border: none;">
                                    Reply
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                confirmButtonColor: '#BE9676'
            });
        </script>
    @endif

    @if(session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Waiting List',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#BE9676'
            });
        </script>
    @endif

    <!-- FORCE CAROUSEL MANUAL JS INITIALIZATION BINDING ENGINE -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var myCarouselEl = document.getElementById('somaImageSlider');
            if (myCarouselEl) {
                // Instantiates Bootstrap 5 core slider explicitly over the DOM element
                var carousel = new bootstrap.Carousel(myCarouselEl, {
                    interval: 4000,
                    ride: 'carousel',
                    wrap: true
                });
            }
        });

        function joinClass(classId) {

            Swal.fire({
                title: 'Join Class',
                text: 'Are you sure you want to join this class?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BE9676',
                cancelButtonColor: '#999',
                confirmButtonText: 'Yes, Join',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/join/class/${classId}`;
                }
            });
        }

        function replyModalHandler() {
            return {
                isOpen: false,
                parentCommentId: null,
                replyingToUser: '',
                parentContentPreview: '',

                openReplyModal(commentId, username, contentText) {
                    this.parentCommentId = commentId;
                    this.replyingToUser = username;
                    this.parentContentPreview = contentText;
                    this.isOpen = true;

                    // Automatically direct user focus cursor immediately inside textbox
                    this.$nextTick(() => {
                        this.$refs.replyInput.focus();
                    });
                }
            }
        }

        function cancelClass(classId) {
            Swal.fire({
                title: 'Cancel Class',
                text: 'Are you sure you want to cancel this class?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BE9676',
                cancelButtonColor: '#999',
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/remove/class/${classId}`;
                }
            });
        }
    </script>

@endsection