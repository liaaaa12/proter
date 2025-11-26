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
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 35px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .menu-item:hover {
            transform: scale(1.05);
        }

        .menu-item svg {
            width: 35px;
            height: 35px;
        }

        .menu-item span {
            font-size: 12px;
            font-weight: 600;
            text-align: center;
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
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: conic-gradient(#00A311 0% 60%, #ED6363 60% 100%);
            position: relative;
            margin-bottom: 20px;
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

            .menu-item svg {
                width: 28px;
                height: 28px;
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
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo"></div>
            
            <div class="menu-item">
                <svg viewBox="0 0 39 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.875 36.4444H12.1875V22.7778H26.8125V36.4444H34.125V15.9444L19.5 5.69444L4.875 15.9444V36.4444ZM0 41V13.6667L19.5 0L39 13.6667V41H21.9375V27.3333H17.0625V41H0Z" fill="black"/>
                </svg>
                <span>Dashboard</span>
            </div>

            <a href="{{ route('budgeting') }}" class="menu-item" >
                <svg viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M45.9481 24.5292C45.7971 24.6842 45.6862 24.8738 45.6252 25.0814C45.5641 25.2891 45.5547 25.5085 45.5977 25.7206C45.8103 26.7839 45.9167 27.877 45.9167 29C45.9164 32.4104 44.8854 35.7411 42.9588 38.5553C41.0321 41.3694 38.2999 43.5355 35.1206 44.7694C31.9412 46.0033 28.4631 46.2474 25.1425 45.4697C21.8219 44.6921 18.8139 42.9289 16.5131 40.4115C16.399 40.2836 16.2603 40.1798 16.1054 40.1065C15.9505 40.0331 15.7824 39.9916 15.6111 39.9844C15.4398 39.9771 15.2688 40.0044 15.1083 40.0645C14.9477 40.1246 14.8009 40.2163 14.6764 40.3342C14.4649 40.539 14.3402 40.8172 14.3281 41.1114C14.316 41.4055 14.4173 41.6931 14.6112 41.9147C17.4501 45.0802 21.2535 47.2216 25.4324 48.0072C29.6113 48.7928 33.9327 48.1787 37.7274 46.2602C41.5221 44.3417 44.5785 41.2256 46.4233 37.3945C48.2681 33.5634 48.7984 29.231 47.9322 25.0681C47.7413 24.1425 46.6151 23.8646 45.9481 24.5316M42.9901 17.3057C43.2056 17.097 43.3308 16.8122 43.339 16.5123C43.3471 16.2124 43.2375 15.9212 43.0336 15.7011C40.123 12.6268 36.2838 10.5929 32.1055 9.91156C27.9271 9.23027 23.6407 9.93929 19.9043 11.9297C16.1679 13.9202 13.1881 17.0821 11.4225 20.9298C9.65687 24.7776 9.20303 29.0985 10.1307 33.2292C10.3337 34.1427 11.4502 34.4109 12.1123 33.7487C12.4265 33.4346 12.5473 32.9754 12.4555 32.5404C11.6852 28.9451 12.1073 25.1966 13.6578 21.8626C15.2084 18.5286 17.803 15.7906 21.0488 14.0631C24.2947 12.3355 28.015 11.7126 31.6465 12.2885C35.2781 12.8645 38.6231 14.6079 41.1752 17.255C41.2908 17.3775 41.4294 17.476 41.5831 17.5448C41.7368 17.6137 41.9025 17.6516 42.0709 17.6563C42.2392 17.661 42.4069 17.6325 42.5642 17.5723C42.7215 17.5122 42.8678 17.4216 42.9901 17.3057Z" fill="black"/>
                </svg>
                <span>Budgeting</span>
            </a>

            <div class="menu-item">
                <svg viewBox="0 0 63 63" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21 42.2438C21.246 42.4899 21.3842 42.8237 21.3842 43.1717C21.3842 43.5197 21.246 43.8535 21 44.0996L18.375 46.7246C18.2539 46.85 18.1091 46.95 17.9489 47.0188C17.7888 47.0876 17.6166 47.1238 17.4423 47.1253C17.268 47.1268 17.0952 47.0936 16.9339 47.0276C16.7726 46.9616 16.626 46.8641 16.5028 46.7409C16.3796 46.6177 16.2821 46.4711 16.2161 46.3098C16.1501 46.1485 16.1169 45.9757 16.1184 45.8014C16.12 45.6272 16.1562 45.4549 16.2249 45.2948C16.2937 45.1347 16.3937 44.9898 16.5191 44.8688L19.1441 42.2438C19.3902 41.9977 19.724 41.8595 20.072 41.8595C20.42 41.8595 20.7538 41.9977 21 42.2438Z" fill="black"/>
                </svg>
                <span>Goals</span>
            </div>

            <div class="menu-item">
                <svg viewBox="0 0 44 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.5 21.5417H22.95V19.975C23.8833 19.8444 24.6753 19.4691 25.326 18.849C25.9767 18.2288 26.3013 17.4292 26.3 16.45C26.3 15.6014 25.9667 14.8912 25.3 14.3193C24.6333 13.7475 23.8667 13.2827 23 12.925V9.30208C23.3333 9.4 23.6087 9.56319 23.826 9.79167C24.0433 10.0201 24.2013 10.2976 24.3 10.624L26.1 9.88958C25.8667 9.20417 25.4667 8.65779 24.9 8.25046C24.3333 7.84313 23.7 7.57353 23 7.44167V5.875H21.5V7.39271C20.5667 7.49062 19.7753 7.8255 19.126 8.39733C18.4767 8.96917 18.1513 9.72769 18.15 10.6729C18.15 11.5542 18.492 12.2885 19.176 12.876C19.86 13.4635 20.6347 13.9368 21.5 14.2958V18.1635C20.9667 18.0003 20.5167 17.7229 20.15 17.3313C19.7833 16.9396 19.5333 16.4826 19.4 15.9604L17.65 16.6948C17.9167 17.6087 18.3833 18.3594 19.05 18.9469C19.7167 19.5344 20.5333 19.8934 21.5 20.024V21.5417Z" fill="black"/>
                </svg>
                <span>Laporan</span>
            </div>
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
                    <h1>Halo, {{ Auth::user()->name }}!</h1>
                    <p>Let's track your spending today</p>
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
                    <div class="card-icon">
                        <svg width="30" height="24" viewBox="0 0 37 29" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M32.5075 4.12205H3.82441V24.7324H32.5075V4.12205ZM3.82441 0C1.71225 0 0 1.8455 0 4.12205V24.7324C0 27.0089 1.71225 28.8544 3.82441 28.8544H32.5075C34.6196 28.8544 36.3318 27.0089 36.3318 24.7324V4.12205C36.3318 1.8455 34.6196 0 32.5075 0H3.82441Z" fill="#00456A" fill-opacity="0.7"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Saldo saat ini</h3>
                        <div class="amount">Rp600.000</div>
                    </div>
                </div>

                <div class="card income">
                    <div class="card-icon">
                        <svg width="30" height="30" viewBox="0 0 39 35" fill="none">
                            <path d="M10.2632 17.9134V10.105C10.2632 9.33946 10.5628 8.68906 11.1622 8.15381C11.7616 7.61855 12.4882 7.35031 13.3421 7.34908C14.196 7.34786 14.9233 7.6161 15.524 8.15381C16.1248 8.69151 16.4238 9.3419 16.421 10.105V17.9134C16.421 18.6789 16.122 19.3299 15.524 19.8664C14.9261 20.4029 14.1987 20.6705 13.3421 20.6693C12.4855 20.6681 11.7588 20.4004 11.1622 19.8664C10.5656 19.3324 10.2659 18.6814 10.2632 17.9134Z" fill="#00670B"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Pemasukan</h3>
                        <div class="amount">Rp1.000.000</div>
                    </div>
                </div>

                <div class="card expense">
                    <div class="card-icon">
                        <svg width="30" height="30" viewBox="0 0 39 35" fill="none">
                            <path opacity="0.95" d="M10.0254 17.0866V24.895C10.0254 25.6605 10.3181 26.3109 10.9036 26.8462C11.4891 27.3815 12.1989 27.6497 13.033 27.6509C13.8671 27.6521 14.5776 27.3839 15.1644 26.8462C15.7512 26.3085 16.0433 25.6581 16.0406 24.895V17.0866C16.0406 16.3211 15.7485 15.6701 15.1644 15.1336C14.5802 14.5971 13.8698 14.3295 13.033 14.3307C12.1962 14.3319 11.4864 14.5996 10.9036 15.1336C10.3208 15.6676 10.0281 16.3186 10.0254 17.0866Z" fill="#A61C1C"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Pengeluaran</h3>
                        <div class="amount">Rp400.000</div>
                    </div>
                </div>

                <div class="card target">
                    <div class="card-icon">
                        <svg width="40" height="40" viewBox="0 0 52 54" fill="none">
                            <path d="M23.8333 41.265C23.8287 40.9973 23.7375 40.739 23.5744 40.5317C23.4113 40.3244 23.1858 40.1801 22.9341 40.122C20.5117 39.5109 18.3096 38.1894 16.5901 36.315C16.416 36.1186 16.1834 35.9886 15.9296 35.9457C15.6758 35.9029 15.4156 35.9498 15.1905 36.0788L11.349 38.385C11.2165 38.461 11.1017 38.5663 11.0127 38.6934C10.9237 38.8206 10.8627 38.9664 10.834 39.1208C10.8053 39.2751 10.8095 39.434 10.8465 39.5864C10.8834 39.7389 10.9521 39.881 11.0478 40.0028C14.2194 43.9368 18.7108 46.474 23.6145 47.1015C23.642 47.105 23.6699 47.1024 23.6964 47.0938C23.7229 47.0853 23.7473 47.0709 23.768 47.0517C23.7887 47.0326 23.8053 47.009 23.8165 46.9827C23.8278 46.9564 23.8335 46.9278 23.8333 46.899V41.265Z" fill="#00456A" fill-opacity="0.7"/>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Target Dana Darurat</h3>
                        <div class="amount" style="color: #2C3E50; font-size: 24px; font-weight: 400;">50%</div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
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
                            <div class="pie-chart"></div>
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
                            <div class="transaction-item">
                                <div class="transaction-name">Gaji</div>
                                <div class="transaction-date">10/10/2025</div>
                                <div class="transaction-amount income">+ Rp1.000.000</div>
                            </div>
                            <div class="transaction-item">
                                <div class="transaction-name">Shopping</div>
                                <div class="transaction-date">5/10/2025</div>
                                <div class="transaction-amount expense">- Rp200.000</div>
                            </div>
                            <div class="transaction-item">
                                <div class="transaction-name">Makan</div>
                                <div class="transaction-date">5/10/2025</div>
                                <div class="transaction-amount expense">- Rp50.000</div>
                            </div>
                            <div class="transaction-item">
                                <div class="transaction-name">Sedekah</div>
                                <div class="transaction-date">1/10/2025</div>
                                <div class="transaction-amount expense">- Rp150.000</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>