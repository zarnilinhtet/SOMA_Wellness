@extends('layouts.link')

@section('content')
    <style>
        /* --- General Variables & Layout --- */
        .bg-section {
            background-color: #ffffff;
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