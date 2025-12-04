@php
    // Gunakan data goals dari controller, atau dummy data jika belum ada
    $goalsData = $goals ?? collect([
        (object)['id' => 1, 'namaGoal' => 'Dana Darurat', 'tanggalTarget' => '100 hari lagi', 'nominalBerjalan' => 500000, 'targetNominal' => 1000000],
        (object)['id' => 2, 'namaGoal' => 'Haji dan Umroh', 'tanggalTarget' => '5 tahun lagi', 'nominalBerjalan' => 500000, 'targetNominal' => 1000000],
    ]);
@endphp

<!-- Goals Header -->
<div class="header goals-header">
    <div class="header-text">
        <h1>Target Keuangan</h1>
        <p>Wujudkan impianmu satu per satu</p>
    </div>
    <button class="btn-add-goal" onclick="openGoalModal()">
        + Tambah Target
    </button>
</div>

<!-- Add Goal Button -->
<div class="goals-actions" style="display: none;">
    <button class="btn-add-goal" id="btn-add-goal">+ Tambah Target</button>
</div>

<!-- Goals List -->
<div class="goals-list">
    @forelse($goalsData as $index => $goal)
        <div class="goal-card" data-goal-id="{{ $goal->id }}" onclick="showHistory({{ $goal->id }})" style="cursor: pointer;">
            <div class="goal-header color-{{ ($index % 5) + 1 }}">
                <div class="goal-icon">🎯</div>
                <div class="goal-title">
                    <h3>{{ $goal->namaGoal }}</h3>
                    @php
                        $targetDate = \Carbon\Carbon::parse($goal->tanggalTarget);
                        $daysText = $targetDate->isPast() ? 'Tercapai / Lewat' : $targetDate->diffForHumans(now(), true) . ' lagi';
                    @endphp
                    <p class="goal-days">{{ $daysText }} ({{ $targetDate->format('d M Y') }})</p>
                </div>
                <div class="goal-actions">
                    <button class="goal-btn-edit" title="Edit" onclick="event.stopPropagation(); editGoal({{ $goal->id }}, '{{ $goal->namaGoal }}', {{ $goal->targetNominal }}, {{ $goal->nominalBerjalan }}, '{{ $goal->tanggalTarget }}')">✏️</button>
                    <button class="goal-btn-delete" title="Delete" onclick="event.stopPropagation(); deleteGoal({{ $goal->id }})">🗑️</button>
                </div>
            </div>
            <div class="goal-body">
                <div class="goal-row">
                    <span class="goal-label">Terkumpul</span>
                    <span class="goal-amount">Rp{{ number_format($goal->nominalBerjalan, 0, ',', '.') }}</span>
                </div>
                <div class="goal-row">
                    <span class="goal-label">Target</span>
                    <span class="goal-amount">Rp{{ number_format($goal->targetNominal, 0, ',', '.') }}</span>
                </div>
                <div class="goal-progress">
                    @php
                        $percentage = $goal->targetNominal > 0 ? round(($goal->nominalBerjalan / $goal->targetNominal) * 100) : 0;
                    @endphp
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $percentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">🎯</div>
            <h3>Belum Ada Goals</h3>
            <p>Mulai atur target keuangan Anda untuk masa depan yang lebih baik!</p>
            <button class="btn-add-goal" style="margin-top: 10px;" onclick="document.getElementById('btn-add-goal').click()">+ Tambah Target Pertama</button>
        </div>
    @endforelse
</div>

<!-- Goal Form (Modal-like) - Add & Edit -->
<div id="goal-form-container" class="goal-form-container" style="display: none;">
    <div class="goal-form">
        <h3 id="form-title">Tambah Goal Baru</h3>
        <form id="goal-form" method="POST" action="{{ route('goals.store') }}">
            @csrf
            <input type="hidden" id="form-mode" value="add">
            <input type="hidden" id="form-goal-id">
            
            <div class="form-group">
                <label for="namaGoal">Nama Goal</label>
                <input type="text" id="namaGoal" name="namaGoal" placeholder="Contoh: Dana Darurat" required>
            </div>
            <div class="form-group">
                <label for="targetNominal">Target Nominal</label>
                <input type="number" id="targetNominal" name="targetNominal" placeholder="Rp" required>
            </div>
            <div class="form-group">
                <label for="nominalBerjalan">Nominal Berjalan</label>
                <input type="number" id="nominalBerjalan" name="nominalBerjalan" placeholder="Rp (Opsional, default 0)">
            </div>
            <div class="form-group">
                <label for="tanggalTarget">Target Tanggal</label>
                <input type="date" id="tanggalTarget" name="tanggalTarget" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save" id="form-submit-btn">Simpan</button>
                <button type="button" class="btn-cancel" id="form-cancel-btn">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- History Modal -->
<div class="modal-overlay" id="historyModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 id="historyModalTitle">Riwayat Transaksi</h2>
            <button class="close-btn" onclick="closeHistoryModal()">&times;</button>
        </div>
        <div class="history-list" id="historyList" style="max-height: 400px; overflow-y: auto;">
            <!-- Transaksi akan dimuat di sini -->
            <div style="text-align:center; padding:20px;">Memuat data...</div>
        </div>
    </div>
</div>

<!-- Voice Modal, Loading, Toast removed (moved to layout) -->

<style>
    .goals-header {
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

    .goals-actions {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 25px;
    }

    .btn-add-goal {
        background: rgba(0, 69, 106, 0.7);
        color: white;
        padding: 12px 28px;
        border-radius: 100px;
        border: 1px solid #00456A;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-add-goal:hover {
        background: #00456A;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 69, 106, 0.3);
    }

    .goals-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .goal-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        border: 1px solid #E0E0E0;
    }

    .goal-header {
        padding: 15px 20px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .goal-header.color-1 { background: #6B9BD1; }
    .goal-header.color-2 { background: #D1786B; }
    .goal-header.color-3 { background: #D19E6B; }
    .goal-header.color-4 { background: #6BC1D1; }
    .goal-header.color-5 { background: #9E6BD1; }

    .goal-icon {
        width: 45px;
        height: 45px;
        background: #DDE6E6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .goal-title {
        flex: 1;
    }

    .goal-title h3 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }

    .goal-days {
        font-size: 13px;
        opacity: 0.9;
        margin: 2px 0 0 0;
        font-weight: 400;
    }

    .goal-days {
        font-size: 14px;
        opacity: 0.9;
        margin: 5px 0 0 0;
    }

    .goal-actions {
        display: flex;
        gap: 10px;
    }

    .goal-btn-edit,
    .goal-btn-delete {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        transition: background 0.3s;
    }

    .goal-btn-edit:hover,
    .goal-btn-delete:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .goal-body {
        padding: 20px;
    }

    .goal-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #F0F0F0;
    }

    .goal-row:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .goal-label {
        font-size: 16px;
        color: #666;
        font-weight: 500;
    }

    .goal-amount {
        font-size: 18px;
        font-weight: 700;
        color: #2C3E50;
        text-align: right;
    }

    .goal-progress {
        margin-top: 15px;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #E0E0E0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: #6B9BD1;
        transition: width 0.3s ease;
    }

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

    @media (max-width: 768px) {
        .goal-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .goal-actions {
            margin-top: 10px;
            width: 100%;
            justify-content: flex-end;
        }

        .goal-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .goal-amount {
            text-align: left;
        }

        .goal-form {
            max-width: 100%;
            width: 95%;
        }
    }
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 10px;
        border: 2px dashed #00456A;
        margin-top: 20px;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #2C3E50;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #666;
        font-size: 16px;
        margin-bottom: 20px;
    }
</style>

<script>
    // ========== UNIFIED GOAL FORM (ADD & EDIT) ==========
    // Get form elements (they persist in partial)
    const goalForm = document.getElementById('goal-form');
    const goalFormContainer = document.getElementById('goal-form-container');
    const formMode = document.getElementById('form-mode');
    const formGoalId = document.getElementById('form-goal-id');
    const formTitle = document.getElementById('form-title');
    const formSubmitBtn = document.getElementById('form-submit-btn');

    // Function to open form in EDIT mode
    function editGoal(id, namaGoal, targetNominal, nominalBerjalan, tanggalTarget) {
        formMode.value = 'edit';
        formGoalId.value = id;
        formTitle.textContent = 'Edit Goal';
        formSubmitBtn.textContent = 'Update';
        
        document.getElementById('namaGoal').value = namaGoal;
        document.getElementById('targetNominal').value = targetNominal;
        document.getElementById('nominalBerjalan').value = nominalBerjalan;
        document.getElementById('tanggalTarget').value = tanggalTarget;
        
        goalFormContainer.style.display = 'flex';
    }

    // Function to delete goal
    function deleteGoal(id) {
        Swal.fire({
            title: 'Hapus Goals?',
            text: 'Apakah Anda yakin ingin menghapus goals ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ED6363',
            cancelButtonColor: '#999',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_method', 'DELETE');
                data.append('_token', document.querySelector('[name="_token"]').value);

                fetch(`/goals/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: data
                })
                .then(resp => resp.json())
                .then(result => {
                    if (result.success) {
                        const goalCard = document.querySelector(`[data-goal-id="${id}"]`);
                        if (goalCard) {
                            goalCard.remove();
                        }
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Goal berhasil dihapus.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus goal',
                        icon: 'error'
                    });
                });
            }
        });
    }

    // Function to update an existing goal card
    function updateGoalCard(goal) {
        const goalCard = document.querySelector(`[data-goal-id="${goal.id}"]`);
        if (goalCard) {
            const percentage = goal.targetNominal > 0 ? Math.round((goal.nominalBerjalan / goal.targetNominal) * 100) : 0;
            
            goalCard.querySelector('h3').textContent = goal.namaGoal;
            goalCard.querySelector('.goal-days').textContent = goal.tanggalTarget;
            
            const rows = goalCard.querySelectorAll('.goal-row');
            rows[0].querySelector('.goal-amount').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(goal.nominalBerjalan);
            rows[1].querySelector('.goal-amount').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(goal.targetNominal);
            
            goalCard.querySelector('.progress-fill').style.width = percentage + '%';
            
            const editBtn = goalCard.querySelector('.goal-btn-edit');
            editBtn.setAttribute('onclick', `editGoal(${goal.id}, '${goal.namaGoal}', ${goal.targetNominal}, ${goal.nominalBerjalan}, '${goal.tanggalTarget}')`);
        }
    }

    // Function to add new goal card
    function addGoalCard(goal) {
        const goalsList = document.querySelector('.goals-list');
        if (!goalsList) return;

        // Remove empty state if exists
        const emptyState = goalsList.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        const percentage = goal.targetNominal > 0 ? Math.round((goal.nominalBerjalan / goal.targetNominal) * 100) : 0;
        
        // Calculate color index based on current number of cards
        const cardCount = goalsList.querySelectorAll('.goal-card').length;
        const colorIndex = (cardCount % 5) + 1;

        const newCard = document.createElement('div');
        newCard.className = 'goal-card';
        newCard.setAttribute('data-goal-id', goal.id);
        newCard.setAttribute('onclick', `showHistory(${goal.id})`);
        newCard.style.cursor = 'pointer';

        // Calculate days left
        const targetDate = new Date(goal.tanggalTarget);
        const today = new Date();
        const diffTime = targetDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
        let daysText = diffDays > 0 ? diffDays + ' hari lagi' : 'Tercapai / Lewat';
        const dateFormatted = targetDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

        newCard.innerHTML = `
            <div class="goal-header color-${colorIndex}">
                <div class="goal-icon">🎯</div>
                <div class="goal-title">
                    <h3>${goal.namaGoal}</h3>
                    <p class="goal-days">${daysText} (${dateFormatted})</p>
                </div>
                <div class="goal-actions">
                    <button class="goal-btn-edit" title="Edit" onclick="event.stopPropagation(); editGoal(${goal.id}, '${goal.namaGoal}', ${goal.targetNominal}, ${goal.nominalBerjalan}, '${goal.tanggalTarget}')">✏️</button>
                    <button class="goal-btn-delete" title="Delete" onclick="event.stopPropagation(); deleteGoal(${goal.id})">🗑️</button>
                </div>
            </div>
            <div class="goal-body">
                <div class="goal-row">
                    <span class="goal-label">Terkumpul</span>
                    <span class="goal-amount">Rp${new Intl.NumberFormat('id-ID').format(goal.nominalBerjalan)}</span>
                </div>
                <div class="goal-row">
                    <span class="goal-label">Target</span>
                    <span class="goal-amount">Rp${new Intl.NumberFormat('id-ID').format(goal.targetNominal)}</span>
                </div>
                <div class="goal-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${percentage}%;"></div>
                    </div>
                </div>
            </div>
        `;

        // Insert at the beginning (since we order by desc)
        goalsList.insertBefore(newCard, goalsList.firstChild);
    }

    // ===== EVENT HANDLERS WITH EVENT DELEGATION =====
    
    // Add goal button
    document.addEventListener('click', function(e) {
        if (e.target.closest('#btn-add-goal')) {
            formMode.value = 'add';
            formGoalId.value = '';
            formTitle.textContent = 'Tambah Goal Baru';
            formSubmitBtn.textContent = 'Simpan';
            goalForm.reset();
            goalFormContainer.style.display = 'flex';
        }
    });

    // Close form button
    document.addEventListener('click', function(e) {
        if (e.target.closest('#form-cancel-btn')) {
            goalFormContainer.style.display = 'none';
            goalForm.reset();
            formMode.value = 'add';
            formGoalId.value = '';
        }
    });

    // Close form when clicking outside
    goalFormContainer.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            goalForm.reset();
            formMode.value = 'add';
            formGoalId.value = '';
        }
    });

    // Handle form submission
    goalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('.btn-save');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = formMode.value === 'add' ? 'Menyimpan...' : 'Mengupdate...';

        const isEditMode = formMode.value === 'edit';
        const url = isEditMode ? `/goals/${formGoalId.value}` : '{{ route('goals.store') }}';

        const data = new FormData();
        if (isEditMode) {
            data.append('_method', 'PUT');
        }
        data.append('_token', document.querySelector('#goal-form [name="_token"]').value);
        data.append('namaGoal', document.getElementById('namaGoal').value);
        data.append('targetNominal', document.getElementById('targetNominal').value);
        
        const nominalBerjalan = document.getElementById('nominalBerjalan').value;
        data.append('nominalBerjalan', nominalBerjalan === '' ? '0' : nominalBerjalan);
        
        data.append('tanggalTarget', document.getElementById('tanggalTarget').value);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: data
        })
        .then(resp => resp.json())
        .then(result => {
            if (result.success) {
                const goal = result.goal;
                
                if (isEditMode) {
                    updateGoalCard(goal);
                } else {
                    addGoalCard(goal);
                }
                
                const messageText = isEditMode 
                    ? 'Goal berhasil diperbarui.' 
                    : 'Goal baru berhasil ditambahkan.';
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: messageText,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                goalFormContainer.style.display = 'none';
                goalForm.reset();
                formMode.value = 'add';
                formGoalId.value = '';
                
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            } else {
                throw result;
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            
            let errorMsg = isEditMode 
                ? 'Terjadi kesalahan saat mengupdate goal' 
                : 'Terjadi kesalahan saat menambahkan goal';
            
            if (err.message) {
                errorMsg = err.message;
            }
            
            Swal.fire({
                title: 'Perhatian!',
                text: errorMsg,
                icon: err.status === 'warning' ? 'warning' : 'error'
            });
        });
    });
</script>

<script>
    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        const historyModal = document.getElementById('historyModal');
        if (historyModal) {
            historyModal.addEventListener('click', function(e) {
                if (e.target === this) closeHistoryModal();
            });
        }
    });

    // History Functions
    function showHistory(goalId) {
        const modal = document.getElementById('historyModal');
        const list = document.getElementById('historyList');
        const title = document.getElementById('historyModalTitle');
        
        modal.classList.add('active');
        list.innerHTML = '<div style="text-align:center; padding:20px;">Memuat data...</div>';
        
        fetch(`/api/goals/${goalId}/transactions`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    title.textContent = `Riwayat: ${data.budget_name}`;
                    if(data.transactions.length === 0) {
                        list.innerHTML = '<div style="text-align:center; padding:20px; color:#666;">Belum ada transaksi untuk goal ini.</div>';
                    } else {
                        let html = '';
                        data.transactions.forEach(trx => {
                            const date = new Date(trx.tanggal).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                            const amount = new Intl.NumberFormat('id-ID').format(trx.jumlah);
                            html += `
                                <div class="transaction-item" style="margin-bottom:10px; border-left: 4px solid #6B9BD1; background: #F0F7FF; padding: 10px; border-radius: 5px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <div class="transaction-name" style="font-weight:600;">${trx.keterangan || 'Tabungan'}</div>
                                        <div class="transaction-amount income" style="color:#00A311; font-weight:bold;">+ Rp${amount}</div>
                                    </div>
                                    <div class="transaction-date" style="font-size:12px; color:#888; margin-top:4px;">${date}</div>
                                </div>
                            `;
                        });
                        list.innerHTML = html;
                    }
                } else {
                    list.innerHTML = '<div style="color:red; text-align:center;">Gagal memuat data.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                list.innerHTML = '<div style="color:red; text-align:center;">Terjadi kesalahan.</div>';
            });
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.remove('active');
    }
</script>
