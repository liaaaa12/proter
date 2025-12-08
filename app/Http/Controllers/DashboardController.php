<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show dashboard with real data
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get total pemasukan
        $totalPemasukan = DB::table('transaction')
            ->where('user_id', $userId)
            ->where('jenis', 'Pemasukan')
            ->sum('jumlah');

        // Get total pengeluaran
        $totalPengeluaran = DB::table('transaction')
            ->where('user_id', $userId)
            ->where('jenis', 'Pengeluaran')
            ->sum('jumlah');

        // Calculate saldo
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Get all goals and calculate their progress
        $allGoals = DB::table('goals')
            ->where('user_id', $userId)
            ->get();

        $goal = null;
        $goalPercentage = 0;

        if ($allGoals->isNotEmpty()) {
            // Calculate nominalBerjalan from transactions for each goal
            $goalsWithProgress = $allGoals->map(function($g) use ($userId) {
                // Sum all transactions allocated to this goal
                $nominalBerjalan = DB::table('transaction')
                    ->where('user_id', $userId)
                    ->where('goal_id', $g->id)
                    ->sum('jumlah');
                
                $g->nominalBerjalan = $nominalBerjalan;
                $g->percentage = $g->targetNominal > 0 ? ($nominalBerjalan / $g->targetNominal) * 100 : 0;
                
                return $g;
            });

            // Get goal with highest percentage (closest to completion)
            $goal = $goalsWithProgress->sortByDesc('percentage')->first();
            $goalPercentage = $goal ? $goal->percentage : 0;
        }

        // Get recent transactions (5 latest)
        $recentTransactions = DB::table('transaction')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get all budgets for dropdown
        $allBudgets = DB::table('budget')
            ->where('user_id', $userId)
            ->get();

        // Get all goals for dropdown
        $goals = DB::table('goals')
            ->where('user_id', $userId)
            ->get();

        return view('dashboard', compact(
            'saldo',
            'totalPemasukan',
            'totalPengeluaran',
            'goal',
            'goalPercentage',
            'recentTransactions',
            'allBudgets',
            'goals'
        ));
    }
}
