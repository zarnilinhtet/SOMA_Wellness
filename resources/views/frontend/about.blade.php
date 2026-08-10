<style>
    .tall-img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        border-radius: 0 0 0 45px;
        border: 2px solid rgba(190, 150, 118, 0.3);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
    }

    .tall-img:hover {
        transform: scale(1.03);
    }

    .about-first {
        background: linear-gradient(135deg, var(--soma-cream) 0%, #ffffff 100%);
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: 40px 0 60px;
    }

    /* Staggered effect for desktop only */
    @media (min-width: 768px) {
        .about-first {
            padding: 80px 0;
        }

        .staggered-down {
            transform: translateY(24px);
        }
    }
</style>

<!-- ABOUT SECTION -->
<div class="about-first">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-12 col-md-6">
                <h1 class="fw-bolder mb-3" style="font-size: clamp(2rem, 4vw, 3rem);">About SOMA</h1>
                <p style="color:#8D7E71; line-height: 1.8; font-size: 1.05rem;" class="m-0">
                    Born from a deep passion for holistic movement, SOMA is more than just a studio; it is a modern
                    sanctuary crafted for your physical and mental well-being. We believe that physical conditioning and
                    mental clarity go hand in hand to create lasting harmony.
                </p>
            </div>

            <div class="col-12 col-md-6 text-center">
                <img src="{{ asset('assets/img/about-img.png') }}" class="img-fluid rounded-4" alt="About SOMA">
            </div>
        </div>
    </div>
</div>

<!-- IMAGE + TEXT SECTION -->
<div class="container py-5 my-2 my-md-4">
    <div class="row align-items-center g-4 g-lg-5">

        <div class="col-12 col-sm-6 col-md-4">
            <img src="{{ asset('assets/img/yo.jpg') }}" class="img-fluid tall-img" alt="SOMA Yoga Flow">
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <img src="{{ asset('assets/img/yoga-man.jpg') }}" class="img-fluid tall-img staggered-down"
                alt="SOMA Instructor">
        </div>

        <div class="col-12 col-md-4 mt-4 mt-md-0">
            <h2 class="mb-3" style="font-size: clamp(1.75rem, 3vw, 2.25rem);">Create a healthy life you love!</h2>

            <h4 class="mb-3" style="color: var(--soma-taupe, #8D7E71); font-size: 1.15rem; line-height: 1.5;">
                True wellness is not a destination, but a beautiful daily ritual.
            </h4>

            <p style="color:#8D7E71; line-height: 1.7;">
                At SOMA, we provide the space, the community, and the expert guidance to help you craft a lifestyle that
                nourishes both your body and mind.
            </p>

            <p style="color:#8D7E71; line-height: 1.7;" class="m-0">
                Step into our beautifully designed space, leave the stress behind, and transform your daily routine into
                a meaningful ritual of self-care and strength.
            </p>
        </div>

    </div>
</div>

<!-- TESTIMONIAL SECTION (Uncomment when ready) -->
<!-- 
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-3" style="font-size: clamp(1.75rem, 3vw, 2.5rem);">Real stories of transformation from the SOMA community</h2>
        <p class="col-12 col-md-8 col-lg-6 mx-auto text-muted">
            As you pour the first glass of your favorite Chianti or Chardonnay and settle into an intimate Friday evening.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="text-center shadow-sm p-4 rounded-4 border bg-white h-100">
                <img src="{{ asset('assets/img/profile.jpg') }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">
                <p class="text-muted m-0">
                    Accessories Here you can find the best computer accessory for your laptop, monitor, printer, scanner, speaker, projector, hardware and more.
                </p>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="text-center shadow-sm p-4 rounded-4 border bg-white h-100">
                <img src="{{ asset('assets/img/sauro.jpg') }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">
                <p class="text-muted m-0">
                    Accessories Here you can find the best computer accessory for your laptop, monitor, printer, scanner, speaker, projector, hardware and more.
                </p>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="text-center shadow-sm p-4 rounded-4 border bg-white h-100">
                <img src="{{ asset('assets/img/profile2.jpg') }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">
                <p class="text-muted m-0">
                    Accessories Here you can find the best computer accessory for your laptop, monitor, printer, scanner, speaker, projector, hardware and more.
                </p>
            </div>
        </div>
    </div>
</div> 
-->