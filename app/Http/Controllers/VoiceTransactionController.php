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
            $data = [
                'jenis' => 'Pengeluaran', // Default
                'kategori' => 'Lainnya',
                'jumlah' => 0,
                'keterangan' => ucfirst($text),
                'budget_allocation' => null, // Default to null
                'goal_allocation' => null    // Default to null
            ];

            // 1. Deteksi Jumlah (Angka)
            // Mencari pola angka, bisa diikuti "ribu", "rb", "k", "juta", "jt"
            // Contoh: "50000", "50.000", "50 ribu", "50k", "1.5 juta"
            if (preg_match('/(\d+(?:[\.,]\d+)*)\s*(ribu|juta|rb|jt|k|m)?/i', $lowerText, $matches)) {
                // Bersihkan angka dari titik/koma ribuan (ambil digit dan titik desimal jika ada)
                // Asumsi format Indonesia: titik = ribuan, koma = desimal.
                // Tapi speech-to-text kadang tidak konsisten.
                // Pendekatan aman: ambil semua digit.
                
                $modifier = strtolower($matches[2] ?? '');
                $baseNumber = 0;

                if ($modifier) {
                    // Jika ada modifier, parsing angka dengan hati-hati (misal "1.5" atau "1,5")
                    $cleanNumStr = str_replace(',', '.', $matches[1]); // Ubah koma jadi titik desimal standar
                    $baseNumber = floatval($cleanNumStr);
                } else {
                    // Jika tidak ada modifier, anggap integer murni (hapus non-digit)
                    $baseNumber = floatval(preg_replace('/[^0-9]/', '', $matches[1]));
                }

                // Kalikan sesuai modifier
                if (in_array($modifier, ['ribu', 'rb', 'k'])) {
                    $baseNumber *= 1000;
                } elseif (in_array($modifier, ['juta', 'jt', 'm'])) {
                    $baseNumber *= 1000000;
                }
                
                $data['jumlah'] = $baseNumber;
            }

            // 2. Deteksi Jenis
            $pemasukanKeywords = ['dapat', 'terima', 'gaji', 'masuk', 'jual', 'income', 'tunjangan', 'bonus', 'thr'];
            foreach ($pemasukanKeywords as $keyword) {
                if (strpos($lowerText, $keyword) !== false) {
                    $data['jenis'] = 'Pemasukan';
                    break;
                }
            }

            // 3. Deteksi Kategori
            $kategoriMap = [
                'Makanan' => ['makan', 'minum', 'nasi', 'kopi', 'snack', 'jajan', 'warteg', 'restoran', 'cafe', 'roti', 'mie', 'bakso'],
                'Transport' => ['bensin', 'parkir', 'grab', 'gojek', 'tol', 'angkot', 'kereta', 'bus', 'ojek', 'taksi', 'uber'],
                'Belanja' => ['beli', 'belanja', 'supermarket', 'indomaret', 'alfamart', 'pasar', 'mall', 'shopee', 'tokopedia', 'baju', 'celana'],
                'Hiburan' => ['nonton', 'bioskop', 'game', 'main', 'liburan', 'wisata', 'jalan-jalan', 'spotify', 'netflix'],
                'Tagihan' => ['listrik', 'air', 'internet', 'wifi', 'pulsa', 'paket data', 'pln', 'pdam', 'bpjs', 'asuransi'],
                'Kesehatan' => ['obat', 'dokter', 'rumah sakit', 'klinik', 'vitamin', 'masker'],
                'Pendidikan' => ['buku', 'sekolah', 'kursus', 'kuliah', 'spp', 'les'],
                'Sedekah' => ['sedekah', 'infaq', 'zakat', 'donasi', 'sumbangan']
            ];

            foreach ($kategoriMap as $kategori => $keywords) {
                foreach ($keywords as $keyword) {
                    // Cek exact word match agar "makan" tidak match "makanan" (eh, gapapa sih)
                    // Tapi "beli" (Belanja) jangan sampai match "beli makan" (Makanan).
                    // Prioritas kategori di atas sudah cukup baik.
                    if (strpos($lowerText, $keyword) !== false) {
                        $data['kategori'] = $kategori;
                        break 2; // Break both loops
                    }
                }
            }
            
            // Jika jenis Pemasukan tapi kategori masih Lainnya/Default, coba tebak
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
}
