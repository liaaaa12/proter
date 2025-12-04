<!-- Header -->
<div class="header">
    <h1>Laporan</h1>
    <p>Yuk, lihat laporan anda!</p>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-header">
        <div class="filter-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                <path d="M19 4H5C3.89 4 3 4.9 3 6V8C3 8.55 3.45 9 4 9H20C20.55 9 21 8.55 21 8V6C21 4.9 20.11 4 19 4ZM19 20H5C3.89 20 3 19.1 3 18V16C3 15.45 3.45 15 4 15H20C20.55 15 21 15.45 21 16V18C21 19.1 20.11 20 19 20Z" fill="#2C3E50"/>
            </svg>
            Pilih Periode
        </div>
        <button class="btn-download" id="btnDownloadPdf">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="margin-right: 6px;">
                <path d="M19 9H15V3H9V9H5L12 16L19 9ZM5 18V20H19V18H5Z" fill="white"/>
            </svg>
            Unduh Laporan
        </button>
    </div>

    <div class="filter-controls">
        <div class="filter-label">Dari</div>
        
        <select class="filter-select" id="bulanDari">
            <option value="">Bulan</option>
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <option value="3">Maret</option>
            <option value="4">April</option>
            <option value="5">Mei</option>
            <option value="6">Juni</option>
            <option value="7">Juli</option>
            <option value="8">Agustus</option>
            <option value="9">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
        </select>

        <select class="filter-select" id="tahunDari">
            <option value="">Tahun</option>
            @foreach($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>

        <div class="filter-separator">-</div>

        <div class="filter-label">Sampai</div>

        <select class="filter-select" id="bulanSampai">
            <option value="">Bulan</option>
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <option value="3">Maret</option>
            <option value="4">April</option>
            <option value="5">Mei</option>
            <option value="6">Juni</option>
            <option value="7">Juli</option>
            <option value="8">Agustus</option>
            <option value="9">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
        </select>

        <select class="filter-select" id="tahunSampai">
            <option value="">Tahun</option>
            @foreach($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>

        <button class="btn-search" id="btnCari">Cari</button>
    </div>
</div>

<!-- Table Section -->
<div class="table-section">
    <div id="periodInfo" style="margin-bottom: 15px; font-size: 16px; font-weight: 600; color: #00456A; display: none;">
        Periode: <span id="periodText"></span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nominal</th>
                <th>Saldo</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                    Pilih periode dan klik "Cari" untuk menampilkan data
                </td>
            </tr>
        </tbody>
        <tfoot id="tableSummary" style="display: none;">
            <tr style="background: #E3F5FF; font-weight: 700;">
                <td colspan="2" style="text-align: right; padding: 15px 20px;">Total Pemasukan:</td>
                <td id="totalPemasukan" style="text-align: right; color: #2ecc71;"></td>
                <td colspan="2"></td>
            </tr>
            <tr style="background: #E3F5FF; font-weight: 700;">
                <td colspan="2" style="text-align: right; padding: 15px 20px;">Total Pengeluaran:</td>
                <td id="totalPengeluaran" style="text-align: right; color: #e74c3c;"></td>
                <td colspan="2"></td>
            </tr>
            <tr style="background: #00456A; color: white; font-weight: 700;">
                <td colspan="2" style="text-align: right; padding: 15px 20px;">Saldo Akhir:</td>
                <td id="saldoAkhir" style="text-align: right;"></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    /* Filter Section */
    .filter-section {
        background: white;
        padding: 20px 30px;
        border-radius: 10px;
        border: 1px solid #00456A;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        margin-bottom: 25px;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-title {
        display: flex;
        align-items: center;
        color: #2C3E50;
        font-size: 20px;
        font-weight: 600;
    }

    .filter-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-label {
        color: #676363;
        font-size: 16px;
        font-weight: 500;
        flex-shrink: 0;
    }

    .filter-separator {
        color: #676363;
        font-size: 20px;
        font-weight: 500;
        padding: 0 5px;
        flex-shrink: 0;
    }

    .filter-select {
        padding: 10px 20px;
        border: 2px solid #E3F5FF;
        border-radius: 10px;
        background: #E3E2E2;
        font-size: 16px;
        font-family: 'Inter', sans-serif;
        color: #676363;
        flex: 1;
        min-width: 120px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .filter-select:focus {
        outline: none;
        border-color: #00456A;
        background: white;
    }

    .btn-search {
        background: #557F96;
        color: white;
        padding: 10px 30px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #00456A80;
        flex-shrink: 0;
    }

    .btn-search:hover {
        background: #00456A;
        transform: translateY(-2px);
    }

    .btn-download {
        background: #557F96;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #00456A80;
        display: flex;
        align-items: center;
    }

    .btn-download:hover {
        background: #00456A;
        transform: translateY(-2px);
    }

    /* Table Section */
    .table-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        border: 1px solid #00456A;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    thead {
        background: #00456A;
        color: white;
    }

    th {
        padding: 15px 20px;
        text-align: left;
        font-size: 16px;
        font-weight: 600;
        white-space: nowrap;
    }

    td {
        padding: 15px 20px;
        text-align: left;
        font-size: 16px;
        border-bottom: 1px solid #E3F5FF;
        vertical-align: middle;
    }

    th:nth-child(1), td:nth-child(1) {
        white-space: nowrap;
        width: 15%;
    }

    th:nth-child(3), td:nth-child(3),
    th:nth-child(4), td:nth-child(4) {
        text-align: right;
        white-space: nowrap;
        font-family: 'Consolas', 'Monaco', monospace;
        font-weight: 600;
    }

    tbody tr:hover {
        background: #F8FCFF;
    }

    @media (max-width: 768px) {
        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-select {
            width: 100%;
        }

        .btn-search,
        .btn-download {
            width: 100%;
            justify-content: center;
        }

        .table-section {
            overflow-x: scroll;
        }
    }
</style>

<script>
    const API_URL = '{{ route("laporan.transactions") }}';
    const PDF_URL = '{{ route("laporan.export.pdf") }}';

    document.getElementById('btnCari').addEventListener('click', fetchTransactions);
    document.getElementById('btnDownloadPdf').addEventListener('click', downloadPdf);

    async function fetchTransactions() {
        const bulanDari = document.getElementById('bulanDari').value;
        const tahunDari = document.getElementById('tahunDari').value;
        const bulanSampai = document.getElementById('bulanSampai').value;
        const tahunSampai = document.getElementById('tahunSampai').value;

        if (!bulanDari || !tahunDari || !bulanSampai || !tahunSampai) {
            alert('Mohon lengkapi semua filter periode');
            return;
        }

        try {
            const response = await fetch(`${API_URL}?bulan_dari=${bulanDari}&tahun_dari=${tahunDari}&bulan_sampai=${bulanSampai}&tahun_sampai=${tahunSampai}`);
            const data = await response.json();

            if (data.success) {
                renderTable(data.transactions, data.summary);
                showPeriodInfo(bulanDari, tahunDari, bulanSampai, tahunSampai);
            } else {
                alert('Gagal mengambil data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data');
        }
    }

    function renderTable(transactions, summary) {
        const tbody = document.getElementById('tableBody');
        const tfoot = document.getElementById('tableSummary');

        if (transactions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #999;">Tidak ada data untuk periode ini</td></tr>';
            tfoot.style.display = 'none';
            return;
        }

        let html = '';
        let saldo = 0;

        transactions.forEach(trx => {
            const nominal = parseFloat(trx.jumlah);
            saldo += trx.jenis === 'Pemasukan' ? nominal : -nominal;

            html += `
                <tr>
                    <td>${formatDate(trx.tanggal)}</td>
                    <td>${trx.kategori}</td>
                    <td style="color: ${trx.jenis === 'Pemasukan' ? '#2ecc71' : '#e74c3c'}">
                        ${trx.jenis === 'Pemasukan' ? '+' : '-'} Rp ${formatNumber(nominal)}
                    </td>
                    <td>Rp ${formatNumber(saldo)}</td>
                    <td>${trx.keterangan}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        // Update summary
        document.getElementById('totalPemasukan').textContent = 'Rp ' + formatNumber(summary.total_pemasukan);
        document.getElementById('totalPengeluaran').textContent = 'Rp ' + formatNumber(summary.total_pengeluaran);
        document.getElementById('saldoAkhir').textContent = 'Rp ' + formatNumber(summary.saldo_akhir);
        tfoot.style.display = 'table-footer-group';
    }

    function showPeriodInfo(bulanDari, tahunDari, bulanSampai, tahunSampai) {
        const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const periodText = `${months[parseInt(bulanDari)]} ${tahunDari} - ${months[parseInt(bulanSampai)]} ${tahunSampai}`;
        document.getElementById('periodText').textContent = periodText;
        document.getElementById('periodInfo').style.display = 'block';
    }

    function downloadPdf() {
        const bulanDari = document.getElementById('bulanDari').value;
        const tahunDari = document.getElementById('tahunDari').value;
        const bulanSampai = document.getElementById('bulanSampai').value;
        const tahunSampai = document.getElementById('tahunSampai').value;

        if (!bulanDari || !tahunDari || !bulanSampai || !tahunSampai) {
            alert('Mohon pilih periode terlebih dahulu');
            return;
        }

        window.open(`${PDF_URL}?bulan_dari=${bulanDari}&tahun_dari=${tahunDari}&bulan_sampai=${bulanSampai}&tahun_sampai=${tahunSampai}`, '_blank');
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
</script>
