<!-- Header -->
<div class="header">
    <h1>Pengaturan Akun</h1>
    <p>Kelola informasi akun Anda</p>
</div>

<!-- Settings Card -->
<div class="settings-card">
    <h2>Informasi Pribadi</h2>
    
    <form id="settingsForm">
        @csrf
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

<style>
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    @media (max-width: 768px) {
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
            alert('Password dan konfirmasi password tidak sama');
            return;
        }

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Send to API
            const response = await fetch('{{ route("settings.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                alert('✅ ' + result.message);
                
                // Clear password fields
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            } else {
                if (result.errors) {
                    const firstError = Object.values(result.errors)[0][0];
                    alert('❌ ' + firstError);
                } else {
                    alert('❌ ' + result.message);
                }
            }

        } catch (error) {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
        }
    });
</script>
