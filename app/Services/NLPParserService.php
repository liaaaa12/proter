<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * NLP Parser Service
 * Port dari Python nlp_parser.py ke PHP untuk parsing transaksi dari text
 */
class NLPParserService
{
    private $kategoriKeywords = [
        'Makanan' => [
            'makan', 'kopi', 'warung', 'restoran', 'jajan', 'sarapan',
            'minum', 'nasi', 'ayam', 'soto', 'bakso', 'mie', 'goreng',
            'cafe', 'kedai', 'snack', 'cemilan', 'gorengan', 'sate',
            'burger', 'pizza', 'roti', 'kue', 'minuman', 'teh', 'jus'
        ],
        'Transportasi' => [
            'bensin', 'grab', 'gojek', 'angkot', 'parkir', 'tol', 'ojek',
            'bus', 'kereta', 'taxi', 'uber', 'motor', 'mobil', 'bbm',
            'isi bensin', 'solar', 'pertamax', 'ongkos', 'transport'
        ],
        'Hiburan' => [
            'nonton', 'film', 'game', 'konser', 'liburan', 'wisata',
            'bioskop', 'cinema', 'netflix', 'spotify', 'youtube',
            'main', 'jalan-jalan', 'rekreasi', 'tiket'
        ],
        'Kebutuhan' => [
            'bayar', 'kos', 'listrik', 'air', 'wifi', 'pulsa', 'internet',
            'token', 'pdam', 'pln', 'cicilan', 'angsuran', 'sewa',
            'kontrak', 'tagihan', 'bill', 'pembayaran'
        ],
        'Belanja' => [
            'beli', 'belanja', 'shopping', 'baju', 'celana', 'sepatu',
            'tas', 'kosmetik', 'skincare', 'makeup', 'pakaian', 'fashion',
            'aksesoris', 'elektronik', 'gadget', 'hp', 'laptop'
        ],
        'Kesehatan' => [
            'dokter', 'rumah sakit', 'obat', 'apotek', 'vitamin',
            'medical', 'checkup', 'berobat', 'konsultasi', 'terapi',
            'rs', 'klinik', 'periksa'
        ],
        'Pendidikan' => [
            'kuliah', 'sekolah', 'kursus', 'les', 'buku', 'spp',
            'uang kuliah', 'pendidikan', 'belajar', 'training',
            'seminar', 'workshop'
        ],
        'Gaji' => [
            'gaji', 'salary', 'upah', 'honor', 'fee', 'penghasilan',
            'income', 'pendapatan', 'terima gaji'
        ],
        'Bonus' => [
            'bonus', 'thr', 'insentif', 'komisi', 'reward', 'hadiah',
            'dapat bonus', 'terima bonus'
        ],
        'Investasi' => [
            'investasi', 'saham', 'reksadana', 'deposito', 'dividen',
            'profit', 'return', 'bunga'
        ],
        'Tabungan' => [
            'nabung', 'tabungan', 'simpan', 'saving', 'menabung',
            'setor', 'transfer tabungan'
        ],
        'Sedekah' => [
            'sedekah', 'donasi', 'zakat', 'infaq', 'sumbangan',
            'amal', 'charity', 'derma'
        ],
        'Lainnya' => [
            'lain', 'other', 'misc', 'miscellaneous'
        ]
    ];

    private $pemasukanKeywords = [
        'masuk', 'dapat', 'terima', 'gaji', 'bonus', 'transfer masuk',
        'pendapatan', 'income', 'dibayar', 'diterima', 'hasil',
        'untung', 'profit', 'cashback'
    ];

    private $pengeluaranKeywords = [
        'beli', 'bayar', 'keluar', 'belanja', 'shopping', 'buat',
        'untuk', 'isi', 'transfer', 'kirim', 'spend', 'habis',
        'pakai', 'pake', 'ambil', 'tarik'
    ];

    /**
     * Parse text menjadi data transaksi
     */
    public function parse(string $text): array
    {
        Log::info("Parsing text: {$text}");

        $result = [
            'success' => false,
            'jenis' => null,
            'kategori' => null,
            'jumlah' => null,
            'keterangan' => null,
            'budget_allocation' => null,
            'goal_allocation' => null,
            'error' => null
        ];

        // 1. Extract nominal
        $nominal = $this->extractNominal($text);
        if ($nominal === null) {
            $result['error'] = 'Nominal tidak terdeteksi. Mohon sebutkan jumlah uang dengan jelas.';
            return $result;
        }

        $result['jumlah'] = $nominal;

        // 2. Detect jenis transaksi
        $result['jenis'] = $this->detectJenis($text);

        // 3. Detect kategori
        $result['kategori'] = $this->detectKategori($text);

        // 4. Extract keterangan
        $result['keterangan'] = $this->extractKeterangan($text, $nominal);

        // 5. Detect budget allocation
        $result['budget_allocation'] = $this->detectBudgetAllocation($text);

        // 6. Detect goal allocation
        $result['goal_allocation'] = $this->detectGoalAllocation($text);

        $result['success'] = true;
        Log::info("Parsing berhasil", $result);

        return $result;
    }

    /**
     * Extract nominal dari text
     */
    private function extractNominal(string $text): ?float
    {
        $text = strtolower($text);

        // Pattern untuk mendeteksi angka dengan satuan
        $patterns = [
            // Format: "4 juta", "4juta", "4 jt"
            '/(\d+(?:\.\d+)?)\s*(?:juta|jt)/i' => 1000000,
            // Format: "500 ribu", "500ribu", "500rb", "500k"
            '/(\d+(?:\.\d+)?)\s*(?:ribu|rb|k)\b/i' => 1000,
            // Format: "50 ratus ribu", "50 ratus rb"
            '/(\d+(?:\.\d+)?)\s*ratus\s*(?:ribu|rb)/i' => 100000,
            // Format: angka biasa "50000"
            '/(\d{4,})/' => 1,
        ];

        foreach ($patterns as $pattern => $multiplier) {
            if (preg_match($pattern, $text, $matches)) {
                $number = floatval($matches[1]);
                $nominal = $number * $multiplier;
                Log::info("Nominal terdeteksi: {$nominal}");
                return $nominal;
            }
        }

        Log::warning("Nominal tidak terdeteksi dalam text");
        return null;
    }

    /**
     * Detect kategori transaksi
     */
    private function detectKategori(string $text): string
    {
        $text = strtolower($text);

        foreach ($this->kategoriKeywords as $kategori => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    Log::info("Kategori terdeteksi: {$kategori} (keyword: {$keyword})");
                    return $kategori;
                }
            }
        }

        Log::warning("Kategori tidak terdeteksi, menggunakan 'Lainnya'");
        return 'Lainnya';
    }

    /**
     * Detect jenis transaksi (Pemasukan/Pengeluaran)
     */
    private function detectJenis(string $text): string
    {
        $text = strtolower($text);

        // Cek keyword pemasukan
        $pemasukanScore = 0;
        foreach ($this->pemasukanKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $pemasukanScore++;
            }
        }

        // Cek keyword pengeluaran
        $pengeluaranScore = 0;
        foreach ($this->pengeluaranKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $pengeluaranScore++;
            }
        }

        // Jika ada keyword gaji/bonus, pasti pemasukan
        $gajiKeywords = ['gaji', 'bonus', 'thr', 'salary'];
        foreach ($gajiKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                Log::info("Jenis terdeteksi: Pemasukan (keyword gaji/bonus)");
                return 'Pemasukan';
            }
        }

        // Bandingkan score
        if ($pemasukanScore > $pengeluaranScore) {
            Log::info("Jenis terdeteksi: Pemasukan (score: {$pemasukanScore})");
            return 'Pemasukan';
        } else {
            Log::info("Jenis terdeteksi: Pengeluaran (score: {$pengeluaranScore})");
            return 'Pengeluaran';
        }
    }

    /**
     * Extract keterangan dari text
     */
    private function extractKeterangan(string $text, float $nominal): string
    {
        // Remove nominal dari text
        $keterangan = $text;

        $patterns = [
            '/\d+(?:\.\d+)?\s*(?:juta|jt)/i',
            '/\d+(?:\.\d+)?\s*(?:ribu|rb|k)\b/i',
            '/\d+(?:\.\d+)?\s*ratus\s*(?:ribu|rb)/i',
            '/\d{4,}/'
        ];

        foreach ($patterns as $pattern) {
            $keterangan = preg_replace($pattern, '', $keterangan);
        }

        // Clean up whitespace
        $keterangan = preg_replace('/\s+/', ' ', $keterangan);

        // Remove kata-kata umum di awal
        $removeWords = ['aku', 'saya', 'barusan', 'baru', 'sudah', 'udah', 'tadi'];
        $words = explode(' ', trim($keterangan));
        $filteredWords = array_filter($words, function($word) use ($removeWords) {
            return !in_array(strtolower($word), $removeWords);
        });

        $keterangan = implode(' ', $filteredWords);

        Log::info("Keterangan: {$keterangan}");
        return trim($keterangan);
    }

    /**
     * Detect budget allocation dari text
     */
    private function detectBudgetAllocation(string $text): ?string
    {
        $text = strtolower($text);

        $patterns = [
            '/(?:untuk|ke|masuk(?:kan)?)\s+budget\s+(\w+)/i',
            '/(?:untuk|ke|masuk(?:kan)?)\s+anggaran\s+(\w+)/i',
            '/budget\s+(\w+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $budgetName = $matches[1];
                Log::info("Budget allocation terdeteksi: {$budgetName}");
                return $budgetName;
            }
        }

        return null;
    }

    /**
     * Detect goal allocation dari text
     */
    private function detectGoalAllocation(string $text): ?string
    {
        $text = strtolower($text);

        $patterns = [
            '/(?:untuk|ke|masuk(?:kan)?)\s+tabungan\s+(\w+)/i',
            '/(?:untuk|ke|masuk(?:kan)?)\s+nabung\s+(\w+)/i',
            '/(?:untuk|ke|masuk(?:kan)?)\s+goal\s+(\w+)/i',
            '/(?:untuk|ke|masuk(?:kan)?)\s+tujuan\s+(\w+)/i',
            '/tabungan\s+(\w+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $goalName = $matches[1];
                Log::info("Goal allocation terdeteksi: {$goalName}");
                return $goalName;
            }
        }

        return null;
    }
}
