<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Akun - VOICA</title>
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

        /* Sidebar - Same as dashboard */
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 35px;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            color: black;
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

        /* Header - Same as dashboard */
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            border: 1px solid #00456A;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .header h1 {
            color: #2C3E50;
            font-size: clamp(24px, 4vw, 40px);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header p {
            color: #2C3E50;
            font-size: clamp(14px, 2vw, 18px);
        }

        /* Settings Card */
        .settings-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            border: 1px solid #00456A;
            max-width: 800px;
            margin: 0 auto;
        }

        .settings-card h2 {
            color: #2C3E50;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Form Styles - Same as modal form */
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

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E3F5FF;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            background: #F8FCFF;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00456A;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 69, 106, 0.1);
            transform: translateY(-1px);
        }

        .form-group input:disabled {
            background: #E3F5FF;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Button Styles */
        .btn {
            padding: 12px 30px;
            border-radius: 100px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #00456A;
            color: white;
        }

        .btn-primary:hover {
            background: #003855;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 69, 106, 0.3);
        }

        .btn-secondary {
            background: #D9D9D9;
            color: #2C3E50;
        }

        .btn-secondary:hover {
            background: #C0C0C0;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        /* Toast Notification - Same as dashboard */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
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

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-icon {
            font-size: 24px;
        }

        .toast-message {
            flex: 1;
            color: #2C3E50;
            font-size: 14px;
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #E3F5FF;
            border-top-color: #00456A;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: #2C3E50;
            font-size: 16px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .settings-card {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="{{ asset('images/voica-logo.png') }}" alt="VOICA Logo" style="width: 100%; height: auto; object-fit: contain;">
            </div>
            
            <a href="{{ route('dashboard') }}" class="menu-item">
                <svg viewBox="0 0 39 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.875 36.4444H12.1875V22.7778H26.8125V36.4444H34.125V15.9444L19.5 5.69444L4.875 15.9444V36.4444ZM0 41V13.6667L19.5 0L39 13.6667V41H21.9375V27.3333H17.0625V41H0Z" fill="black"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('budgeting') }}" class="menu-item">
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
                <h1>Pengaturan Akun</h1>
                <p>Kelola informasi akun Anda</p>
            </div>

            <!-- Settings Card -->
            <div class="settings-card">
                <h2>Informasi Pribadi</h2>
                
                <form id="settingsForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" value="{{ $user->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon *</label>
                            <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '' }}" required>
                        </div>
                    </div>

                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #E3F5FF;">

                    <h2 style="margin-bottom: 20px;">Ubah Password (Opsional)</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('dashboard') }}'">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div class="loading-text">Menyimpan perubahan...</div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-icon" id="toastIcon"></div>
        <div class="toast-message" id="toastMessage"></div>
    </div>

    <script>
        // Handle form submission
        document.getElementById('settingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Get form data
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            // Validate password confirmation
            if (formData.password && formData.password !== formData.password_confirmation) {
                showToast('Password dan konfirmasi password tidak sama', 'error');
                return;
            }

            // Show loading
            document.getElementById('loadingOverlay').classList.add('active');

            try {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Send to API
                const response = await fetch('/settings/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                // Hide loading
                document.getElementById('loadingOverlay').classList.remove('active');

                if (result.success) {
                    showToast('✅ ' + result.message, 'success');
                    
                    // Clear password fields
                    document.getElementById('password').value = '';
                    document.getElementById('password_confirmation').value = '';
                } else {
                    if (result.errors) {
                        const firstError = Object.values(result.errors)[0][0];
                        showToast('❌ ' + firstError, 'error');
                    } else {
                        showToast('❌ ' + result.message, 'error');
                    }
                }

            } catch (error) {
                document.getElementById('loadingOverlay').classList.remove('active');
                console.error('Error:', error);
                showToast('❌ Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        });

        // Toast notification function
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
    </script>
</body>
</html>
