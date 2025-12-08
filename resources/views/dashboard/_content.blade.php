@php
    // Data transaksi dari controller atau dummy data
    $transactions = $transactions ?? collect([
        ['name' => 'Gaji',     'date' => '10/10/2025', 'amount' => 1000000],
        ['name' => 'Shopping', 'date' => '5/10/2025',  'amount' => -200000],
        ['name' => 'Makan',    'date' => '5/10/2025',  'amount' => -50000],
        ['name' => 'Sedekah',  'date' => '1/10/2025',  'amount' => -150000],
    ]);

    $totalIncome  = isset($totalPemasukan) ? $totalPemasukan : $transactions->where('amount', '>', 0)->sum('amount');
    $totalExpense = isset($totalPengeluaran) ? $totalPengeluaran : abs($transactions->where('amount', '<', 0)->sum('amount'));
    $saldo = isset($saldo) ? $saldo : ($totalIncome - $totalExpense);

    $total = $totalIncome + $totalExpense;
    $incomePerc  = $total ? round($totalIncome  / $total * 100) : 0;
    $expensePerc = 100 - $incomePerc;

    // Posisi label di pie chart
    $incomeAngle  = -90 + ($incomePerc * 3.6 / 2);
    $expenseAngle = -90 + ($incomePerc * 3.6) + ($expensePerc * 3.6 / 2);
@endphp

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
        <span class="voice-btn-text">Transaksi dengan Suara</span>
    </button>
</div>

<!-- Cards Grid -->
<div class="cards-grid">
    <div class="card balance">
        <div class="card-icon">💳</div>
        <div class="card-content">
            <h3>Saldo saat ini</h3>
            <div class="amount">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
        </div>
    </div>

    @if(isset($goal) && $goal)
    <div class="card target">
        <div class="card-icon">🎯</div>
        <div class="card-content">
            <h3>{{ $goal->namaGoal }}</h3>
            <div class="amount">
                {{ number_format($goalPercentage ?? 0, 1) }}%
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ min($goalPercentage ?? 0, 100) }}%;"></div>
            </div>
        </div>
    </div>
    @endif

    <div class="card income">
        <div class="card-icon">📈</div>
        <div class="card-content">
            <h3>Pemasukan</h3>
            <div class="amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card expense">
        <div class="card-icon">📉</div>
        <div class="card-content">
            <h3>Pengeluaran</h3>
            <div class="amount">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
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
                    <div class="chart-label chart-label-income" style="--angle: {{ $incomeAngle }}deg;">
                        {{ $incomePerc }}%
                    </div>
                    <div class="chart-label chart-label-expense" style="--angle: {{ $expenseAngle }}deg;">
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
                @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                    @foreach($recentTransactions as $transaction)
                    <div class="transaction-item">
                        <div class="transaction-name">{{ $transaction->keterangan }}</div>
                        <div class="transaction-date">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</div>
                        <div class="transaction-amount {{ $transaction->jenis == 'Pemasukan' ? 'income' : 'expense' }}">
                            {{ $transaction->jenis == 'Pemasukan' ? '+ ' : '- ' }}Rp{{ number_format($transaction->jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                @else
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
                @endif
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
                    @if(isset($budgets))
                        @foreach($budgets as $budget)
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
                        @foreach($goals as $goalItem)
                        <option value="{{ $goalItem->id }}">{{ $goalItem->namaGoal }}</option>
                        @endforeach
                    @endif
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
            if (voiceText) voiceText.textContent = 'Transaksi dengan Suara';
        }
    }

    async function sendTextToAPI(text) {
        showLoading('Memproses text...');
        
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            console.log('Sending text to API:', text);
            console.log('CSRF Token:', csrfToken ? 'Found' : 'NOT FOUND!');
            
            if (!csrfToken) {
                hideLoading();
                showToast('❌ CSRF token tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }
            
            // Send to Laravel API for parsing
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
            
            // Handle 419 CSRF error specifically
            if (response.status === 419) {
                hideLoading();
                showToast('❌ Session expired. Silakan refresh halaman (F5).', 'error');
                return;
            }
            
            const result = await response.json();
            console.log('API Response:', result);
            
            hideLoading();
            
            if (result.success) {
                // Auto-fill form with parsed data
                autoFillForm(result.data);
                
                // Show modal
                openModal();
                
                showToast(`✅ Terdeteksi: "${result.raw_text}"`, 'success');
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
        
        console.log('Saving transaction:', formData);
        
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                hideLoading();
                showToast('❌ CSRF token tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }
            
            // Send to Laravel API
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
            
            // Handle 419 CSRF error specifically
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
                
                // Reload page after 1.5 seconds
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
    // Use event delegation for voice button since it might be dynamic
    document.addEventListener('click', function(e) {
        if (e.target.closest('.voice-btn')) {
            startVoiceRecording();
        }
    });
    
    // Close modal on overlay click
    const voiceModal = document.getElementById('voiceModal');
    if (voiceModal) {
        voiceModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
</script>
