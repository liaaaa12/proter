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
        <h1>Goals 🚀</h1>
        <p>Yang penting bukan nominalnya, tapi niatnya. Asal jangan lupa isi lagi, bukan tarik lagi !</p>
    </div>
    <button class="voice-btn">
        <svg width="24" height="30" viewBox="0 0 38 48" fill="none">
            <path d="M38 20.8929C38 20.6571 37.7927 20.4643 37.5394 20.4643H34.0849C33.8315 20.4643 33.6242 20.6571 33.6242 20.8929C33.6242 28.4089 27.0779 34.5 19 34.5C10.9221 34.5 4.37576 28.4089 4.37576 20.8929C4.37576 20.6571 4.16849 20.4643 3.91515 20.4643H0.460606C0.207273 20.4643 0 20.6571 0 20.8929C0 29.9304 7.28909 37.3875 16.697 38.4429V43.9286H8.33121C7.54243 43.9286 6.90909 44.6946 6.90909 45.6429V47.5714C6.90909 47.8071 7.0703 48 7.26606 48H30.7339C30.9297 48 31.0909 47.8071 31.0909 47.5714V45.6429C31.0909 44.6946 30.4576 43.9286 29.6688 43.9286H21.0727V38.4696C30.59 37.5054 38 30.0054 38 20.8929ZM19 30C24.4064 30 28.7879 25.9714 28.7879 21V9C28.7879 4.02857 24.4064 0 19 0C13.5936 0 9.21212 4.02857 9.21212 9V21C9.21212 25.9714 13.5936 30 19 30Z" fill="white"/>
        </svg>
        Tekan Untuk Bersuara
    </button>
</div>

<!-- Add Goal Button -->
<div class="goals-actions">
    <button class="btn-add-goal" id="btn-add-goal">+ Tambah Target</button>
</div>

<!-- Goals List -->
<div class="goals-list">
    @forelse($goalsData as $goal)
        <div class="goal-card" data-goal-id="{{ $goal->id }}">
            <div class="goal-header">
                <div class="goal-header-left">
                    <div class="goal-icon">🎯</div>
                    <div>
                        <h3>{{ $goal->namaGoal }}</h3>
                        <p class="goal-days">{{ $goal->tanggalTarget }}</p>
                    </div>
                </div>
                <div class="goal-actions">
                    <button class="goal-btn-edit" title="Edit" onclick="editGoal({{ $goal->id }}, '{{ $goal->namaGoal }}', {{ $goal->targetNominal }}, {{ $goal->nominalBerjalan }}, '{{ $goal->tanggalTarget }}')">✏️</button>
                    <button class="goal-btn-delete" title="Delete" onclick="deleteGoal({{ $goal->id }})">🗑️</button>
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
        <p>Belum ada goal.</p>
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
                <input type="number" id="nominalBerjalan" name="nominalBerjalan" placeholder="Rp" required>
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
        background: #2A8576;
        color: white;
        padding: 12px 28px;
        border-radius: 100px;
        border: none;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-add-goal:hover {
        background: #1C5A50;
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
        background: linear-gradient(135deg, #2A8576 0%, #1C5A50 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .goal-header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .goal-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .goal-header-left h3 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
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
        background: linear-gradient(90deg, #6B9BD1 0%, #2A8576 100%);
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

        const percentage = goal.targetNominal > 0 ? Math.round((goal.nominalBerjalan / goal.targetNominal) * 100) : 0;

        const newCard = document.createElement('div');
        newCard.className = 'goal-card';
        newCard.setAttribute('data-goal-id', goal.id);
        newCard.innerHTML = `
            <div class="goal-header">
                <div class="goal-header-left">
                    <div class="goal-icon">🎯</div>
                    <div>
                        <h3>${goal.namaGoal}</h3>
                        <p class="goal-days">${goal.tanggalTarget}</p>
                    </div>
                </div>
                <div class="goal-actions">
                    <button class="goal-btn-edit" title="Edit" onclick="editGoal(${goal.id}, '${goal.namaGoal}', ${goal.targetNominal}, ${goal.nominalBerjalan}, '${goal.tanggalTarget}')">✏️</button>
                    <button class="goal-btn-delete" title="Delete" onclick="deleteGoal(${goal.id})">🗑️</button>
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

        goalsList.appendChild(newCard);
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
        data.append('nominalBerjalan', document.getElementById('nominalBerjalan').value);
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
