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
                'jenis' => $validated['jenis'],
                'kategori' => $validated['kategori'],
                'jumlah' => $validated['jumlah'],
                'keterangan' => $validated['keterangan'],
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
     * Get all budgets for dropdown
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBudgets()
    {
        try {
            $budgets = DB::table('budget')
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
            // Validasi input
            $validated = $request->validate([
                'text' => 'required|string'
            ]);

            $text = $validated['text'];
            Log::info("Parsing voice text: {$text}");

            // Parse menggunakan NLPParserService
            $parser = new \App\Services\NLPParserService();
            $result = $parser->parse($text);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                    'raw_text' => $text
                ], 400);
            }

            // Return hasil parsing
            return response()->json([
                'success' => true,
                'data' => [
                    'jenis' => $result['jenis'],
                    'kategori' => $result['kategori'],
                    'jumlah' => $result['jumlah'],
                    'keterangan' => $result['keterangan'],
                    'budget_allocation' => $result['budget_allocation'],
                    'goal_allocation' => $result['goal_allocation']
                ],
                'raw_text' => $text
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

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
