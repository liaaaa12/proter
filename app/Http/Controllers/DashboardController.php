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

        // Get first goal (target)
        $goal = DB::table('goals')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Calculate goal percentage
        $goalPercentage = 0;
        if ($goal && $goal->targetNominal > 0) {
            $goalPercentage = ($goal->nominalBerjalan / $goal->targetNominal) * 100;
        }

        // Get recent transactions (5 latest)
        $recentTransactions = DB::table('transaction')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get all budgets for dropdown
        $budgets = DB::table('budget')
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
            'budgets',
            'goals'
        ));
    }
}
