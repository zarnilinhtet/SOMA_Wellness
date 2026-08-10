<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SOMA - Yoga Studio</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #EAE3DA; 
            --card-bg: #FAF8F5; 
            --text-color: #5A4D42; 
            --input-bg: #EBE2D6; 
            --btn-bg: #67574B; 
            --lotus-color: rgba(189, 175, 161, 0.4); 
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--primary-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
            color: var(--text-color);
        }

        .serif-font {
            font-family: 'Playfair Display', serif;
        }

        /* Main Card Container */
        .split-card {
            background-color: var(--card-bg);
            border-radius: 2rem;
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
        }

        /* Left Side (Image Panel) */
        .left-pane {
            background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3)), 
                        url("{{ asset('assets/img/soma_login.jpg') }}");
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 550px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
            letter-spacing: 2px;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        /* Right Side (Form Panel) */
        .right-pane {
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            position: relative;
            background-color: var(--card-bg);
        }

        /* Form Layout Styles */
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
            z-index: 2;
        }

        .form-header h1 {
            font-size: 3rem;
            margin-bottom: 0;
            color: var(--text-color);
            letter-spacing: 1px;
        }

        .form-header p {
            font-size: 0.9rem;
            font-weight: 300;
            letter-spacing: 0.5px;
            color: var(--text-color);
        }

        .lotus-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            z-index: 1;
            pointer-events: none;
        }

        .login-form {
            position: relative;
            z-index: 2;
            max-width: 320px;
            margin: 0 auto;
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label-custom {
            font-weight: 400;
            font-size: 0.85rem;
            color: var(--text-color);
            margin-bottom: 0.3rem;
            padding-left: 1rem;
        }

        .custom-input {
            background-color: var(--input-bg) !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            border-radius: 50px !important;
            padding: 0.75rem 1.2rem !important;
            font-size: 0.9rem;
            color: var(--text-color);
            box-shadow: none !important;
        }

        .custom-input::placeholder {
            color: rgba(90, 77, 66, 0.5);
            font-weight: 300;
        }

        .btn-brown {
            background-color: var(--btn-bg);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            font-weight: 400;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-brown:hover {
            background-color: #52443a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .footer-text {
            position: absolute;
            bottom: 1.5rem;
            right: 2rem;
            font-size: 0.75rem;
            font-weight: 300;
            color: var(--text-color);
        }

        /* =========================================
           MOBILE RESPONSIVE UPGRADES 
           ========================================= */
        @media (min-width: 768px) {
            .left-pane { width: 55%; }
            .right-pane { width: 45%; }
        }
        
        @media (max-width: 767px) {
            body {
                padding: 0; /* Remove padding to make it a full-screen app feel */
                background-color: var(--card-bg);
            }
            .split-card {
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
                align-content: flex-start;
            }
            .left-pane {
                width: 100%;
                min-height: 280px;
                padding: 2rem 1.5rem;
                justify-content: flex-end; /* Pushes SOMA text closer to the form */
            }
            .hero-title {
                font-size: 2.8rem;
            }
            .right-pane {
                width: 100%;
                padding: 2.5rem 1.5rem 2rem;
                border-top-left-radius: 2rem; /* Rounds only the top corners */
                border-top-right-radius: 2rem;
                margin-top: -2.5rem; /* OVERLAP EFFECT: Slides the form over the image */
                z-index: 10;
                flex-grow: 1;
                box-shadow: 0 -10px 25px rgba(0,0,0,0.05); /* Adds a soft shadow above the form */
            }
            .form-header h1 {
                font-size: 2.5rem;
            }
            .footer-text {
                position: relative;
                bottom: 0;
                right: 0;
                text-align: center;
                margin-top: 3rem;
                padding-bottom: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="split-card">
        
        <div class="left-pane">
            <h1 class="hero-title serif-font">SOMA</h1>
            <p class="hero-subtitle">Find your Soma. Find your Peace.</p>
        </div>

        <div class="right-pane">
            {{ $slot }}
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>