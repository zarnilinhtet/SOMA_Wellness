<style>
    /* --- Elegant Heading Area --- */
    .package-header h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 3rem;
        color: var(--text-dark, #2b2b2b);
        font-weight: 500;
    }

    .package-header p {
        color: var(--soma-taupe, #706e6b);
        font-size: 1.05rem;
        letter-spacing: 0.3px;
    }


    /* --- Search Box --- */
    .search-box {
        position: relative;
        width: 100%;
        max-width: 350px;
        margin: 0 auto;
        /* Added to center horizontally */
    }

    .search-box i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--soma-taupe, #706e6b);
        opacity: 0.7;
        pointer-events: none;
        /* Prevents icon from blocking input clicks */
    }

    .search-box input {
        width: 100%;
        padding: 12px 20px 12px 45px;
        border: 1px solid rgba(190, 150, 118, 0.2);
        border-radius: 50px;
        font-size: 0.95rem;
        color: var(--text-dark, #2b2b2b);
        background: #f8f6f2;
        outline: none;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        background: #ffffff;
        border-color: var(--soma-beige, #c49a72);
        box-shadow: 0 4px 15px rgba(190, 150, 118, 0.1);
    }

    .search-box input::placeholder {
        color: #999;
    }

    /* --- Scrollable Filter Container --- */
    .filter-scroll-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        /* Smooth scrolling on iOS */
        scrollbar-width: none;
        /* Firefox */
        padding-bottom: 5px;
        display: flex;
        justify-content: center;
        /* Center on desktop */
    }

    .filter-scroll-container::-webkit-scrollbar {
        display: none;
        /* Chrome/Safari */
    }

    .filter-group {
        display: inline-flex;
        background: #f8f6f2;
        padding: 0.5rem;
        border-radius: 50px;
        border: 1px solid rgba(190, 150, 118, 0.1);
        white-space: nowrap;
    }

    .filter-btn {
        text-decoration: none !important;
        padding: 0.75rem 2rem;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--soma-taupe, #706e6b);
        border-radius: 50px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .filter-btn.active {
        text-decoration: none !important;
        background: white;
        color: var(--text-dark, #2b2b2b);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    /* --- MOBILE VIEW SCROLL FIX --- */
    @media (max-width: 767px) {
        .filter-scroll-container {
            justify-content: flex-start;
            /* Start align so left side isn't cut off */
            padding-left: 15px;
            /* Add spacing on edges */
            padding-right: 15px;
        }

        .filter-group {
            flex-shrink: 0;
            /* Prevent the container from shrinking */
        }
    }

    /* --- Package Cards Modded --- */
    .package-card {
        background: #ffffff;
        border: 1px solid rgba(190, 150, 118, 0.15);
        border-radius: 12px;
        padding: 35px 24px;
        box-shadow: 0 4px 20px rgba(190, 150, 118, 0.03);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .package-card:hover {
        transform: translateY(-6px);
        border-color: var(--soma-beige, #c49a72);
        box-shadow: 0 12px 30px rgba(190, 150, 118, 0.08);
    }

    .package-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--text-dark, #2b2b2b);
        margin-bottom: 4px;
    }

    .package-type {
        font-size: 11px;
        color: var(--soma-beige, #a67c52);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
    }

    .package-desc {
        font-size: 14px;
        line-height: 1.6;
        color: #6e6e6e;
        margin: 20px 0;
        flex-grow: 1;
    }

    /* --- Loyal Point Box --- */
    .loyal-box {
        background: rgba(245, 230, 211, 0.25);
        border: 1px dashed rgba(190, 150, 118, 0.4);
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        margin-bottom: 18px;
    }

    .loyal-box small {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #8c6b4f;
        display: block;
        margin-bottom: 2px;
    }

    .loyal-box .points {
        font-weight: 600;
        font-size: 15px;
        color: var(--text-dark, #2b2b2b);
    }

    /* --- Minimalist Badges --- */
    .status-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f4f4f4;
        color: #777;
    }

    .status-badge.active {
        background: #eef9f1;
        color: #1e7e34;
    }

    /* --- Real App Pricing & Discount Styles --- */
    .price-wrap {
        margin: 20px 0;
    }

    .original-price {
        font-size: 16px;
        color: #999;
        text-decoration: line-through;
        font-family: 'Cormorant Garamond', serif;
        margin-bottom: -4px;
    }

    .discount-badge-inline {
        display: inline-block;
        background: #eef9f1;
        color: #1e7e34;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 4px;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .price {
        font-size: 32px;
        font-family: 'Cormorant Garamond', serif;
        font-weight: 600;
        color: var(--text-dark, #2b2b2b);
        line-height: 1;
    }

    .price span {
        font-size: 18px;
        vertical-align: super;
        margin-right: 2px;
    }

    .price-wrap small {
        font-size: 12px;
        color: #999;
        display: block;
        margin-top: 4px;
    }

    /* --- Buttons --- */
    .btn-premium {
        background-color: var(--soma-taupe, #706e6b);
        color: #ffffff;
        border-radius: 50px;
        padding: 12px 30px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-premium:hover {
        background-color: var(--soma-beige, #c49a72);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-full {
        width: 100%;
        padding: 12px 16px;
    }

    /* --- Clean Pagination --- */
    .soma-pagination-wrap {
        display: flex;
        justify-content: center;
        margin-top: 4rem;
    }

    .soma-pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .soma-pagination li a,
    .soma-pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        border-radius: 50%;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid rgba(190, 150, 118, 0.2);
        background: transparent;
        color: var(--soma-taupe, #706e6b);
        transition: all 0.3s ease;
    }

    .soma-pagination li a:hover {
        background: var(--soma-beige, #c49a72);
        color: white;
        border-color: var(--soma-beige, #c49a72);
    }

    .soma-pagination li.active span {
        background: var(--soma-beige, #c49a72);
        color: white;
        border-color: var(--soma-beige, #c49a72);
    }

    .soma-pagination li.disabled span {
        opacity: 0.3;
        cursor: not-allowed;
    }

    #package-results {
        scroll-margin-top: 100px;
    }

    .discount-expiration {
        font-size: 11px;
        color: #d9534f;
        font-weight: 600;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- MAIN PRODUCT SECTION -->
<div class="bg-section py-5">
    <div class="container py-4">

        <div class="text-center package-header mb-5">
            <h2>Membership Packages</h2>
            <p>Choose a plan that fits your journey to inner peace</p>
        </div>

        <!-- NEW SEARCH & FILTER CONTROLS AREA -->
        <div class="controls-wrapper mb-3">
            <!-- Search Box -->
            <div class="w-100 my-3">
                <div class="search-box w-100" style="max-width: 60%;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search packages...">
                </div>
            </div>

            <!-- Scrollable Filter -->
            <div class="filter-scroll-container">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">All</button>
                    @foreach($categories as $category)
                        <button class="filter-btn"
                            data-filter="{{ str_replace(' ', '-', strtolower($category->name)) }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 justify-content-center" id="package-results">
            @foreach($packages as $package)
                <div
                    class="col-12 col-sm-6 col-lg-4 col-xl-3 package-card-wrapper package-{{ str_replace(' ', '-', strtolower($package->category->name ?? '')) }}">
                    <div class="package-card text-center">

                        <div class="mb-3">
                            <span class="status-badge {{ $package->status == 'active' ? 'active' : '' }}">
                                {{ $package->status }}
                            </span>
                        </div>

                        <h3 class="package-title">{{ $package->name }}</h3>
                        <div class="package-type">
                            @php
                                $months = floor($package->duration / 30);
                                $days = $package->duration % 30;

                                $output = [];
                                if ($months > 0) {
                                    $output[] = $months . " month(s)";
                                }
                                if ($days > 0) {
                                    $output[] = $days . " day(s)";
                                }

                                $duration = !empty($output) ? implode(" and ", $output) : "0 day(s)";
                            @endphp

                            {{ $duration }} - {{ $package->class_count ?? '-' }} classes
                        </div>
                        <div>{{ $package->category->name ?? '-' }}</div>

                        <div
                            style="width: 30px; height: 1px; background-color: rgba(190, 150, 118, 0.25); margin: 15px auto;">
                        </div>

                        <div class="loyal-box">
                            <small>Earn Reward</small>
                            <div class="points">✨ {{ $package->loyal_point }} points</div>
                        </div>

                        <!-- DISCOUNT CALCULATION LOGIC -->
                        @php
                            $isPackageDiscounted = false;
                            $originalPrice = $package->price;
                            $finalPrice = $originalPrice;
                            $discountValue = 0;
                            $discountExpiration = null; // Store expiration date/time

                            if (auth()->check() && isset($userDiscountPackages)) {
                                // Find a valid discount record for this specific package
                                $discountRecord = $userDiscountPackages->first(function ($discount) use ($package, &$discountExpiration) {
                                    if ($discount->package_id != $package->id) {
                                        return false;
                                    }

                                    // Combine expiration date and time if available
                                    if ($discount->expiration_date) {
                                        $expTime = $discount->expiration_time ?? '23:59:59';
                                        $expiresAt = \Carbon\Carbon::parse($discount->expiration_date . ' ' . $expTime);

                                        if ($expiresAt->isFuture()) {
                                            $discountExpiration = $expiresAt;
                                            return true;
                                        }
                                        return false;
                                    }

                                    return true; // No expiration date means it's always valid
                                });

                                if ($discountRecord && $discountRecord->discount_amount > 0) {
                                    $isPackageDiscounted = true;
                                    $discountValue = $discountRecord->discount_amount;

                                    // Calculate final price (assuming discount_amount is percentage)
                                    $finalPrice = $originalPrice - ($originalPrice * ($discountValue / 100));
                                }
                            }
                        @endphp

                        <!-- PRICE DISPLAY SECTION -->
                        <div class="price-wrap">
                            @if($isPackageDiscounted)
                                <span class="discount-badge-inline">{{ $discountValue }}% OFF</span>
                                <div class="original-price"><span>K</span>{{ number_format($originalPrice) }}</div>
                                <div class="price" style="color: #b33939;"><span>K</span>{{ number_format($finalPrice) }}</div>

                                @if($discountExpiration)
                                    <div class="discount-expiration">
                                        <i class="far fa-clock"></i>
                                        Expires: {{ $discountExpiration->format('M d, Y h:i A') }}
                                    </div>
                                @endif
                            @else
                                <div class="price"><span>K</span>{{ number_format($originalPrice) }}</div>
                            @endif
                            <small>per package</small>
                        </div>

                        <!-- ACTION BUTTON -->
                        @if($package->status == 'active')
                            <button class="btn-premium btn-full" onclick="
                                                @guest
                                                    window.location.href='{{ route('login') }}';
                                                @endguest

                                                @auth
                                                    @if(isset($buttonAction))
                                                        {{ $buttonAction }}('Monthly', this)
                                                    @elseif(auth()->user()->coins >= $package->loyal_point)
                                                        buyPackage({{ $package->id }})
                                                    @else
                                                        window.location.href='{{ route('payment.page', ['id' => $package->id]) }}'
                                                    @endif
                                                @endauth">
                                Buy Now
                            </button>
                        @else
                            <button class="btn-premium btn-full" style="background-color: #cccccc; color: #666666;" disabled>
                                Unavailable
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if (method_exists($packages, 'lastPage') && $packages->lastPage() > 0)
            <div class="soma-pagination-wrap">
                <ul class="soma-pagination">

                    @if ($packages->onFirstPage())
                        <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li>
                            <a href="{{ $packages->previousPageUrl() . '#package-results' }}"><i
                                    class="fas fa-chevron-left"></i></a>
                        </li>
                    @endif

                    @foreach ($packages->getUrlRange(1, $packages->lastPage()) as $page => $url)
                        @if ($page == $packages->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url . '#package-results' }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($packages->hasMorePages())
                        <li>
                            <a href="{{ $packages->nextPageUrl() . '#package-results' }}"><i
                                    class="fas fa-chevron-right"></i></a>
                        </li>
                    @else
                        <li class="disabled"><span><i class="fas fa-chevron-right"></i></span></li>
                    @endif

                </ul>
            </div>
        @endif

    </div>
</div>

<script>
    function buyPackage(classId) {
        Swal.fire({
            title: 'Buy Package',
            text: 'Choose your payment method.',
            icon: 'question',

            showCancelButton: true,
            showDenyButton: true,

            confirmButtonText: 'Redeem Coin',
            denyButtonText: 'Digital Payment',
            cancelButtonText: 'Cancel',

            confirmButtonColor: '#BE9676',
            denyButtonColor: '#28a745',
            cancelButtonColor: '#999'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/buy/package/viacoin/${classId}`;
            } else if (result.isDenied) {
                window.location.href = `/payment/${classId}`;
            }
        });
    }

    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const packageCards = document.querySelectorAll('.package-card-wrapper');

    let activeCategory = 'all';
    let searchQuery = '';

    function filterPackages() {
        packageCards.forEach(card => {
            const title = card.querySelector('.package-title').innerText.toLowerCase();
            const matchesCategory = activeCategory === 'all' || card.classList.contains('package-' + activeCategory);
            const matchesSearch = title.includes(searchQuery);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            activeCategory = this.getAttribute('data-filter');
            filterPackages();

            const targetSection = document.getElementById('package-results');
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    searchInput.addEventListener('input', function (e) {
        searchQuery = e.target.value.toLowerCase().trim();
        filterPackages();
    });
</script>