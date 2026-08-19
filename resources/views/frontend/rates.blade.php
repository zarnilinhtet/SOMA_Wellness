@extends('layouts.link')

@section('content')
    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- Brand Variables & Fonts --- */
        :root {
            --soma-primary: #BE9676; /* Perfect Beige */
            --soma-secondary: #8D7E71; /* Desert Taupe */
            --soma-bg: #FFF7E9; /* Soft Cream */
        }

        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea, table, th, td, label, ul, li {
            font-family: 'Fahkwang', sans-serif !important;
        }

        /* --- General Variables & Layout --- */
        .bg-section {
            background-color: var(--soma-bg); /* Updated to match brand background */
            position: relative;
            overflow: hidden;
        }
        /* --- Responsive Structural Triggers --- */
        @media (max-width: 992px) {

            .package-header h2 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {


            .package-header h2 {
                font-size: 2rem;
            }
        }
    </style>

    @include('frontend.package_card', ['packages' => $packages])
@endsection