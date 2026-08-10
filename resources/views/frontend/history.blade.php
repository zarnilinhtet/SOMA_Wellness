@extends('layouts.link')

@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-title {
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        /* --- Modern Premium Tab Switcher --- */
        .tabs-wrapper {
            display: flex;
            background: #e2e8f0;
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
            color: #64748b;
            cursor: pointer;
            border-radius: 9px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            z-index: 1;
        }

        .tab-btn i {
            font-size: 15px;
        }

        .tab-btn.active {
            color: #0f172a;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
        }

        /* Sticky Modern Search Bar Container */
        .search-container {
            position: sticky;
            top: 12px;
            z-index: 100;
            margin-bottom: 24px;
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 4px 0;
        }

        .search-box-wrapper {
            max-width: 480px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 100%;
            padding: 14px 44px;
            font-size: 15px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #1e293b;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--soma-taupe, #706e6b);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.1);
            background: #ffffff;
        }

        .search-icon-left {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        .clear-search-btn {
            position: absolute;
            right: 14px;
            background: #f1f5f9;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: none;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            padding: 0;
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

        <input type="text" id="searchInput" class="search-input mb-4" placeholder="Search..." autocomplete="off">

        {{-- Dynamic Active Class based on backend state --}}
        <div class="tabs-wrapper">
            <button class="tab-btn {{ (!isset($tab) || $tab === 'rates') ? 'active' : '' }}" onclick="switchTab('rates', this)">
                <i class="bi bi-percent"></i> Package
            </button>
            <button class="tab-btn {{ (isset($tab) && $tab === 'class') ? 'active' : '' }}" onclick="switchTab('class', this)">
                <i class="bi bi-journal-bookmark"></i> Class
            </button>
        </div>

        {{-- Added Error and Warning Session Alerts --}}
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

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible mt-3 fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="resultsWrapper">
            {{-- Pass the dynamic $tab variable down to the child component --}}
            @include('frontend.history_list', ['tab' => $tab ?? 'rates'])
        </div>

        {{-- Global No Matching Result Alerts --}}
        <div id="noMatchState" class="empty-state" style="display: none;">
            <div class="fs-3 mb-2">🔍</div>
            <div class="text-dark fw-bold mb-1">No matching orders</div>
            <p class="text-muted small mb-0">Try checking your spelling or typing a different term.</p>
        </div>

    </div>

    <script>
        let searchTimeout = null;

        // Initialize localStorage with backend tab state on page load
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
            }, 500); // Wait 500ms before searching
        });

        function switchTab(category, btn) {
            localStorage.setItem('activeTab', category);
            
            // Update active styling
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Optionally, update the URL without refreshing the page so reloads keep the tab
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', category);
            window.history.pushState({}, '', newUrl);

            // Refresh content for the new tab
            const query = document.getElementById('searchInput').value;
            fetch(`{{ route('history.page') }}?search=${query}&tab=${category}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('resultsWrapper').innerHTML = html;
                });
        }

        function toggleCard(cardElement, event) {
            if (event && event.target.closest('.card-details-collapsible')) {
                return;
            }

            const currentlyExpanded = document.querySelector('.purchase-card.is-expanded');

            if (currentlyExpanded && currentlyExpanded !== cardElement) {
                currentlyExpanded.classList.remove('is-expanded');
            }
            cardElement.classList.toggle('is-expanded');
        }
    </script>

@endsection