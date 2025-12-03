<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountSettingsController extends Controller
{
    /**
     * Show account settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('settings', compact('user'));
    }

    /**
     * Update account settings
     */
    public function update(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'password' => 'nullable|min:8|confirmed',
            ]);

            $user = Auth::user();

            // Update nama dan telepon
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'updated_at' => now()
                ]);

            // Update password jika diisi
            if (!empty($validated['password'])) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'password' => Hash::make($validated['password']),
                        'updated_at' => now()
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan akun berhasil diperbarui'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating account settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui pengaturan'
            ], 500);
        }
    }
}
