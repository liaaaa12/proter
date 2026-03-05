<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TransactionService;
use App\Services\NLPParserService;
use Illuminate\Support\Facades\DB;

class VoiceTransactionController extends Controller
{
    protected TransactionService $transactionService;
    protected NLPParserService $nlpParserService;

    public function __construct(TransactionService $transactionService, NLPParserService $nlpParserService)
    {
        $this->transactionService = $transactionService;
        $this->nlpParserService = $nlpParserService;
    }

    /**
     * Store a new transaction
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'jenis' => 'required|in:Pemasukan,Pengeluaran',
                'kategori' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'required|string',
                'budget_id' => 'nullable|exists:budget,id',
                'goal_id' => 'nullable|exists:goals,id'
            ]);

            $transactionId = $this->transactionService->store(auth()->id(), $validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan',
                    'transaction_id' => $transactionId
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi berhasil disimpan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing transaction
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jenis' => 'required|in:Pemasukan,Pengeluaran',
                'kategori' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'required|string'
            ]);

            $this->transactionService->update($id, auth()->id(), $validated);

            return redirect()->back()->with('success', 'Transaksi berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete a transaction
     */
    public function destroy($id)
    {
        try {
            $this->transactionService->destroy($id, auth()->id());
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Parse voice text menjadi data transaksi
     */
    public function parseVoiceText(Request $request)
    {
        try {
            $text = $request->input('text');
            if (empty($text)) {
                return response()->json(['success' => false, 'message' => 'Teks kosong'], 400);
            }

            // The existing NLPParserService returns a different shape and expects different rules, 
            // but for safety during refactor we map its parsed data to what the frontend expects.
            // Note: I will map the result to match the old shape.
            $parsedData = $this->nlpParserService->parse($text);

            // To ensure 100% compatibility with the old frontend logic, we merge smart logic if needed.
            if ($parsedData['success']) {
                $data = [
                    'jenis' => $parsedData['jenis'],
                    'kategori' => $parsedData['kategori'],
                    'jumlah' => $parsedData['jumlah'],
                    'keterangan' => $parsedData['keterangan'],
                    'budget_id' => null,
                    'goal_id' => null,
                    'budget_name' => $parsedData['budget_allocation'],
                    'goal_name' => $parsedData['goal_allocation']
                ];

                // Attempt to resolve IDs from names (the old logic did this inside the controller)
                if ($data['budget_name']) {
                    $budget = DB::table('budget')
                        ->where('user_id', auth()->id())
                        ->where('namaBudget', 'LIKE', '%' . $data['budget_name'] . '%')
                        ->first();
                    if ($budget) $data['budget_id'] = $budget->id;
                }

                if ($data['goal_name']) {
                    $goal = DB::table('goals')
                        ->where('user_id', auth()->id())
                        ->where('namaGoal', 'LIKE', '%' . $data['goal_name'] . '%')
                        ->first();
                    if ($goal) $data['goal_id'] = $goal->id;
                }

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'raw_text' => $text
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $parsedData['error'] ?? 'Gagal mem-parsing teks'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error parsing voice text: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat parsing text',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBudgets()
    {
        try {
            $budgets = DB::table('budget')
                ->where('user_id', auth()->id())
                ->select('id', 'namaBudget', 'kategori', 'jumlah', 'jumlahBerjalan')
                ->get();
            return response()->json(['success' => true, 'data' => $budgets]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data budget'], 500);
        }
    }

    public function getGoals()
    {
        try {
            $goals = DB::table('goals')
                ->where('user_id', auth()->id())
                ->select('id', 'namaGoal', 'targetNominal', 'nominalBerjalan', 'tanggalTarget')
                ->get();
            return response()->json(['success' => true, 'data' => $goals]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data goals'], 500);
        }
    }
}
