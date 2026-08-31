@extends('layouts.link')

@section('content')

    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* --- Brand Variables & Fonts --- */
        :root {
            --soma-primary: #BE9676; /* Perfect Beige */
            --soma-secondary: #8D7E71; /* Desert Taupe */
            --soma-bg: #FFF7E9; /* Soft Cream */
            --soma-card: #FFFFFF;
        }

        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea, table, th, td, label, ul, li {
            font-family: 'Fahkwang', sans-serif !important;
        }

        body {
            background: var(--soma-bg) !important;
            -webkit-tap-highlight-color: transparent;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-title {
            color: var(--soma-secondary);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .text-primary {
            color: var(--soma-primary) !important;
        }

        /* --- Modern Premium Tab Switcher --- */
        .tabs-wrapper {
            display: flex;
            background: rgba(141, 126, 113, 0.15);
            padding: 4px;
            border-radius: 12px;
            max-width: 320px;
            margin-bottom: 20px;
            position: relative;
        }

        .tab-btn {
            flex: 1;
            border: none;
            background: transparent;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--soma-secondary);
            cursor: pointer;
            border-radius: 9px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            z-index: 1;
        }

        .tab-btn.active {
            color: #ffffff;
            background: var(--soma-secondary);
            box-shadow: 0 4px 10px rgba(141, 126, 113, 0.2);
        }

        /* Sticky Modern Search Bar Container */
        .search-container {
            position: sticky;
            top: 12px;
            z-index: 100;
            margin-bottom: 24px;
            background: rgba(255, 247, 233, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 4px 0;
        }

        .search-box-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 100%;
            padding: 14px 44px;
            font-size: 15px;
            background: #ffffff;
            border: 1px solid rgba(141, 126, 113, 0.3);
            border-radius: 14px;
            color: var(--soma-secondary);
            box-shadow: 0 4px 10px rgba(141, 126, 113, 0.03);
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--soma-primary);
            box-shadow: 0 10px 15px -3px rgba(190, 150, 118, 0.15);
        }

        .search-icon-left {
            position: absolute;
            left: 16px;
            color: rgba(141, 126, 113, 0.6);
            font-size: 16px;
            pointer-events: none;
        }

        .empty-state {
            background: #ffffff;
            padding: 40px 24px;
            border-radius: 16px;
            border: 1px dashed rgba(141, 126, 113, 0.3);
            text-align: center;
            margin-top: 10px;
        }
        
        .empty-state .text-dark {
            color: var(--soma-secondary) !important;
        }
    </style>

    <div class="container lg:p-5 p-2 mt-5 mb-5">

        <div class="page-header">
            <h2 class="page-title">My Purchases</h2>
            <div class="text-primary mt-3">
                <strong>***Note:</strong> <small>Packages are consumed in the order they were purchased.
                    The oldest package listed below is your currently active package.</small>
            </div>
        </div>

        {{-- Added Search Container UI --}}
        <div class="search-container">
            <div class="search-box-wrapper">
                <i class="bi bi-search search-icon-left"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Search packages or classes..." autocomplete="off">
            </div>
        </div>

        {{-- Dynamic Active Class based on backend state --}}
        <div class="tabs-wrapper">
            <button class="tab-btn {{ (!isset($tab) || $tab === 'rates') ? 'active' : '' }}" onclick="switchTab('rates', this)">
                <i class="bi bi-percent"></i> Package
            </button>
            <button class="tab-btn {{ (isset($tab) && $tab === 'class') ? 'active' : '' }}" onclick="switchTab('class', this)">
                <i class="bi bi-journal-bookmark"></i> Class
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible mt-3 fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mt-3 fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="resultsWrapper">
            {{-- Pass the dynamic $tab variable down to the child component --}}
            @include('frontend.history_list', ['tab' => $tab ?? 'rates'])
        </div>

        <div id="noMatchState" class="empty-state" style="display: none;">
            <div class="fs-3 mb-2">🔍</div>
            <div class="text-dark fw-bold mb-1">No matching orders</div>
            <p class="text-muted small mb-0">Try checking your spelling or typing a different term.</p>
        </div>

    </div>

    <script>
        let searchTimeout = null;

        document.addEventListener('DOMContentLoaded', function () {
            let serverTab = "{{ $tab ?? 'rates' }}";
            localStorage.setItem('activeTab', serverTab);
        });

        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            let query = this.value;
            let currentTab = localStorage.getItem('activeTab') || 'rates';

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('history.page') }}?search=${query}&tab=${currentTab}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('resultsWrapper').innerHTML = html;
                    });
            }, 500); 
        });

        function switchTab(category, btn) {
            localStorage.setItem('activeTab', category);
            
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', category);
            window.history.pushState({}, '', newUrl);

            const query = document.getElementById('searchInput').value;
            fetch(`{{ route('history.page') }}?search=${query}&tab=${category}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('resultsWrapper').innerHTML = html;
                });
        }
    </script>

@endsection