<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Keuangan</title>
    <style>
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
            padding: 5px;
        }
        /* nanti kalau pakai img:
           .logo img { width:100%; height:100%; object-fit:cover; border-radius:44px; } */

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


        /* label persentase di tengah warna masing-masing */
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

        /* warna teks ikut warna slice */
        .chart-label-income {
            color: #00A311;   /* hijau pemasukan */
        }

        .chart-label-expense {
            color: #ED6363;   /* merah pengeluaran */
        }



        .chart-legend {
            display: flex;
            gap: 30px;
            font-size: 16px;
            font-weight: 800;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 12px;
            height: 13px;
        }

        .legend-color.income {
            background: #00A311;
        }

        .legend-color.expense {
            background: #ED6363;
        }

        /* Transactions */
        .transaction-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 260px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .transaction-list::-webkit-scrollbar {
            width: 6px;
        }
        .transaction-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .transaction-list::-webkit-scrollbar-thumb {
            background: #C4C4C4;
            border-radius: 10px;
        }

        .transaction-item {
            background: #E3F5FF;
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid #203838;
            box-shadow: 1px 4px 3px 1px rgba(0, 0, 0, 0.15);
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            align-items: center;
            gap: 10px;
        }

        .transaction-name {
            font-size: 18px;
            font-weight: 400;
        }

        .transaction-date {
            color: rgba(0, 0, 0, 0.6);
            font-size: 16px;
            text-align: center;
        }

        .transaction-amount {
            font-size: 18px;
            font-weight: 800;
            text-align: right;
        }

        .transaction-amount.income {
            color: #00A311;
        }

        .transaction-amount.expense {
            color: #ED6363;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 15px 0;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .menu-item span {
                font-size: 10px;
            }

            .main-content {
                padding: 15px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            /* Pie Chart Mobile */
            .pie-chart {
                width: 150px;
                height: 150px;
            }

            .chart-legend {
                flex-direction: column;
                gap: 10px;
                font-size: 14px;
            }

            /* Transaction Mobile */
            .transaction-item {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 12px 15px;
            }

            .transaction-name {
                font-size: 16px;
                font-weight: 600;
            }

            .transaction-date {
                font-size: 14px;
                text-align: left;
            }

            .transaction-amount {
                font-size: 16px;
                text-align: left;
                font-weight: 700;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #2C3E50;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
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
    
    </style>
</head>
<body>

@php
    // kalau controller sudah mengirim $transactions, ini dipakai
    // kalau belum, pakai dummy data bawaanmu
    $transactions = $transactions ?? collect([
        ['name' => 'Gaji',     'date' => '10/10/2025', 'amount' => 1000000],
        ['name' => 'Shopping', 'date' => '5/10/2025',  'amount' => -200000],
        ['name' => 'Makan',    'date' => '5/10/2025',  'amount' => -50000],
        ['name' => 'Sedekah',  'date' => '1/10/2025',  'amount' => -150000],
    ]);

    $totalIncome  = $transactions->where('amount', '>', 0)->sum('amount');
    $totalExpense = abs($transactions->where('amount', '<', 0)->sum('amount'));

    $total = $totalIncome + $totalExpense;

    $incomePerc  = $total ? round($totalIncome  / $total * 100) : 0;
    $expensePerc = 100 - $incomePerc;

    // Pindahkan awal lingkaran ke atas (−90°), 
    $incomeAngle  = -90 + ($incomePerc * 3.6 / 2);
    $expenseAngle = -90 + ($incomePerc * 3.6) + ($expensePerc * 3.6 / 2);

@endphp

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo"></div>

        <div class="menu-item">
            <div class="menu-item-icon">🏠</div>
            <span>Dashboard</span>
        </div>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="{{ asset('images/voica-logo.png') }}" alt="VOICA Logo" style="width: 100%; height: auto; object-fit: contain;">
            </div>
            
            <div class="menu-item">
                <svg viewBox="0 0 39 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.875 36.4444H12.1875V22.7778H26.8125V36.4444H34.125V15.9444L19.5 5.69444L4.875 15.9444V36.4444ZM0 41V13.6667L19.5 0L39 13.6667V41H21.9375V27.3333H17.0625V41H0Z" fill="black"/>
                </svg>
                <span>Dashboard</span>
            </div>

        <a href="{{ route('budgeting') }}" class="menu-item">
            <div class="menu-item-icon">💰</div>
            <span>Budgeting</span>
        </a>

        <div class="menu-item">
            <div class="menu-item-icon">🎯</div>
            <span>Goals</span>
        </div>

        <div class="menu-item">
            <div class="menu-item-icon">📊</div>
            <span>Laporan</span>
        </div>

        <div class="menu-item" style="margin-top: auto; margin-bottom: 20px;">
            <form method="POST" action="{{ route('logout') }}" style="display: contents;">
                @csrf
                <button type="submit" style="background: none; border: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 0;">
                    <div class="menu-item-icon" style="background:#FFE4E4;">🚪</div>
                    <span style="color: #F53003; font-size: 12px; font-weight: 600;">Logout</span>
                </button>
            </form>
        </div>
    </div>
            <a href="{{ route('laporan') }}" class="menu-item">
                <svg viewBox="0 0 44 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.5 21.5417H22.95V19.975C23.8833 19.8444 24.6753 19.4691 25.326 18.849C25.9767 18.2288 26.3013 17.4292 26.3 16.45C26.3 15.6014 25.9667 14.8912 25.3 14.3193C24.6333 13.7475 23.8667 13.2827 23 12.925V9.30208C23.3333 9.4 23.6087 9.56319 23.826 9.79167C24.0433 10.0201 24.2013 10.2976 24.3 10.624L26.1 9.88958C25.8667 9.20417 25.4667 8.65779 24.9 8.25046C24.3333 7.84313 23.7 7.57353 23 7.44167V5.875H21.5V7.39271C20.5667 7.49062 19.7753 7.8255 19.126 8.39733C18.4767 8.96917 18.1513 9.72769 18.15 10.6729C18.15 11.5542 18.492 12.2885 19.176 12.876C19.86 13.4635 20.6347 13.9368 21.5 14.2958V18.1635C20.9667 18.0003 20.5167 17.7229 20.15 17.3313C19.7833 16.9396 19.5333 16.4826 19.4 15.9604L17.65 16.6948C17.9167 17.6087 18.3833 18.3594 19.05 18.9469C19.7167 19.5344 20.5333 19.8934 21.5 20.024V21.5417Z" fill="black"/>
                </svg>
                <span>Laporan</span>
            </a>

            <a href="{{ route('settings') }}" class="menu-item">
                <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.14 12.94C19.18 12.64 19.2 12.33 19.2 12C19.2 11.68 19.18 11.36 19.13 11.06L21.16 9.48C21.34 9.34 21.39 9.07 21.28 8.87L19.36 5.55C19.24 5.33 18.99 5.26 18.77 5.33L16.38 6.29C15.88 5.91 15.35 5.59 14.76 5.35L14.4 2.81C14.36 2.57 14.16 2.4 13.92 2.4H10.08C9.84 2.4 9.65 2.57 9.61 2.81L9.25 5.35C8.66 5.59 8.12 5.92 7.63 6.29L5.24 5.33C5.02 5.25 4.77 5.33 4.65 5.55L2.74 8.87C2.62 9.08 2.66 9.34 2.86 9.48L4.89 11.06C4.84 11.36 4.8 11.69 4.8 12C4.8 12.31 4.82 12.64 4.87 12.94L2.84 14.52C2.66 14.66 2.61 14.93 2.72 15.13L4.64 18.45C4.76 18.67 5.01 18.74 5.23 18.67L7.62 17.71C8.12 18.09 8.65 18.41 9.24 18.65L9.6 21.19C9.65 21.43 9.84 21.6 10.08 21.6H13.92C14.16 21.6 14.36 21.43 14.39 21.19L14.75 18.65C15.34 18.41 15.88 18.09 16.37 17.71L18.76 18.67C18.98 18.75 19.23 18.67 19.35 18.45L21.27 15.13C21.39 14.91 21.34 14.66 21.15 14.52L19.14 12.94ZM12 15.6C10.02 15.6 8.4 13.98 8.4 12C8.4 10.02 10.02 8.4 12 8.4C13.98 8.4 15.6 10.02 15.6 12C15.6 13.98 13.98 15.6 12 15.6Z" fill="black"/>
                </svg>
                <span>Settings</span>
            </a>
            <div class="menu-item" style="margin-top: auto; margin-bottom: 20px;">
                <form method="POST" action="{{ route('logout') }}" style="display: contents;">
                    @csrf
                    <button type="submit" style="background: none; border: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 0;">
                        <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 7L15.59 8.41L18.17 11H8V13H18.17L15.59 15.59L17 17L22 12L17 7ZM4 5H12V3H4C2.9 3 2 3.9 2 5V19C2 20.1 2.9 21 4 21H12V19H4V5Z" fill="#F53003"/>
                        </svg>
                        <span style="color: #F53003; font-size: 12px; font-weight: 600;">Logout</span>
                    </button>
                </form>
            </div>
        </div>
        
        

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-text">
                <h1>Halo, {{ Auth::check() ? Auth::user()->name : 'Budi' }}!</h1>
                <p>Mari lihat pengeluaran Anda hari ini</p>
            </div>
            <button class="voice-btn">
                <svg width="24" height="30" viewBox="0 0 38 48" fill="none">
                    <path d="M38 20.8929C38 20.6571 37.7927 20.4643 37.5394 20.4643H34.0849C33.8315 20.4643 33.6242 20.6571 33.6242 20.8929C33.6242 28.4089 27.0779 34.5 19 34.5C10.9221 34.5 4.37576 28.4089 4.37576 20.8929C4.37576 20.6571 4.16849 20.4643 3.91515 20.4643H0.460606C0.207273 20.4643 0 20.6571 0 20.8929C0 29.9304 7.28909 37.3875 16.697 38.4429V43.9286H8.33121C7.54243 43.9286 6.90909 44.6946 6.90909 45.6429V47.5714C6.90909 47.8071 7.0703 48 7.26606 48H30.7339C30.9297 48 31.0909 47.8071 31.0909 47.5714V45.6429C31.0909 44.6946 30.4576 43.9286 29.6688 43.9286H21.0727V38.4696C30.59 37.5054 38 30.0054 38 20.8929ZM19 30C24.4064 30 28.7879 25.9714 28.7879 21V9C28.7879 4.02857 24.4064 0 19 0C13.5936 0 9.21212 4.02857 9.21212 9V21C9.21212 25.9714 13.5936 30 19 30Z" fill="white"/>
                </svg>
                Tekan Untuk Bersuara
            </button>
        </div>

        <!-- Cards Grid -->
        <div class="cards-grid">
            <div class="card balance">
                <div class="card-icon">💳</div>
                <div class="card-content">
                    <h3>Saldo saat ini</h3>
                    <div class="amount">
                        Rp{{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <!-- Cards Grid -->
            <div class="cards-grid">
                <div class="card balance">
                    <div class="card-icon">
                        <svg width="30" height="24" viewBox="0 0 37 29" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M32.5075 4.12205H3.82441V24.7324H32.5075V4.12205ZM3.82441 0C1.71225 0 0 1.8455 0 4.12205V24.7324C0 27.0089 1.71225 28.8544 3.82441 28.8544H32.5075C34.6196 28.8544 36.3318 27.0089 36.3318 24.7324V4.12205C36.3318 1.8455 34.6196 0 32.5075 0H3.82441Z" fill="#00456A" fill-opacity="0.7"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Saldo saat ini</h3>
                        <div class="amount">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                    </div>
                </div>

            <div class="card target">
                <div class="card-icon">🎯</div>
                <div class="card-content">
                    <h3>Target Dana Darurat</h3>
                    <div class="amount" style="color: #2C3E50; font-size: 24px; font-weight: 400;">
                        50%
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    <div class="card-content">
                        <h3>Pemasukan</h3>
                        <div class="amount">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="card income">
                <div class="card-icon">📈</div>
                <div class="card-content">
                    <h3>Pemasukan</h3>
                    <div class="amount">
                        Rp{{ number_format($totalIncome, 0, ',', '.') }}

                <div class="card expense">
                    <div class="card-icon">
                        <svg width="30" height="30" viewBox="0 0 39 35" fill="none">
                            <path opacity="0.95" d="M10.0254 17.0866V24.895C10.0254 25.6605 10.3181 26.3109 10.9036 26.8462C11.4891 27.3815 12.1989 27.6497 13.033 27.6509C13.8671 27.6521 14.5776 27.3839 15.1644 26.8462C15.7512 26.3085 16.0433 25.6581 16.0406 24.895V17.0866C16.0406 16.3211 15.7485 15.6701 15.1644 15.1336C14.5802 14.5971 13.8698 14.3295 13.033 14.3307C12.1962 14.3319 11.4864 14.5996 10.9036 15.1336C10.3208 15.6676 10.0281 16.3186 10.0254 17.0866Z" fill="#A61C1C"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Pengeluaran</h3>
                        <div class="amount">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="card expense">
                <div class="card-icon">📉</div>
                <div class="card-content">
                    <h3>Pengeluaran</h3>
                    <div class="amount">
                        Rp{{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

                @if($goal)
                <div class="card target">
                    <div class="card-icon">
                        <svg width="40" height="40" viewBox="0 0 52 54" fill="none">
                            <path d="M23.8333 41.265C23.8287 40.9973 23.7375 40.739 23.5744 40.5317C23.4113 40.3244 23.1858 40.1801 22.9341 40.122C20.5117 39.5109 18.3096 38.1894 16.5901 36.315C16.416 36.1186 16.1834 35.9886 15.9296 35.9457C15.6758 35.9029 15.4156 35.9498 15.1905 36.0788L11.349 38.385C11.2165 38.461 11.1017 38.5663 11.0127 38.6934C10.9237 38.8206 10.8627 38.9664 10.834 39.1208C10.8053 39.2751 10.8095 39.434 10.8465 39.5864C10.8834 39.7389 10.9521 39.881 11.0478 40.0028C14.2194 43.9368 18.7108 46.474 23.6145 47.1015C23.642 47.105 23.6699 47.1024 23.6964 47.0938C23.7229 47.0853 23.7473 47.0709 23.768 47.0517C23.7887 47.0326 23.8053 47.009 23.8165 46.9827C23.8278 46.9564 23.8335 46.9278 23.8333 46.899V41.265Z" fill="#00456A" fill-opacity="0.7"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>{{ $goal->namaGoal }}</h3>
                        <div class="amount" style="color: #2C3E50; font-size: 24px; font-weight: 400;">{{ number_format($goalPercentage, 1) }}%</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min($goalPercentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <div class="bottom-grid">
                <!-- Chart Section -->
                <div class="chart-section">
                    <h2>Chart per Bulan</h2>
                    <div class="chart-container">
                        <div class="pie-chart"
                        style="background: conic-gradient(
                                    #00A311 0 {{ $incomePerc }}%,
                                    #ED6363 {{ $incomePerc }}% 100%
                                );">
                        <div class="chart-label" style="--angle: {{ $incomeAngle }}deg;">
                            {{ $incomePerc }}%
                        </div>
                        <div class="chart-label" style="--angle: {{ $expenseAngle }}deg;">
                            {{ $expensePerc }}%
                        </div>
                    </div>


                        <div class="chart-legend">
                            <div class="legend-item">
                                <div class="legend-color income"></div>
                                <span style="color: #00A311;">Pemasukan</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color expense"></div>
                                <span style="color: #ED6363;">Pengeluaran</span>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- Bottom Section -->
            <div class="bottom-section">
                <div class="bottom-grid">
                    <!-- Chart Section -->
                    <div class="chart-section">
                        <h2>Chart per Bulan</h2>
                        <div class="chart-container">
                            @php
                                $total = $totalPemasukan + $totalPengeluaran;
                                $pemasukanPercent = $total > 0 ? ($totalPemasukan / $total * 100) : 50;
                                $pengeluaranPercent = $total > 0 ? ($totalPengeluaran / $total * 100) : 50;
                            @endphp
                            <div class="pie-chart" style="background: conic-gradient(#00A311 0% {{ $pemasukanPercent }}%, #ED6363 {{ $pemasukanPercent }}% 100%);"></div>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <div class="legend-color income"></div>
                                    <span style="color: #00A311;">Pemasukan ({{ number_format($pemasukanPercent, 1) }}%)</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color expense"></div>
                                    <span style="color: #ED6363;">Pengeluaran ({{ number_format($pengeluaranPercent, 1) }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Transactions Section -->
                <div class="transactions-section">
                    <h2>Transaksi Terbaru</h2>
                    <div class="transaction-list">
                        @foreach($transactions as $trx)
                            @php
                                $isIncome = $trx['amount'] > 0;
                            @endphp
                    <!-- Transactions Section -->
                    <div class="transactions-section">
                        <h2>Transaksi Terbaru</h2>
                        <div class="transaction-list">
                            @forelse($recentTransactions as $transaction)
                            <div class="transaction-item">
                                <div class="transaction-name">{{ $trx['name'] }}</div>
                                <div class="transaction-date">{{ $trx['date'] }}</div>
                                <div class="transaction-amount {{ $isIncome ? 'income' : 'expense' }}">
                                    {{ $isIncome ? '+ ' : '- ' }}Rp{{ number_format(abs($trx['amount']), 0, ',', '.') }}
                                </div>
                                <div class="transaction-name">{{ $transaction->keterangan }}</div>
                                <div class="transaction-date">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</div>
                                <div class="transaction-amount {{ $transaction->jenis == 'Pemasukan' ? 'income' : 'expense' }}">
                                    {{ $transaction->jenis == 'Pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                            @empty
                            <div style="text-align: center; padding: 40px; color: #999;">
                                <p>Belum ada transaksi</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voice Transaction Modal -->
    <div class="modal-overlay" id="voiceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>🎤 Transaksi Voice</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            
            <form id="transactionForm" onsubmit="saveTransaction(event)">
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
                        @foreach($budgets as $budget)
                        <option value="{{ $budget->id }}">{{ $budget->namaBudget }} ({{ $budget->kategori }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="goal">Alokasi Tabungan (Opsional)</label>
                    <select id="goal" name="goal">
                        <option value="">Tidak ada</option>
                        @foreach($goals as $goalItem)
                        <option value="{{ $goalItem->id }}">{{ $goalItem->namaGoal }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
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

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-icon" id="toastIcon"></div>
        <div class="toast-message" id="toastMessage"></div>
    </div>

    <script>
        // Configuration
        const LARAVEL_API_URL = '/api/voice-transaction';
        const PARSE_API_URL = '/api/parse-voice-text';
        
        // Global variables
        let recognition = null;
        let isRecording = false;

        // Initialize Web Speech API
        function initSpeechRecognition() {
            // Check browser support
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                showToast('❌ Browser Anda tidak support voice recognition. Gunakan Chrome atau Edge.', 'error');
                return false;
            }

            // Create recognition instance
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            
            // Configure recognition
            recognition.lang = 'id-ID'; // Bahasa Indonesia
            recognition.continuous = false; // Stop after one result
            recognition.interimResults = false; // Only final results
            recognition.maxAlternatives = 1;

            // Event: Recognition starts
            recognition.onstart = function() {
                isRecording = true;
                updateVoiceButtonRecording(true);
                showToast('🎤 Mulai berbicara...', 'success');
            };

            // Event: Recognition ends
            recognition.onend = function() {
                isRecording = false;
                updateVoiceButtonRecording(false);
            };

            // Event: Result received
            recognition.onresult = function(event) {
                const text = event.results[0][0].transcript;
                const confidence = event.results[0][0].confidence;
                
                console.log('Speech recognized:', text, 'Confidence:', confidence);
                
                // Send to Laravel for parsing
                sendTextToAPI(text);
            };

            // Event: Error occurred
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

        // Voice Recording Functions
        function startVoiceRecording() {
            if (isRecording) {
                // Stop recording
                stopVoiceRecording();
                return;
            }
            
            // Initialize if not yet
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
            const voiceText = voiceBtn.querySelector('.voice-btn-text');
            
            if (recording) {
                voiceBtn.classList.add('recording');
                if (voiceText) voiceText.textContent = '🔴 Merekam... (Klik untuk Stop)';
            } else {
                voiceBtn.classList.remove('recording');
                if (voiceText) voiceText.textContent = 'Tekan Untuk Bersuara';
            }
        }

        async function sendTextToAPI(text) {
            showLoading('Memproses text...');
            
            try {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                // Send to Laravel API for parsing
                const response = await fetch(PARSE_API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ text: text })
                });
                
                const result = await response.json();
                
                hideLoading();
                
                if (result.success) {
                    // Auto-fill form with parsed data
                    autoFillForm(result.data);
                    
                    // Show modal
                    openModal();
                    
                    showToast(`✅ Terdeteksi: "${result.raw_text}"`, 'success');
                } else {
                    showToast(`❌ ${result.error}`, 'error');
                }
                
            } catch (error) {
                hideLoading();
                console.error('Error sending text:', error);
                showToast('❌ Gagal memproses text. Silakan coba lagi.', 'error');
            }
        }

        function autoFillForm(data) {
            // Fill form fields
            document.getElementById('jenis').value = data.jenis || '';
            document.getElementById('kategori').value = data.kategori || '';
            document.getElementById('jumlah').value = data.jumlah || '';
            document.getElementById('keterangan').value = data.keterangan || '';
            
            // TODO: Set budget and goal if detected
            // For now, we'll leave them empty for user to select manually
        }

        // Modal Functions
        function openModal() {
            document.getElementById('voiceModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('voiceModal').classList.remove('active');
            document.getElementById('transactionForm').reset();
        }

        // Save Transaction
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
            
            try {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                // Send to Laravel API
                const response = await fetch(LARAVEL_API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                hideLoading();
                
                if (result.success) {
                    closeModal();
                    showToast('✅ Transaksi berhasil disimpan!', 'success');
                    
                    // Reload page after 1.5 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(`❌ ${result.message}`, 'error');
                }
                
            } catch (error) {
                hideLoading();
                console.error('Error saving transaction:', error);
                showToast('❌ Gagal menyimpan transaksi', 'error');
            }
        }

        // UI Helper Functions
        function showLoading(text) {
            document.getElementById('loadingText').textContent = text;
            document.getElementById('loadingOverlay').classList.add('active');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        function showToast(message, type) {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');
            
            // Set icon
            toastIcon.textContent = type === 'success' ? '✅' : '❌';
            
            // Set message
            toastMessage.textContent = message;
            
            // Set type
            toast.className = `toast ${type} active`;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                toast.classList.remove('active');
            }, 5000);
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Attach voice button event
            const voiceBtn = document.querySelector('.voice-btn');
            if (voiceBtn) {
                voiceBtn.addEventListener('click', startVoiceRecording);
            }
            
            // Close modal on overlay click
            document.getElementById('voiceModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        });
    </script>
</body>

</html>
