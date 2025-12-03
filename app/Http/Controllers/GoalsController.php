<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;

class GoalsController extends Controller
{
    public function index(Request $request)
    {
        $goals = Goal::orderBy('created_at', 'desc')->get();

        // If request is AJAX, return only the inner content to swap into the page
        if ($request->ajax() || $request->wantsJson()) {
            return view('goals._content', compact('goals'));
        }

        // Full page (when navigated directly)
        return view('goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'namaGoal' => ['required', 'string', 'max:255'],
            'targetNominal' => ['required', 'numeric', 'min:1'],
            'nominalBerjalan' => ['nullable', 'numeric', 'min:0'],
            'tanggalTarget' => ['required', 'date'],
        ]);

        $data['nominalBerjalan'] = $data['nominalBerjalan'] ?? 0;

        // Check if nominalBerjalan >= targetNominal (goal already achieved)
        if ($data['nominalBerjalan'] >= $data['targetNominal']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Goal sudah tercapai, tidak perlu disimpan lagi.',
                    'status' => 'warning'
                ], 400);
            }
            return redirect()->route('goals')->with('error', 'Goal sudah tercapai, tidak perlu disimpan lagi.');
        }

        // Check if identical goal already exists (same name on same date)
        $existingGoal = Goal::where('namaGoal', $data['namaGoal'])
            ->whereDate('tanggalTarget', $data['tanggalTarget'])
            ->first();

        if ($existingGoal) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Goal dengan nama dan tanggal yang sama sudah ada. Silakan ubah atau gunakan nama berbeda.',
                    'status' => 'warning'
                ], 400);
            }
            return redirect()->route('goals')->with('error', 'Goal dengan nama dan tanggal yang sama sudah ada.');
        }

        $goal = Goal::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            // Return JSON with goal data for DOM update
            return response()->json([
                'success' => true,
                'goal' => $goal
            ]);
        }

        return redirect()->route('goals')->with('status', 'Goal berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);

        $data = $request->validate([
            'namaGoal' => ['required', 'string', 'max:255'],
            'targetNominal' => ['required', 'numeric'],
            'nominalBerjalan' => ['nullable', 'numeric'],
            'tanggalTarget' => ['required', 'date'],
        ]);

        $data['nominalBerjalan'] = $data['nominalBerjalan'] ?? 0;

        $goal->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            // Return JSON with updated goal data
            return response()->json([
                'success' => true,
                'goal' => $goal
            ]);
        }

        return redirect()->route('goals')->with('status', 'Goal berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);
        $goal->delete();

        if ($request->ajax() || $request->wantsJson()) {
            // Return JSON response for delete
            return response()->json([
                'success' => true,
                'message' => 'Goals berhasil dihapus'
            ]);
        }

        return redirect()->route('goals')->with('status', 'Goal berhasil dihapus');
    }
}
