<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Proyek Terapan')</title>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #E3F5FF;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 100px;
            background: white;
            box-shadow: 1px 4px 5px rgba(0, 0, 0, 0.2);
            border-right: 1px solid #D2C7C7;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            position: sticky;
            top: 0;
            height: 100vh;
            justify-content: flex-start;
        }

        .logo {
            width: 70px;
            height: 70px;
            border-radius: 44px;
            margin-bottom: 40px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-bottom: 26px;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
        }

        .menu-item:hover {
            transform: scale(1.05);
        }

        .menu-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: #E3F5FF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .menu-item span {
            font-size: 18px;
            font-weight: 750;
            text-align: center;
            color: #000;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            border: 1px solid #00456A;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-text h1 {
            color: #2C3E50;
            font-size: clamp(24px, 4vw, 40px);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header-text p {
            color: #2C3E50;
            font-size: clamp(14px, 2vw, 20px);
        }

        .voice-btn {
            background: #00456A;
            color: white;
            padding: 12px 24px;
            border-radius: 100px;
            border: 1px solid rgba(32, 56, 55, 0.5);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.3s;
        }

        .voice-btn:hover {
            background: #003855;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 30px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: #DDE6E6;
            border-radius: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 30px;
        }

        .card-content h3 {
            color: rgba(44, 62, 80, 0.8);
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .card-content .amount {
            font-size: 28px;
            font-weight: 700;
        }

        .card.balance {
            border: 1px solid #00456A;
        }

        .card.balance .amount {
            color: #00456A;
        }

        .card.income {
            border: 1px solid #00670B;
        }

        .card.income .amount {
            color: #00A311;
            font-weight: 800;
        }

        .card.expense {
            border: 1px solid #A61C1C;
        }

        .card.expense .amount {
            color: #ED6363;
            font-weight: 800;
        }

        .card.target .progress-bar {
            width: 100%;
            height: 8px;
            background: #D9D9D9;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 10px;
        }

        .card.target .progress-fill {
            height: 100%;
            background: #6B9BD1;
            width: 50%;
        }

        /* Bottom Section */
        .bottom-section {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0px 0px 50px rgba(0, 0, 0, 0.2);
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .chart-section h2,
        .transactions-section h2 {
            color: rgba(44, 62, 80, 0.8);
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Chart */
        .chart-container {
            position: relative;
            padding: 20px;
            border: 1px solid #203838;
            border-radius: 20px;
            box-shadow: 1px 3px 4px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pie-chart {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            position: relative;
            margin-bottom: 20px;
        }

        .chart-label {
            position: absolute;
            top: 50%;
            left: 50%;
            font-size: 26px;
            font-weight: 800;
            transform: translate(-50%, -50%) rotate(var(--angle))
                    translate(70px) rotate(calc(-1 * var(--angle)));
            white-space: nowrap;
        }

        .chart-label-income { color: #FFFFFF; text-shadow: 0 1px 2px rgba(0,0,0,0.3); }
        .chart-label-expense { color: #FFFFFF; text-shadow: 0 1px 2px rgba(0,0,0,0.3); }

        .chart-legend { display: flex; gap: 30px; font-size: 16px; font-weight: 800; }
        .legend-item { display: flex; align-items: center; gap: 8px; }
        .legend-color { width: 12px; height: 13px; }
        .legend-color.income { background: #00A311; }
        .legend-color.expense { background: #ED6363; }

        .transaction-list { display: flex; flex-direction: column; gap: 12px; max-height: 260px; overflow-y: auto; padding-right: 4px; }
        .transaction-list::-webkit-scrollbar { width: 6px; }
        .transaction-list::-webkit-scrollbar-track { background: transparent; }
        .transaction-list::-webkit-scrollbar-thumb { background: #C4C4C4; border-radius: 10px; }

        .transaction-item { background: #E3F5FF; padding: 15px 20px; border-radius: 10px; border: 1px solid #203838; box-shadow: 1px 4px 3px 1px rgba(0, 0, 0, 0.15); display: grid; grid-template-columns: 2fr 1fr 1fr; align-items: center; gap: 10px; }
        .transaction-name { font-size: 18px; font-weight: 400; }
        .transaction-date { color: rgba(0, 0, 0, 0.6); font-size: 16px; text-align: center; }
        .transaction-amount { font-size: 18px; font-weight: 800; text-align: right; }
        .transaction-amount.income { color: #00A311; }
        .transaction-amount.expense { color: #ED6363; }

        /* Goal Form Styles */
        .goal-form-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .goal-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }

        .goal-form h3 {
            font-size: 24px;
            font-weight: 700;
            color: #2C3E50;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #DDD;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2A8576;
            box-shadow: 0 0 5px rgba(42, 133, 118, 0.3);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-save,
        .btn-cancel {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-save {
            background: #2A8576;
            color: white;
        }

        .btn-save:hover {
            background: #1C5A50;
        }

        .btn-save:disabled {
            background: #999;
            cursor: not-allowed;
        }

        .btn-cancel {
            background: #EEE;
            color: #333;
        }

        .btn-cancel:hover {
            background: #DDD;
        }

        /* Voice Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            position: relative;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: #2C3E50;
            font-size: 24px;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            color: #999;
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E3F5FF;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            background: #F8FCFF;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #00456A;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 69, 106, 0.1);
            transform: translateY(-1px);
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2300456A' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }

        .form-group select:hover {
            border-color: #6B9BD1;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 100px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #00456A;
            color: white;
        }

        .btn-primary:hover {
            background: #003855;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 69, 106, 0.3);
        }

        .btn-secondary {
            background: #E3F5FF;
            color: #00456A;
            border: 1px solid #00456A;
        }

        .btn-secondary:hover {
            background: #D0E9F5;
        }

        /* Loading Spinner */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 18px;
            font-weight: 600;
        }

        /* Recording Animation */
        .voice-btn.recording {
            background: #ED6363;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(237, 99, 99, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(237, 99, 99, 0);
            }
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 3000;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.active {
            display: flex;
        }

        .toast.success {
            border-left: 4px solid #00A311;
        }

        .toast.error {
            border-left: 4px solid #ED6363;
        }

        .toast-icon {
            font-size: 24px;
        }

        .toast-message {
            font-size: 14px;
            color: #2C3E50;
        }

        @media (max-width: 1024px) { .bottom-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { 
            .sidebar { width: 80px; padding: 15px 0; } 
            .logo { width: 50px; height: 50px; } 
            .menu-item span { font-size: 10px; } 
            .main-content { padding: 15px; } 
            .cards-grid { grid-template-columns: 1fr; } 
            .transaction-item { grid-template-columns: 1fr; text-align: center; } 
            .transaction-date, .transaction-amount { text-align: center; } 
            .goal-form { max-width: 100%; width: 95%; }
            
            /* Voice Button Mobile - Icon Only (Microphone) */
            .voice-btn {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                padding: 0;
                position: fixed;
                top: auto;
                bottom: 24px;
                right: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 6px 20px rgba(0, 69, 106, 0.4);
                z-index: 1000;
                background: #00456A;
            }

            .voice-btn:hover {
                transform: scale(1.05);
                box-shadow: 0 8px 24px rgba(0, 69, 106, 0.5);
            }

            .voice-btn svg {
                width: 24px;
                height: 30px;
                margin: 0;
            }

            .voice-btn-text {
                display: none !important;
            }
        }
    /* Responsive Voice Button */
    @media (max-width: 768px) {
        .voice-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            padding: 0;
            position: fixed;
            top: auto;
            bottom: 24px;
            right: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0, 69, 106, 0.4);
            z-index: 1000;
            background: #00456A;
        }

        .voice-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0, 69, 106, 0.5);
        }

        .voice-btn svg {
            width: 24px;
            height: 30px;
            margin: 0;
        }

        .voice-btn-text {
            display: none !important;
        }
    }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/voica-logo.png') }}" alt="VOICA Logo" style="width: 100%; height: auto; object-fit: contain;">
        </div>

        <a href="{{ route('dashboard') }}" class="menu-item">
            <div class="menu-item-icon">🏠</div>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('budgeting') }}" class="menu-item">
            <div class="menu-item-icon">💰</div>
            <span>Anggaran</span>
        </a>

        <a href="{{ route('goals') }}" class="menu-item">
            <div class="menu-item-icon">🎯</div>
            <span>Target</span>
        </a>

        <a href="{{ route('laporan') }}" class="menu-item">
            <div class="menu-item-icon">📊</div>
            <span>Laporan</span>
        </a>

        <a href="{{ route('settings') }}" class="menu-item">
            <div class="menu-item-icon">⚙️</div>
            <span>Pengaturan</span>
        </a>

        <div class="menu-item" style="margin-top:auto; margin-bottom:20px;">
            <form method="POST" action="{{ route('logout') }}" style="display:contents;">
                @csrf
                <button type="submit" style="background:none; border:none; cursor:pointer; display:flex; flex-direction:column; align-items:center; gap:8px; padding:0;">
                    <div class="menu-item-icon" style="background:#FFE4E4;">🚪</div>
                    <span style="color:#F53003; font-size:12px; font-weight:600;">Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <div class="main-content" id="main-content">
        @yield('main-content')
    </div>


</div>



<script>
// AJAX navigation: fetch page content and replace .main-content, keep sidebar static
function fetchAndSwap(url, push = true) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(resp => {
            if (!resp.ok) throw new Error('Network response was not ok');
            return resp.text();
        })
        .then(html => {
            document.getElementById('main-content').innerHTML = html;
            if (push) history.pushState({ url: url }, '', url);
            // Re-run any inline scripts in the inserted HTML
            Array.from(document.getElementById('main-content').querySelectorAll('script')).forEach(oldScript => {
                const s = document.createElement('script');
                if (oldScript.src) { s.src = oldScript.src; }
                s.textContent = oldScript.textContent;
                document.body.appendChild(s).parentNode.removeChild(s);
            });
        })
        .catch(err => console.error('AJAX navigation error:', err));
}

document.addEventListener('click', function(e) {
    const a = e.target.closest('a.ajax-link');
    if (!a) return;

    // Decide whether to perform SPA-like AJAX swap or full page reload.
    // We'll do a full reload when:
    // - The link has `data-reload` attribute, OR
    // - The navigation target is a different top-level section (first path segment) than current.
    try {
        const targetUrl = new URL(a.href, location.origin);
        const currentSection = (location.pathname.split('/')[1] || '').toLowerCase();
        const targetSection = (targetUrl.pathname.split('/')[1] || '').toLowerCase();

        if (a.hasAttribute('data-reload') || currentSection !== targetSection) {
            // Full navigation (reload). Let browser handle it to ensure a fresh load.
            window.location.href = a.href;
            return;
        }
    } catch (err) {
        // If URL parsing fails for any reason, fall back to AJAX behavior.
        console.warn('URL parse failed, falling back to AJAX navigation', err);
    }

    e.preventDefault();
    fetchAndSwap(a.href, true);
});

window.addEventListener('popstate', function(e) {
    const url = (e.state && e.state.url) || location.pathname;
    fetchAndSwap(url, false);
});
</script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
