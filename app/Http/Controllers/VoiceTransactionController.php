<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoiceTransactionController extends Controller
{
    /**
     * Store a new voice transaction
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'jenis' => 'required|in:Pemasukan,Pengeluaran',
                'kategori' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'required|string',
                'budget_id' => 'nullable|exists:budget,id',
                'goal_id' => 'nullable|exists:goals,id'
            ]);

            DB::beginTransaction();

            // 1. Simpan transaksi ke database
            $transactionId = DB::table('transaction')->insertGetId([
                'user_id' => auth()->id(),
                'tanggal' => now()->format('Y-m-d'), // Tanggal hari ini
                'jenis' => $validated['jenis'],
                'kategori' => $validated['kategori'],
                'jumlah' => $validated['jumlah'],
                'keterangan' => $validated['keterangan'],
                'budget_id' => $validated['budget_id'] ?? null, // ← ADDED
                'goal_id' => $validated['goal_id'] ?? null,     // ← ADDED
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Update budget jika ada alokasi budget
            if (!empty($validated['budget_id'])) {
                $budget = DB::table('budget')->where('id', $validated['budget_id'])->first();
                
                if ($budget) {
                    $newJumlahBerjalan = $budget->jumlahBerjalan + $validated['jumlah'];
                    
                    DB::table('budget')
                        ->where('id', $validated['budget_id'])
                        ->update([
                            'jumlahBerjalan' => $newJumlahBerjalan,
                            'updated_at' => now()
                        ]);
                    
                    Log::info("Budget updated: {$budget->namaBudget}, new amount: {$newJumlahBerjalan}");
                }
            }

            // 3. Update goal jika ada alokasi goal
            if (!empty($validated['goal_id'])) {
                $goal = DB::table('goals')->where('id', $validated['goal_id'])->first();
                
                if ($goal) {
                    $newNominalBerjalan = $goal->nominalBerjalan + $validated['jumlah'];
                    
                    DB::table('goals')
                        ->where('id', $validated['goal_id'])
                        ->update([
                            'nominalBerjalan' => $newNominalBerjalan,
                            'updated_at' => now()
                        ]);
                    
                    Log::info("Goal updated: {$goal->namaGoal}, new amount: {$newNominalBerjalan}");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'transaction_id' => $transactionId,
                    'jenis' => $validated['jenis'],
                    'kategori' => $validated['kategori'],
                    'jumlah' => $validated['jumlah']
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saving voice transaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing transaction
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Get the transaction
            $transaction = DB::table('transaction')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            // Validate input
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jenis' => 'required|in:Pemasukan,Pengeluaran',
                'kategori' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'required|string'
            ]);

            DB::beginTransaction();

            // Revert old budget/goal amounts
            if ($transaction->budget_id) {
                $budget = DB::table('budget')->where('id', $transaction->budget_id)->first();
                if ($budget) {
                    DB::table('budget')
                        ->where('id', $transaction->budget_id)
                        ->update([
                            'jumlahBerjalan' => $budget->jumlahBerjalan - $transaction->jumlah,
                            'updated_at' => now()
                        ]);
                }
            }

            if ($transaction->goal_id) {
                $goal = DB::table('goals')->where('id', $transaction->goal_id)->first();
                if ($goal) {
                    DB::table('goals')
                        ->where('id', $transaction->goal_id)
                        ->update([
                            'nominalBerjalan' => $goal->nominalBerjalan - $transaction->jumlah,
                            'updated_at' => now()
                        ]);
                }
            }

            // Update transaction
            DB::table('transaction')
                ->where('id', $id)
                ->update([
                    'tanggal' => $validated['tanggal'],
                    'jenis' => $validated['jenis'],
                    'kategori' => $validated['kategori'],
                    'jumlah' => $validated['jumlah'],
                    'keterangan' => $validated['keterangan'],
                    'updated_at' => now()
                ]);

            // Apply new budget/goal amounts
            if ($transaction->budget_id) {
                $budget = DB::table('budget')->where('id', $transaction->budget_id)->first();
                if ($budget) {
                    DB::table('budget')
                        ->where('id', $transaction->budget_id)
                        ->update([
                            'jumlahBerjalan' => $budget->jumlahBerjalan + $validated['jumlah'],
                            'updated_at' => now()
                        ]);
                }
            }

            if ($transaction->goal_id) {
                $goal = DB::table('goals')->where('id', $transaction->goal_id)->first();
                if ($goal) {
                    DB::table('goals')
                        ->where('id', $transaction->goal_id)
                        ->update([
                            'nominalBerjalan' => $goal->nominalBerjalan + $validated['jumlah'],
                            'updated_at' => now()
                        ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating transaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a transaction
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Get the transaction
            $transaction = DB::table('transaction')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Revert budget/goal amounts
            if ($transaction->budget_id) {
                $budget = DB::table('budget')->where('id', $transaction->budget_id)->first();
                if ($budget) {
                    DB::table('budget')
                        ->where('id', $transaction->budget_id)
                        ->update([
                            'jumlahBerjalan' => $budget->jumlahBerjalan - $transaction->jumlah,
                            'updated_at' => now()
                        ]);
                }
            }

            if ($transaction->goal_id) {
                $goal = DB::table('goals')->where('id', $transaction->goal_id)->first();
                if ($goal) {
                    DB::table('goals')
                        ->where('id', $transaction->goal_id)
                        ->update([
                            'nominalBerjalan' => $goal->nominalBerjalan - $transaction->jumlah,
                            'updated_at' => now()
                        ]);
                }
            }

            // Delete transaction
            DB::table('transaction')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting transaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all budgets for dropdown
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBudgets()
    {
        try {
            $budgets = DB::table('budget')
                ->where('user_id', auth()->id())
                ->select('id', 'namaBudget', 'kategori', 'jumlah', 'jumlahBerjalan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $budgets
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching budgets: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data budget'
            ], 500);
        }
    }

    /**
     * Get all goals for dropdown
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoals()
    {
        try {
            $goals = DB::table('goals')
                ->where('user_id', auth()->id())
                ->select('id', 'namaGoal', 'targetNominal', 'nominalBerjalan', 'tanggalTarget')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $goals
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching goals: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data goals'
            ], 500);
        }
    }

    /**
     * Parse voice text menjadi data transaksi
     * Endpoint baru untuk Web Speech API (browser-side)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parseVoiceText(Request $request)
    {
        try {
            $text = $request->input('text');
            if (empty($text)) {
                return response()->json(['success' => false, 'message' => 'Teks kosong'], 400);
            }

            $lowerText = strtolower($text);
            $userId = auth()->id();
            
            $data = [
                'jenis' => 'Pengeluaran', // Default
                'kategori' => 'Lainnya',
                'jumlah' => 0,
                'keterangan' => ucfirst($text),
                'budget_id' => null,
                'goal_id' => null,
                'budget_name' => null,
                'goal_name' => null
            ];

            // 1. Deteksi Jumlah (Angka + Terbilang)
            $jumlahDetected = $this->detectJumlah($lowerText);
            if ($jumlahDetected > 0) {
                $data['jumlah'] = $jumlahDetected;
            }

            // 2. Deteksi Jenis
            $pemasukanKeywords = ['dapat', 'terima', 'gaji', 'masuk', 'jual', 'income', 'tunjangan', 'bonus', 'thr'];
            foreach ($pemasukanKeywords as $keyword) {
                if (strpos($lowerText, $keyword) !== false) {
                    $data['jenis'] = 'Pemasukan';
                    break;
                }
            }

            // 3. Deteksi Kategori Dasar (Fallback) - DENGAN KATA GAUL
            $kategoriMap = [
                'Makanan' => [
                    'makan', 'minum', 'nasi', 'kopi', 'snack', 'jajan', 'warteg', 'restoran', 'cafe', 'roti', 'mie', 'bakso', 'soto', 'ayam', 'sate',
                    // Kata gaul:
                    'nasdang', 'naspad', 'nasi padang', 'mcd', 'kfc', 'mekdi', 'ricebowl', 'geprek', 'ngopi', 'nongkrong'
                ],
                'Transportasi' => [
                    'bensin', 'parkir', 'grab', 'gojek', 'tol', 'angkot', 'kereta', 'bus', 'ojek', 'taksi', 'uber', 'bengkel', 'transport', 'ojol',
                    // Kata gaul:
                    'goceng parkir', 'gocar', 'grabcar', 'grabbike', 'gojek', 'ojol'
                ],
                'Belanja' => [
                    'beli', 'belanja', 'supermarket', 'indomaret', 'alfamart', 'pasar', 'mall', 'shopee', 'tokopedia', 'baju', 'celana', 'skincare', 'kosmetik',
                    // Kata gaul:
                    'tokped', 'shopee', 'lazada', 'olshop', 'online shop', 'beli baju', 'shopping'
                ],
                'Hiburan' => [
                    'nonton', 'bioskop', 'game', 'main', 'spotify', 'netflix', 'youtube premium', 'konser',
                    // Kata gaul:
                    'netflik', 'ngefilm', 'ngegame', 'mlbb', 'mobile legend', 'pubg', 'steam'
                ],
                'Jalan-Jalan' => [
                    'liburan', 'wisata', 'jalan-jalan', 'traveling', 'trip', 'vacation', 'hotel', 'penginapan',
                    // Kata gaul:
                    'jalan', 'jalan jalan', 'piknik', 'refreshing', 'staycation'
                ],
                'Tagihan' => [
                    'listrik', 'air', 'internet', 'wifi', 'pulsa', 'paket data', 'pln', 'pdam', 'bpjs', 'asuransi', 'cicilan', 'kredit', 'pinjaman',
                    // Kata gaul:
                    'bayar listrik', 'token listrik', 'beli pulsa', 'isi pulsa', 'kuota'
                ],
                'Kesehatan' => [
                    'obat', 'dokter', 'rumah sakit', 'klinik', 'vitamin', 'masker', 'medical', 'checkup', 'lab',
                    // Kata gaul:
                    'ke dokter', 'berobat', 'beli obat', 'apotek'
                ],
                'Pendidikan' => [
                    'buku', 'sekolah', 'kursus', 'kuliah', 'spp', 'les', 'seminar', 'workshop', 'training',
                    // Kata gaul:
                    'bayar spp', 'beli buku', 'kursus online', 'udemy', 'coursera'
                ],
                'Sedekah' => [
                    'sedekah', 'infaq', 'zakat', 'donasi', 'sumbangan', 'amal', 'charity',
                    // Kata gaul:
                    'nyumbang', 'derma', 'bantuan'
                ],
                'Tabungan' => ['nabung', 'tabung', 'saving', 'simpan', 'investasi'],
                'Gaji' => ['gaji', 'salary', 'upah', 'honor'],
                'Bonus' => ['bonus', 'thr', 'insentif', 'komisi'],
                'Penjualan' => ['jual', 'penjualan', 'sales', 'omzet']
            ];

            foreach ($kategoriMap as $kategori => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($lowerText, $keyword) !== false) {
                        $data['kategori'] = $kategori;
                        break 2;
                    }
                }
            }
            
            // 4. SMART DETECTION: Cek Budget User
            $budgets = DB::table('budget')
                ->where('user_id', $userId)
                ->get(['id', 'namaBudget', 'kategori']);

            foreach ($budgets as $budget) {
                if (strpos($lowerText, strtolower($budget->namaBudget)) !== false) {
                    $data['budget_id'] = $budget->id;
                    $data['budget_name'] = $budget->namaBudget;
                    $data['jenis'] = 'Pengeluaran';
                    
                    if ($data['kategori'] == 'Lainnya' && $budget->kategori) {
                        $data['kategori'] = $budget->kategori;
                    }
                    
                    Log::info("Smart Match: Budget '{$budget->namaBudget}' detected.");
                    break;
                }
            }

            // 5. SMART DETECTION: Cek Goals User
            $goals = DB::table('goals')
                ->where('user_id', $userId)
                ->get(['id', 'namaGoal']);

            foreach ($goals as $goal) {
                if (strpos($lowerText, strtolower($goal->namaGoal)) !== false) {
                    $data['goal_id'] = $goal->id;
                    $data['goal_name'] = $goal->namaGoal;
                    $data['jenis'] = 'Pengeluaran';
                    $data['kategori'] = 'Tabungan';
                    
                    Log::info("Smart Match: Goal '{$goal->namaGoal}' detected.");
                    break;
                }
            }

            // 6. Clean up description - HAPUS kata mata uang dan angka
            $data['keterangan'] = $this->cleanDescription($text, $lowerText);
            
            // 7. Jika jenis Pemasukan tapi kategori masih Lainnya, coba tebak
            if ($data['jenis'] == 'Pemasukan' && $data['kategori'] == 'Lainnya') {
                if (strpos($lowerText, 'gaji') !== false) $data['kategori'] = 'Gaji';
                elseif (strpos($lowerText, 'bonus') !== false) $data['kategori'] = 'Bonus';
                elseif (strpos($lowerText, 'jual') !== false) $data['kategori'] = 'Penjualan';
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'raw_text' => $text
            ]);

        } catch (\Exception $e) {
            Log::error('Error parsing voice text: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat parsing text',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deteksi jumlah dari teks (support angka digit dan terbilang)
     */
    private function detectJumlah($text)
    {
        $jumlah = 0;
        
        // 1. Coba deteksi angka dengan format digit (10.000, 50rb, 2.5jt, dll)
        if (preg_match('/(\d+(?:[\\.,]\d+)*)\s*(ribu|juta|rb|jt|k|m|rp|rupiah)?/i', $text, $matches)) {
            $modifier = strtolower($matches[2] ?? '');
            $baseNumber = 0;

            if (in_array($modifier, ['ribu', 'rb', 'k'])) {
                $cleanNumStr = str_replace(',', '.', $matches[1]);
                $baseNumber = floatval($cleanNumStr) * 1000;
            } elseif (in_array($modifier, ['juta', 'jt', 'm'])) {
                $cleanNumStr = str_replace(',', '.', $matches[1]);
                $baseNumber = floatval($cleanNumStr) * 1000000;
            } else {
                // Angka tanpa modifier atau dengan "rp"/"rupiah"
                // Hapus semua titik dan koma, lalu convert
                $cleanNumStr = preg_replace('/[.,]/', '', $matches[1]);
                $baseNumber = floatval($cleanNumStr);
            }
            
            $jumlah = $baseNumber;
        }
        
        // 2. Jika tidak ada angka digit, coba deteksi terbilang
        if ($jumlah == 0) {
            $jumlah = $this->terbilangKeAngka($text);
        }
        
        return $jumlah;
    }

    /**
     * Convert terbilang ke angka (dari file lama yang sudah proven works!)
     * Contoh: "lima puluh ribu" → 50000
     */
    private function terbilangKeAngka($text)
    {
        $angkaMap = [
            'nol' => 0, 'satu' => 1, 'dua' => 2, 'tiga' => 3, 'empat' => 4, 'lima' => 5,
            'enam' => 6, 'tujuh' => 7, 'delapan' => 8, 'sembilan' => 9, 'sepuluh' => 10,
            'sebelas' => 11, 'belas' => 10, 'puluh' => 10, 'ratus' => 100, 'ribu' => 1000,
            'juta' => 1000000, 'miliar' => 1000000000, 'triliun' => 1000000000000
        ];

        $total = 0;
        $currentVal = 0;
        
        // Perbaiki kata khusus
        $text = str_replace('seribu', 'satu ribu', $text);
        $text = str_replace('seratus', 'satu ratus', $text);
        
        $words = explode(' ', strtolower($text));
        
        foreach ($words as $word) {
            // Skip jika bukan kata angka
            if (!isset($angkaMap[$word])) {
                continue;
            }
            
            $nilai = $angkaMap[$word];
            
            if ($nilai >= 1000) { // Multiplier: ribu, juta, dst.
                $total += ($currentVal !== 0 ? $currentVal : 1) * $nilai;
                $currentVal = 0;
            } elseif ($nilai === 100) { // Ratus
                $currentVal = ($currentVal !== 0 ? $currentVal : 1) * $nilai;
            } elseif ($nilai === 10) { // Puluh atau Belas
                if ($currentVal > 0 && $currentVal < 10) {
                    $currentVal += $nilai; // "lima belas" = 5 + 10 = 15
                } else {
                    $currentVal = ($currentVal !== 0 ? $currentVal : 1) * $nilai;
                }
            } else { // Satuan
                $currentVal += $nilai;
            }
        }
        
        return $total + $currentVal;
    }

    /**
     * Bersihkan deskripsi dari kata-kata yang tidak perlu
     * Hapus: angka, "rp", "rupiah", kata jenis transaksi, dll
     */
    private function cleanDescription($originalText, $lowerText)
    {
        // Kata-kata yang harus dihapus dari deskripsi
        $wordsToRemove = [
            // Mata uang
            'rp', 'rupiah', 'idr',
            // Jenis transaksi
            'pengeluaran', 'keluar', 'biaya', 'bayar', 'beli', 'pemasukan', 'masuk', 'pendapatan', 'dapat', 'terima',
            // Kata perintah
            'catat', 'tolong', 'untuk', 'sebesar',
            // Modifier angka
            'ribu', 'juta', 'miliar', 'rb', 'jt', 'k', 'm'
        ];
        
        // Split text menjadi array kata
        $words = explode(' ', $lowerText);
        $originalWords = explode(' ', $originalText);
        $cleanWords = [];
        
        for ($i = 0; $i < count($words); $i++) {
            $word = $words[$i];
            
            // Skip jika kata adalah angka (digit)
            if (preg_match('/\d/', $word)) {
                continue;
            }
            
            // Skip jika kata adalah terbilang angka
            $angkaWords = ['nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 
                          'sepuluh', 'sebelas', 'belas', 'puluh', 'ratus', 'ribu', 'juta', 'miliar', 'triliun',
                          'seribu', 'seratus'];
            if (in_array($word, $angkaWords)) {
                continue;
            }
            
            // Skip jika kata ada di daftar kata yang harus dihapus
            if (in_array($word, $wordsToRemove)) {
                continue;
            }
            
            // Jika lolos semua filter, masukkan ke clean words (pakai original case)
            $cleanWords[] = $originalWords[$i];
        }
        
        $cleanDescription = trim(implode(' ', $cleanWords));
        
        // Jika deskripsi kosong, gunakan teks asli tapi capitalize
        if (empty($cleanDescription)) {
            $cleanDescription = ucfirst($originalText);
        } else {
            $cleanDescription = ucfirst($cleanDescription);
        }
        
        return $cleanDescription;
    }
}
