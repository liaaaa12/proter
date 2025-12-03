<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background: linear-gradient(180deg, #2A8576 42%, #1C5A50 82%);
            border-radius: 44px;
            margin-bottom: 40px;
            overflow: hidden;
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

            .transaction-item {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .transaction-date,
            .transaction-amount {
                text-align: center;
            }
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

            <div class="card target">
                <div class="card-icon">🎯</div>
                <div class="card-content">
                    <h3>Target Dana Darurat</h3>
                    <div class="amount" style="color: #2C3E50; font-size: 24px; font-weight: 400;">
                        50%
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
            </div>

            <div class="card income">
                <div class="card-icon">📈</div>
                <div class="card-content">
                    <h3>Pemasukan</h3>
                    <div class="amount">
                        Rp{{ number_format($totalIncome, 0, ',', '.') }}
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

                <!-- Transactions Section -->
                <div class="transactions-section">
                    <h2>Transaksi Terbaru</h2>
                    <div class="transaction-list">
                        @foreach($transactions as $trx)
                            @php
                                $isIncome = $trx['amount'] > 0;
                            @endphp
                            <div class="transaction-item">
                                <div class="transaction-name">{{ $trx['name'] }}</div>
                                <div class="transaction-date">{{ $trx['date'] }}</div>
                                <div class="transaction-amount {{ $isIncome ? 'income' : 'expense' }}">
                                    {{ $isIncome ? '+ ' : '- ' }}Rp{{ number_format(abs($trx['amount']), 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
