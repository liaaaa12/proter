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
            width: 220px;
            background: white;
            box-shadow: 1px 4px 5px rgba(0, 0, 0, 0.2);
            border-right: 1px solid #D2C7C7;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            padding: 20px 15px;
            position: sticky;
            top: 0;
            height: 100vh;
            justify-content: flex-start;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 30px auto;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border-radius: 10px;
        }

        .menu-item:hover {
            background: #F0F9FF;
            transform: translateX(3px);
        }

        .menu-item-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #E3F5FF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .menu-item span {
            font-size: 18px;
            font-weight: 600;
            text-align: left;
            color: #2C3E50;
            flex: 1;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            overflow-x: hidden;
            max-width: 100%;
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

        .card.target {
            border: 1px solid #FF8C00; /* Orange border */
        }

        .card.target .amount {
            color: #FF8C00; /* Orange color for percentage */
            font-weight: 800;
        }

        .card.target .progress-bar {
            width: 100%;
            height: 20px; /* Taller progress bar for better visibility */
            background: #FFE4CC; /* Light orange background */
            border-radius: 20px;
            overflow: hidden;
            margin-top: 10px;
        }

        .card.target .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF8C00 0%, #FFA500 100%); /* Orange gradient */
            border-radius: 20px;
            transition: width 0.3s ease;
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
            .main-content { padding: 10px; max-width: calc(100vw - 80px); } 
            .cards-grid { grid-template-columns: 1fr; } 
            .transaction-item { grid-template-columns: 1fr; text-align: center; } 
            .transaction-date, .transaction-amount { text-align: center; } 
            .goal-form { max-width: 100%; width: 95%; }
            
            /* Bottom Section Mobile */
            .bottom-section {
                padding: 15px;
                margin: 0;
                width: 100%;
                box-sizing: border-box;
            }
            
            .chart-section h2,
            .transactions-section h2 {
                font-size: 18px;
                margin-bottom: 15px;
            }
            
            .chart-container {
                padding: 15px;
                width: 100%;
                box-sizing: border-box;
            }
            
            .pie-chart {
                width: 180px;
                height: 180px;
            }
            
            .chart-label {
                font-size: 20px;
            }
            
            .chart-legend {
                gap: 15px;
                font-size: 14px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .transaction-list {
                max-height: 300px;
            }
            
            .transaction-item {
                padding: 12px 15px;
            }
            
            .transaction-name {
                font-size: 16px;
            }
            
            .transaction-date {
                font-size: 14px;
            }
            
            .transaction-amount {
                font-size: 16px;
            }
            
            /* Header Mobile */
            .header {
                padding: 15px;
                margin-bottom: 15px;
                width: 100%;
                box-sizing: border-box;
            }
            
            /* Cards Mobile */
            .cards-grid {
                gap: 15px;
                margin-bottom: 15px;
                width: 100%;
            }
            
            .card {
                padding: 20px;
                width: 100%;
                box-sizing: border-box;
            }
            
            .card-icon {
                width: 50px;
                height: 50px;
                font-size: 26px;
            }
            
            .card-content h3 {
                font-size: 18px;
            }
            
            .card-content .amount {
                font-size: 24px;
            }
            
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

<!-- Global Voice Modal -->
<div class="modal-overlay" id="voiceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🎤 Transaksi Voice</h2>
            <button class="close-btn" onclick="closeVoiceModal()">&times;</button>
        </div>
        
        <form id="voiceTransactionForm" onsubmit="saveVoiceTransaction(event)">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label for="jenis">Jenis Transaksi *</label>
                    <select id="jenis" name="jenis" required>
                        <option value="">Pilih Jenis</option>
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="kategori">Kategori *</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Hiburan">Hiburan</option>
                        <option value="Kebutuhan">Kebutuhan</option>
                        <option value="Belanja">Belanja</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Gaji">Gaji</option>
                        <option value="Bonus">Bonus</option>
                        <option value="Investasi">Investasi</option>
                        <option value="Tabungan">Tabungan</option>
                        <option value="Sedekah">Sedekah</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="jumlah">Jumlah (Rp) *</label>
                <input type="number" id="jumlah" name="jumlah" placeholder="0" required min="0" step="1">
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan *</label>
                <textarea id="keterangan" name="keterangan" placeholder="Masukkan keterangan transaksi" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="budget">Alokasi Budget (Opsional)</label>
                <select id="budget" name="budget">
                    <option value="">Tidak ada</option>
                    @if(isset($allBudgets))
                        @foreach($allBudgets as $budget)
                            <option value="{{ $budget->id }}">{{ $budget->namaBudget }} ({{ $budget->kategori }})</option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="form-group">
                <label for="goal">Alokasi Tabungan (Opsional)</label>
                <select id="goal" name="goal">
                    <option value="">Tidak ada</option>
                    @if(isset($goals))
                        @foreach($goals as $goal)
                            <option value="{{ $goal->id }}">{{ $goal->namaGoal }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeVoiceModal()">Batal</button>
                <button type="submit" class="btn btn-primary">💾 Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <div class="loading-text" id="loadingText">Memproses audio...</div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.modal-overlay.active {
    display: flex;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h2 {
    color: #00456A;
    font-size: 24px;
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 32px;
    color: #999;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 32px;
    height: 32px;
}

.close-btn:hover {
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 16px;
    font-weight: 600;
    color: #2C3E50;
    margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #DDD;
    border-radius: 8px;
    font-size: 16px;
    font-family: 'Inter', sans-serif;
}

.form-group textarea {
    min-height: 80px;
    resize: vertical;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #00456A;
    box-shadow: 0 0 5px rgba(0, 69, 106, 0.3);
}

.modal-actions {
    display: flex;
    gap: 10px;
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

/* Loading Overlay Styles */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 3000;
}

.loading-overlay.active {
    display: flex;
}

.loading-content {
    text-align: center;
    color: white;
}

.spinner {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid white;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-size: 18px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 20px;
    }
    
    .modal-header h2 {
        font-size: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .form-group label {
        font-size: 15px;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 15px;
        padding: 10px;
    }
    
    .modal-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<script>
function closeVoiceModal() {
    const modal = document.getElementById('voiceModal');
    if (modal) {
        modal.classList.remove('active');
        document.getElementById('voiceTransactionForm').reset();
    }
}

async function saveVoiceTransaction(event) {
    event.preventDefault();
    
    const form = event.target;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const data = {
        jenis: document.getElementById('jenis').value,
        kategori: document.getElementById('kategori').value,
        jumlah: parseFloat(document.getElementById('jumlah').value),
        keterangan: document.getElementById('keterangan').value,
        budget_id: null,
        goal_id: null
    };
    
    try {
        const response = await fetch('/api/voice-transaction', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeVoiceModal();
            
            Swal.fire({
                title: 'Berhasil!',
                text: 'Transaksi berhasil disimpan!',
                icon: 'success',
                confirmButtonColor: '#00456A',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Gagal!',
                text: result.message || 'Gagal menyimpan transaksi',
                icon: 'error',
                confirmButtonColor: '#00456A',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan transaksi',
            icon: 'error',
            confirmButtonColor: '#00456A',
            confirmButtonText: 'OK'
        });
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('voiceModal');
    if (e.target === modal) {
        closeVoiceModal();
    }
});
</script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bottom Navigation Bar (Mobile Only) -->
    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="bottom-nav-icon">🏠</span>
            <span class="bottom-nav-label">Home</span>
        </a>
        <a href="{{ route('budgeting') }}" class="bottom-nav-item {{ request()->routeIs('budgeting') ? 'active' : '' }}">
            <span class="bottom-nav-icon">💰</span>
            <span class="bottom-nav-label">Anggaran</span>
        </a>
        <a href="{{ route('goals') }}" class="bottom-nav-item {{ request()->routeIs('goals') ? 'active' : '' }}">
            <span class="bottom-nav-icon">🎯</span>
            <span class="bottom-nav-label">Target</span>
        </a>
        <a href="{{ route('laporan') }}" class="bottom-nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
            <span class="bottom-nav-icon">📊</span>
            <span class="bottom-nav-label">Laporan</span>
        </a>
        <a href="{{ route('settings') }}" class="bottom-nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
            <span class="bottom-nav-icon">⚙️</span>
            <span class="bottom-nav-label">Akun</span>
        </a>
    </nav>

    <style>
        /* Bottom Navigation Bar */
        .bottom-nav {
            display: none; /* Hidden on desktop */
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 75px;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            padding: 0 5px;
            padding-bottom: env(safe-area-inset-bottom); /* For iPhone notch */
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #999;
            padding: 8px 0;
            flex: 1;
            transition: color 0.2s;
        }

        .bottom-nav-item.active {
            color: #00456A;
        }

        .bottom-nav-item:active {
            background: #F0F9FF;
            border-radius: 10px;
        }

        .bottom-nav-icon {
            font-size: 26px;
            margin-bottom: 4px;
        }

        .bottom-nav-label {
            font-size: 12px;
            font-weight: 600;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none !important; /* Hide sidebar on mobile */
            }

            .bottom-nav {
                display: flex; /* Show bottom nav on mobile */
            }

            .main-content {
                padding: 8px !important;
                padding-bottom: 90px !important; /* Space for bottom nav */
                width: 100% !important;
                max-width: 100% !important;
            }

            .container {
                display: block; /* Remove flex for mobile */
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            body {
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Adjust header for mobile */
            .header {
                padding: 15px !important;
                margin-bottom: 15px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
            }

            .header-text h1 {
                font-size: 22px !important;
            }

            .header-text p {
                font-size: 14px !important;
            }

            /* Voice button - make it circular floating button on mobile */
            .voice-btn {
                position: fixed !important;
                bottom: 95px !important;
                right: 20px !important;
                width: 60px !important;
                height: 60px !important;
                border-radius: 50% !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                z-index: 999 !important;
                box-shadow: 0 4px 15px rgba(0, 69, 106, 0.4) !important;
            }

            /* Hide voice button text on mobile, show only icon */
            .voice-btn-text {
                display: none !important;
            }

            /* Make SVG icon larger in floating button */
            .voice-btn svg {
                width: 28px !important;
                height: 28px !important;
            }

            /* Reduce card padding */
            .card, .settings-card, .budget-card, .goal-card {
                padding: 15px !important;
                border-radius: 15px !important; /* Smaller radius on mobile */
            }

            /* Make all cards full width */
            .card, .settings-card, .budget-card, .goal-card,
            .header, .table-section, .chart-section,
            .summary-cards, .controls-section,
            .bottom-section, .bottom-grid {
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                box-sizing: border-box !important;
                border-radius: 15px !important; /* Smaller radius on mobile */
            }

            /* Bottom grid - stack vertically */
            .bottom-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 15px !important;
            }

            .bottom-section {
                padding: 15px !important;
            }

            /* Cards grid - single column on mobile */
            .cards-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
                width: 100% !important;
            }

            .cards-grid .card {
                width: 100% !important;
                min-width: 0 !important; /* Override min-width from grid */
            }

            /* Budget/Goal cards list - full width */
            .budget-list, .goals-list {
                gap: 10px !important;
            }

            .budget-card, .goal-card {
                width: 100% !important;
            }

            /* Form adjustments */
            .form-row {
                grid-template-columns: 1fr !important;
            }

            .form-group input,
            .form-group select {
                padding: 12px !important;
                font-size: 16px !important; /* Prevent zoom on iOS */
                width: 100% !important;
            }

            /* Table adjustments */
            .table-section {
                padding: 10px !important;
            }

            /* Button adjustments */
            .btn {
                padding: 12px 20px !important;
            }

            /* Summary cards - stack vertically */
            .summary-cards {
                flex-direction: column !important;
                gap: 10px !important;
            }

            .summary-card {
                padding: 12px !important;
                width: 100% !important;
            }

            /* Period filter - stack vertically */
            .period-filter {
                flex-direction: column !important;
                gap: 10px !important;
                width: 100% !important;
            }

            .period-filter select,
            .period-filter button {
                width: 100% !important;
            }

            /* Controls section */
            .controls-section {
                flex-direction: column !important;
                gap: 10px !important;
            }

            /* Add budget/goal button */
            .add-budget-btn, .btn-add-goal {
                width: 100% !important;
            }

            /* Goals actions */
            .goals-actions {
                width: 100% !important;
            }

            .goals-actions .btn-add-goal {
                width: 100% !important;
            }
        }
    </style>

<!-- Global Voice Recognition Script -->
<script>

// Global Voice Recognition - Copied from Dashboard
(function() {
    // Configuration
    const PARSE_API_URL = '/api/parse-voice-text';
    const LARAVEL_API_URL = '/api/voice-transaction';
    
    // Global variables
    let recognition = null;
    let isRecording = false;

    // Initialize Web Speech API
    function initSpeechRecognition() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            showToast('❌ Browser Anda tidak support voice recognition. Gunakan Chrome atau Edge.', 'error');
            return false;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = function() {
            isRecording = true;
            updateVoiceButtonRecording(true);
            showToast('🎤 Mulai berbicara...', 'success');
        };

        recognition.onend = function() {
            isRecording = false;
            updateVoiceButtonRecording(false);
        };

        recognition.onresult = function(event) {
            const text = event.results[0][0].transcript;
            console.log('Speech recognized:', text);
            sendTextToAPI(text);
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            isRecording = false;
            updateVoiceButtonRecording(false);
            
            let errorMessage = 'Terjadi kesalahan saat mengenali suara.';
            
            switch(event.error) {
                case 'no-speech':
                    errorMessage = 'Tidak ada suara terdeteksi. Silakan coba lagi.';
                    break;
                case 'audio-capture':
                    errorMessage = 'Microphone tidak terdeteksi. Pastikan microphone terhubung.';
                    break;
                case 'not-allowed':
                    errorMessage = 'Akses microphone ditolak. Izinkan akses microphone di browser settings.';
                    break;
                case 'network':
                    errorMessage = 'Koneksi internet bermasalah. Speech recognition memerlukan internet.';
                    break;
            }
            
            showToast(errorMessage, 'error');
        };

        return true;
    }

    function startVoiceRecording() {
        if (isRecording) {
            stopVoiceRecording();
            return;
        }
        
        if (!recognition) {
            if (!initSpeechRecognition()) {
                return;
            }
        }
        
        try {
            recognition.start();
        } catch (error) {
            console.error('Error starting recognition:', error);
            showToast('❌ Gagal memulai voice recognition', 'error');
        }
    }

    function stopVoiceRecording() {
        if (recognition && isRecording) {
            recognition.stop();
        }
    }

    function updateVoiceButtonRecording(recording) {
        const voiceBtn = document.querySelector('.voice-btn');
        if (!voiceBtn) return;
        
        const voiceText = voiceBtn.querySelector('.voice-btn-text');
        
        if (recording) {
            voiceBtn.classList.add('recording');
            if (voiceText) voiceText.textContent = '🔴 Merekam... (Klik untuk Stop)';
        } else {
            voiceBtn.classList.remove('recording');
            if (voiceText) voiceText.textContent = 'Transaksi dengan Suara';
        }
    }

    async function sendTextToAPI(text) {
        showLoading('Memproses text...');
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            console.log('Sending text to API:', text);
            console.log('CSRF Token:', csrfToken ? 'Found' : 'NOT FOUND!');
            
            if (!csrfToken) {
                hideLoading();
                showToast('❌ CSRF token tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }
            
            const response = await fetch(PARSE_API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ text: text })
            });
            
            console.log('Response status:', response.status);
            
            if (response.status === 419) {
                hideLoading();
                showToast('❌ Session expired. Silakan refresh halaman (F5).', 'error');
                return;
            }
            
            const result = await response.json();
            console.log('API Response:', result);
            
            hideLoading();
            
            if (result.success) {
                // Open modal first
                openModal();
                
                // Wait for modal to render, then fill form
                setTimeout(() => {
                    autoFillForm(result.data);
                    showToast(`✅ Terdeteksi: "${result.raw_text}"`, 'success');
                }, 100); // Small delay to ensure DOM is ready
            } else {
                showToast(`❌ ${result.message || 'Gagal memproses text'}`, 'error');
            }
            
        } catch (error) {
            hideLoading();
            console.error('Error sending text:', error);
            showToast('❌ Gagal memproses text. Silakan coba lagi.', 'error');
        }
    }

    function autoFillForm(data) {
        console.log('=== AUTO FILL FORM ===');
        console.log('Received data:', data);
        
        const jenisField = document.getElementById('jenis');
        const kategoriField = document.getElementById('kategori');
        const jumlahField = document.getElementById('jumlah');
        const keteranganField = document.getElementById('keterangan');
        
        console.log('Form fields found:', {
            jenis: jenisField ? 'YES' : 'NO',
            kategori: kategoriField ? 'YES' : 'NO',
            jumlah: jumlahField ? 'YES' : 'NO',
            keterangan: keteranganField ? 'YES' : 'NO'
        });
        
        // Fill Jenis
        if (jenisField && data.jenis) {
            jenisField.value = data.jenis;
            console.log('Set jenis:', data.jenis, '→ Actual:', jenisField.value);
        }
        
        // Fill Kategori - with validation
        if (kategoriField && data.kategori) {
            const kategoriValue = data.kategori.trim(); // Remove spaces
            kategoriField.value = kategoriValue;
            
            // Verify if value was set correctly
            if (kategoriField.value === '') {
                console.warn('Kategori not set! Trying to find matching option...');
                // Try to find matching option (case insensitive)
                const options = kategoriField.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value.toLowerCase() === kategoriValue.toLowerCase()) {
                        kategoriField.selectedIndex = i;
                        console.log('Found matching option at index:', i);
                        break;
                    }
                }
            }
            console.log('Set kategori:', kategoriValue, '→ Actual:', kategoriField.value);
        }
        
        // Fill Jumlah - ensure it's a number
        if (jumlahField && data.jumlah) {
            const jumlahValue = parseInt(data.jumlah) || 0;
            jumlahField.value = jumlahValue;
            console.log('Set jumlah:', jumlahValue, '→ Actual:', jumlahField.value);
            
            // Force update if not set
            if (jumlahField.value != jumlahValue) {
                jumlahField.setAttribute('value', jumlahValue);
                jumlahField.value = jumlahValue;
                console.log('Forced jumlah update');
            }
        }
        
        // Fill Keterangan
        if (keteranganField && data.keterangan) {
            keteranganField.value = data.keterangan;
            console.log('Set keterangan:', data.keterangan, '→ Actual:', keteranganField.value);
        }
        
        console.log('=== FORM FILLED ===');
        console.log('Final form values:', {
            jenis: jenisField?.value,
            kategori: kategoriField?.value,
            jumlah: jumlahField?.value,
            keterangan: keteranganField?.value
        });
    }

    function openModal() {
        const modal = document.getElementById('voiceModal');
        if (modal) {
            modal.classList.add('active');
            // Budgets and goals are now loaded server-side via Blade
        }
    }

    function closeModal() {
        const modal = document.getElementById('voiceModal');
        if (modal) {
            modal.classList.remove('active');
            const form = document.getElementById('voiceTransactionForm');
            if (form) form.reset();
        }
    }

    async function loadBudgetsAndGoals() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        try {
            // Load budgets
            const budgetResponse = await fetch('/api/budgets', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (budgetResponse.ok) {
                const budgetData = await budgetResponse.json();
                const budgetSelect = document.getElementById('budget');
                
                if (budgetSelect && budgetData.success) {
                    // Clear existing options except first
                    budgetSelect.innerHTML = '<option value="">Tidak ada</option>';
                    
                    // Add budget options
                    budgetData.data.forEach(budget => {
                        const option = document.createElement('option');
                        option.value = budget.id;
                        option.textContent = `${budget.namaBudget} (${budget.kategori})`;
                        budgetSelect.appendChild(option);
                    });
                }
            }
            
            // Load goals
            const goalResponse = await fetch('/api/goals', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (goalResponse.ok) {
                const goalData = await goalResponse.json();
                const goalSelect = document.getElementById('goal');
                
                if (goalSelect && goalData.success) {
                    // Clear existing options except first
                    goalSelect.innerHTML = '<option value="">Tidak ada</option>';
                    
                    // Add goal options
                    goalData.data.forEach(goal => {
                        const option = document.createElement('option');
                        option.value = goal.id;
                        option.textContent = goal.namaGoal;
                        goalSelect.appendChild(option);
                    });
                }
            }
            
        } catch (error) {
            console.error('Error loading budgets/goals:', error);
        }
    }

    async function saveTransaction(event) {
        event.preventDefault();
        
        showLoading('Menyimpan transaksi...');
        
        const formData = {
            jenis: document.getElementById('jenis').value,
            kategori: document.getElementById('kategori').value,
            jumlah: parseFloat(document.getElementById('jumlah').value),
            keterangan: document.getElementById('keterangan').value,
            budget_id: document.getElementById('budget').value || null,
            goal_id: document.getElementById('goal').value || null
        };
        
        console.log('Saving transaction:', formData);
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                hideLoading();
                showToast('❌ CSRF token tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }
            
            const response = await fetch(LARAVEL_API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            console.log('Save response status:', response.status);
            
            if (response.status === 419) {
                hideLoading();
                showToast('❌ Session expired. Silakan refresh halaman (F5).', 'error');
                return;
            }
            
            const result = await response.json();
            console.log('Save API Response:', result);
            
            hideLoading();
            
            if (result.success) {
                closeModal();
                showToast('✅ Transaksi berhasil disimpan!', 'success');
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(`❌ ${result.message || 'Gagal menyimpan transaksi'}`, 'error');
            }
            
        } catch (error) {
            hideLoading();
            console.error('Error saving transaction:', error);
            showToast('❌ Gagal menyimpan transaksi', 'error');
        }
    }

    function showLoading(text) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const loadingText = document.getElementById('loadingText');
        
        if (loadingText) loadingText.textContent = text;
        if (loadingOverlay) loadingOverlay.classList.add('active');
    }

    function hideLoading() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) loadingOverlay.classList.remove('active');
    }

    function showToast(message, type) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type === 'success' ? 'success' : 'error',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Event Listeners
    document.addEventListener('click', function(e) {
        if (e.target.closest('.voice-btn')) {
            startVoiceRecording();
        }
    });

    // Make functions globally available
    window.startVoiceRecording = startVoiceRecording;
    window.stopVoiceRecording = stopVoiceRecording;
    window.closeVoiceModal = closeModal;
    window.saveVoiceTransaction = saveTransaction;
})();
</script>
</body>
</html>
