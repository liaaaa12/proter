<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetController extends Controller
{
    /**
     * Mapping kategori ke icon
     */
    private function getCategoryIcon($kategori)
    {
        $icons = [
            'Makanan' => '🍔',
            'Transportasi' => '🚗',
            'Hiburan' => '🎬',
            'Belanja' => '🛍️',
            'Jalan-Jalan' => '✈️',
            'Kesehatan' => '🏥',
            'Pendidikan' => '📚',
            'Tagihan' => '💳',
            'Lainnya' => '💰',
        ];
        
        return $icons[$kategori] ?? '💰';
    }

    /**
     * Display budgeting page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $periode = $request->input('periode', date('Y-m')); // Default: bulan ini
        
        // Get budgets for selected period
        $budgets = DB::table('budget')
            ->where('user_id', $user->id)
            ->where('periode', $periode)
            ->get();
        
        // Calculate terpakai for each budget from transactions
        $budgetsWithProgress = $budgets->map(function($budget) use ($periode) {
            // Get total pengeluaran for this SPECIFIC budget (by budget_id)
            $startDate = Carbon::parse($periode . '-01')->startOfMonth();
            $endDate = Carbon::parse($periode . '-01')->endOfMonth();
            
            // Only count transactions that are specifically allocated to this budget
            $terpakai = DB::table('transaction')
                ->where('user_id', $budget->user_id)
                ->where('budget_id', $budget->id) // ← CHANGED: Use budget_id instead of kategori
                ->where('jenis', 'Pengeluaran')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('jumlah');
            
            $persentase = $budget->jumlah > 0 ? ($terpakai / $budget->jumlah) * 100 : 0;
            $sisa = $budget->jumlah - $terpakai;
            
            return [
                'id' => $budget->id,
                'namaBudget' => $budget->namaBudget,
                'kategori' => $budget->kategori,
                'icon' => $budget->icon,
                'jumlah' => $budget->jumlah,
                'jumlah_formatted' => 'Rp' . number_format($budget->jumlah, 0, ',', '.'),
                'terpakai' => $terpakai,
                'terpakai_formatted' => 'Rp' . number_format($terpakai, 0, ',', '.'),
                'sisa' => $sisa,
                'sisa_formatted' => 'Rp' . number_format($sisa, 0, ',', '.'),
                'persentase' => round($persentase, 1),
                'periode' => $budget->periode,
            ];
        });
        
        // Get all budgets for voice modal dropdown
        $allBudgets = DB::table('budget')
            ->where('user_id', $user->id)
            ->select('id', 'namaBudget', 'kategori')
            ->get();
        
        // Get all goals for voice modal dropdown
        $goals = DB::table('goals')
            ->where('user_id', $user->id)
            ->select('id', 'namaGoal')
            ->get();
        
        return view('anggaran', compact('budgetsWithProgress', 'periode', 'allBudgets', 'goals'));
    }

    /**
     * Store new budget
     */
    public function store(Request $request)
    {
        $request->validate([
            'namaBudget' => 'required|string|max:255',
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'periode' => 'required|date_format:Y-m',
        ]);
        
        $user = Auth::user();
        
        // Get icon based on category
        $icon = $this->getCategoryIcon($request->kategori);
        
        DB::table('budget')->insert([
            'user_id' => $user->id,
            'namaBudget' => $request->namaBudget,
            'kategori' => $request->kategori,
            'icon' => $icon,
            'jumlah' => $request->jumlah,
            'jumlahBerjalan' => 0,
            'periode' => $request->periode,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Budget berhasil ditambahkan!'
        ]);
    }

    /**
     * Update budget
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'namaBudget' => 'required|string|max:255',
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        
        // Check if budget exists and belongs to user
        $budget = DB::table('budget')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$budget) {
            return response()->json([
                'success' => false,
                'message' => 'Budget tidak ditemukan!'
            ], 404);
        }
        
        // Get icon based on category
        $icon = $this->getCategoryIcon($request->kategori);
        
        DB::table('budget')
            ->where('id', $id)
            ->update([
                'namaBudget' => $request->namaBudget,
                'kategori' => $request->kategori,
                'icon' => $icon,
                'jumlah' => $request->jumlah,
                'updated_at' => now(),
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Budget berhasil diupdate!'
        ]);
    }

    /**
     * Delete budget
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Check if budget exists and belongs to user
        $budget = DB::table('budget')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$budget) {
            return response()->json([
                'success' => false,
                'message' => 'Budget tidak ditemukan!'
            ], 404);
        }
        
        DB::table('budget')->where('id', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Budget berhasil dihapus!'
        ]);
    }
    /**
     * Get transactions for a specific budget
     */
    public function getTransactions($id)
    {
        $user = Auth::user();
        $budget = DB::table('budget')->where('id', $id)->where('user_id', $user->id)->first();

        if (!$budget) {
            return response()->json(['success' => false, 'message' => 'Budget not found'], 404);
        }

        $startDate = Carbon::parse($budget->periode . '-01')->startOfMonth();
        $endDate = Carbon::parse($budget->periode . '-01')->endOfMonth();

        $transactions = DB::table('transaction')
            ->where('user_id', $user->id)
            ->where('kategori', $budget->kategori)
            ->where('jenis', 'Pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'budget_name' => $budget->namaBudget
        ]);
    }
}
