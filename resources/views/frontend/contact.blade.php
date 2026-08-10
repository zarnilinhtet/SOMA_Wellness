@extends('layouts.link')

@section('content')
    <style>
        /* --- General Spacing & Background --- */
        .contact-section {
            background-color: var(--soma-cream);
            padding-top: 5rem;
            padding-bottom: 5rem;
        }

        /* --- Typography --- */
        .section-title {
            font-family: 'Cormorant Garamond', serif;
            color: var(--text-dark);
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .sub-title {
            font-family: 'Cormorant Garamond', serif;
            color: var(--soma-taupe);
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        p.about-text {
            color: var(--soma-taupe);
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 3rem;
        }

        /* --- Contact Info List --- */
        .contact-info li {
            font-size: 1.05rem;
            color: var(--soma-taupe);
            display: flex;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .contact-info i {
            color: var(--soma-beige);
            font-size: 1.2rem;
            width: 30px;
            /* Aligns the icons perfectly */
        }

        /* --- Premium Form Styling --- */
        .form-premium {
            background-color: #ffffff;
            border: 1px solid rgba(190, 150, 118, 0.2);
            border-radius: 8px;
            padding: 14px 20px;
            font-size: 0.95rem;
            color: var(--text-dark);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .form-premium::placeholder {
            color: #b5aba2;
            font-weight: 300;
        }

        .form-premium:focus {
            outline: none;
            border-color: var(--soma-beige);
            box-shadow: 0 0 0 4px rgba(190, 150, 118, 0.15);
            background-color: #ffffff;
        }

        /* --- Button --- */
        .btn-premium {
            background-color: var(--soma-taupe);
            color: #fff;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border: none;
            transition: all 0.4s ease;
            display: inline-block;
            margin-top: 1rem;
        }

        .btn-premium:hover {
            background-color: var(--soma-beige);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(190, 150, 118, 0.3);
        }

        /* --- Map Container --- */
        .map-wrapper {
            background-color: #ffffff;
            padding: 5rem 0;
            border-top: 1px solid rgba(190, 150, 118, 0.15);
        }

        .map-container {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(141, 126, 113, 0.1);
            border: 1px solid rgba(190, 150, 118, 0.15);
            height: 450px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <div class="contact-section">
        <div class="container">
            <div class="row g-5 justify-content-between">

                <div class="col-12 col-lg-5">
                    <div>
                        <h1 class="section-title">About SOMA</h1>
                        <p class="about-text">
                            Born from a deep passion for holistic movement, SOMA is more than just a studio; it is a modern sanctuary crafted for your physical and mental well-being. We believe that physical conditioning and mental clarity go hand in hand to create lasting harmony.
                        </p>
                    </div>

                    <div class="mt-5">
                        <h2 class="sub-title">Information</h2>
                        <ul class="list-unstyled contact-info">
                            <li><i class="fas fa-map-marker-alt"></i> No.110, 27th Street, Between 76th & 77th, Mandalay, Myanmar</li>
                            <li><i class="fas fa-envelope"></i> somawellness.mm@gmail.com</li>
                            <li><i class="fas fa-phone"></i>  092001407, 092002407 </li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="bg-white p-5 rounded-4 shadow-sm border"
                        style="border-color: rgba(190, 150, 118, 0.15) !important;">
                        <h2 class="sub-title mb-4" style="color: var(--text-dark);">Get in touch!</h2>

                        <form method="post" action="{{ route('mail.sent') }}">
                            @csrf
                            <div class="mb-4">
                                <input type="text" name="name" class="form-control form-premium" id="name"
                                    placeholder="Your Name" required>
                            </div>

                            <div class="mb-4">
                                <input type="email" name="email" class="form-control form-premium" id="email"
                                    placeholder="Your Email" required>
                            </div>

                            <div class="mb-4">
                                <input type="text" name="subject" class="form-control form-premium" id="subject"
                                    placeholder="Subject" required>
                            </div>

                            <div class="mb-4">
                                <textarea class="form-control form-premium" name="message" id="message" rows="6"
                                    placeholder="How can we help you?" required></textarea>
                            </div>

                            <button type="submit" class="btn-premium w-100">Send Message</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="map-wrapper">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="section-title">Our Location</h1>
                <p class="text-muted fs-5">Find your sanctuary in the heart of the city.</p>
            </div>

            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d122223.36427329524!2d96.06456012675662!3d16.838952495632057!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1949e223e196b%3A0x56fbd271f8080bb4!2sYangon%2C%20Myanmar%20(Burma)!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

@endsection