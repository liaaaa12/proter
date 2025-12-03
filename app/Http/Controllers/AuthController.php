<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\VoiceAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $voiceAuthService;

    public function __construct(VoiceAuthService $voiceAuthService)
    {
        $this->voiceAuthService = $voiceAuthService;
    }

    /**
     * Show login/register form
     */
    public function showAuthForm($mode = 'login')
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth', ['mode' => $mode]);
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), [
    'name' => ['required', 'string', 'max:255'],
    'phone' => ['required', 'string', 'max:20', 'unique:users'],
    'password' => ['required', 'confirmed', Password::min(8)],
    // voice optional total
    'voice_audio_file' => ['nullable', 'file', 'mimes:wav,webm,ogg,mp3', 'max:10240'],
    'voice_audio_base64' => ['nullable', 'string'],
]);

        // // Custom validation untuk voice audio (terima file ATAU base64)
        // $validator = Validator::make($request->all(), [
        //     'name' => ['required', 'string', 'max:255'],
        //     'phone' => ['required', 'string', 'max:20', 'unique:users'],
        //     'password' => ['required', 'confirmed', Password::min(8)],
        //     'voice_audio_file' => ['nullable', 'file', 'mimes:wav,webm,ogg,mp3', 'max:10240'],
        //     'voice_audio_base64' => ['nullable', 'string'],
        // ]);

        // // Validasi tambahan: minimal salah satu harus ada
        // $validator->after(function ($validator) use ($request) {
        //     if (!$request->hasFile('voice_audio_file') && !$request->filled('voice_audio_base64')) {
        //         $validator->errors()->add('voice_audio', 'Rekaman suara diperlukan untuk pendaftaran');
        //     }
        // });

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('mode', 'register');
        }

        try {
            // Proses audio: prioritaskan file upload, fallback ke base64
            // $audioFile = $this->processAudioInput($request);
            
            // if (!$audioFile) {
            //     return back()
            //         ->withErrors(['voice_audio' => 'Gagal memproses file audio'])
            //         ->withInput()
            //         ->with('mode', 'register');
            // }

            // // Process voice enrollment
            // $voiceResult = $this->processVoiceEnrollment($audioFile);
            
            // // Cleanup temporary file jika dari base64
            // if ($audioFile->getClientOriginalName() === 'recorded_voice') {
            //     $this->voiceAuthService->cleanup($audioFile->getRealPath());
            // }
            
            // if (!$voiceResult['success']) {
            //     return back()
            //         ->withErrors(['voice_audio' => $voiceResult['error']])
            //         ->withInput()
            //         ->with('mode', 'register');
            // }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'voice_path' => null,
                //'voice_path' => $voiceResult['voice_path'],
                'voice_embedding' => null,
                //'voice_embedding' => json_encode($voiceResult['features']),
                'voice_enrolled_at' => null,
                //'voice_enrolled_at' => now(),
            ]);

            // Log the user in
            Auth::login($user);

            return redirect()->route('dashboard')->with('status', 'Registrasi berhasil! Suara Anda telah terdaftar.');
        } catch (\Exception $e) {
            // Clean up voice file if exists
            if (isset($voiceResult) && isset($voiceResult['voice_path'])) {
                Storage::delete($voiceResult['voice_path']);
            }

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage()])
                ->withInput()
                ->with('mode', 'register');
        }
    }

    /**
     * Handle traditional login (password-based)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('mode', 'login');
        }

        $loginField = $request->input('phone');

        // $user = User::where('phone', $loginField)
        //         ->orWhere('name', $loginField)
        //         ->first();

        // $fieldType = $user->phone === $loginField ? 'phone' : 'name';
        $fieldType = is_numeric($loginField) ? 'phone' : 'name';
        
        $credentials = [
            $fieldType => $loginField,
            'password' => $request->password,
        ];
        
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        return back()
            ->withErrors(['phone' => 'Nama / nomor telepon atau kata sandi salah'])
            ->withInput()
            ->with('mode', 'login');
    }

        // if (Auth::attempt($credentials, $request->boolean('remember'))) {
        //     $request->session()->regenerate();
        //     return redirect()->intended('dashboard');
        // }

         $request->session()->regenerate();

    return redirect()->intended('dashboard');
        // return back()
        //     ->withErrors(['phone' => 'Kredensial tidak valid'])
        //     ->withInput()
        //     ->with('mode', 'login');
    }

    /**
     * Handle voice-based login
     */
    public function voiceLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
            'voice_audio_file' => ['nullable', 'file', 'mimes:wav,webm,ogg,mp3', 'max:10240'],
            'voice_audio_base64' => ['nullable', 'string'],
        ]);

        // Validasi tambahan
        $validator->after(function ($validator) use ($request) {
            if (!$request->hasFile('voice_audio_file') && !$request->filled('voice_audio_base64')) {
                $validator->errors()->add('voice_audio', 'Rekaman suara diperlukan');
            }
        });

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('mode', 'login');
        }

        try {
            // Find user by phone
           $loginField = $request->input('phone');

            $user = User::where('phone', $loginField)
            ->orWhere('name', $loginField)
            ->first();

            if (!$user) {
                return back()
                    ->withErrors(['phone' => 'Nomor telepon tidak terdaftar'])
                    ->with('mode', 'login');
            }

            if (!$user->hasVoiceEnrolled()) {
                return back()
                    ->withErrors(['voice_audio' => 'Pengguna belum mendaftarkan suara'])
                    ->with('mode', 'login');
            }

            // Proses audio input
            $audioFile = $this->processAudioInput($request);
            
            if (!$audioFile) {
                return back()
                    ->withErrors(['voice_audio' => 'Gagal memproses file audio'])
                    ->with('mode', 'login');
            }

            // Verify voice
            $verificationResult = $this->verifyVoice(
                $audioFile,
                json_decode($user->voice_embedding, true)
            );

            // Cleanup temporary file jika dari base64
            if ($audioFile->getClientOriginalName() === 'recorded_voice') {
                $this->voiceAuthService->cleanup($audioFile->getRealPath());
            }

            if (!$verificationResult['success']) {
                return back()
                    ->withErrors(['voice_audio' => 'Gagal memverifikasi suara: ' . $verificationResult['error']])
                    ->with('mode', 'login');
            }

            if (!$verificationResult['is_match']) {
                return back()
                    ->withErrors([
                        'voice_audio' => 'Suara tidak cocok. Kemiripan: ' . $verificationResult['similarity'] . '%'
                    ])
                    ->with('mode', 'login');
            }

            // Login successful
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()
                ->route('dashboard')
                ->with('status', 'Login berhasil dengan voice recognition! Kemiripan: ' . $verificationResult['similarity'] . '%');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->with('mode', 'login');
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Process audio input - prioritas file upload, fallback ke base64
     */
    private function processAudioInput(Request $request)
    {
        // Prioritaskan file upload
        if ($request->hasFile('voice_audio_file')) {
            return $request->file('voice_audio_file');
        }

        // Fallback ke base64
        if ($request->filled('voice_audio_base64')) {
            try {
                $base64Data = $request->input('voice_audio_base64');
                return $this->voiceAuthService->base64ToFile($base64Data);
            } catch (\Exception $e) {
                Log::error('Failed to convert base64 to file: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    /**
     * Process voice enrollment (extract features)
     */
    private function processVoiceEnrollment($audioFile)
    {
        try {
            // Ekstrak ekstensi dari file
            $extension = $audioFile->getClientOriginalExtension() ?: 'wav';
            
            // Generate unique filename
            $filename = 'voice_' . uniqid() . '_' . time() . '.' . $extension;
            $voicePath = 'voices/' . $filename;
            
            // Save audio file ke storage/app/public/voices
            $savedPath = Storage::disk('public')->putFileAs('voices', $audioFile, $filename);
            
            if (!$savedPath) {
                throw new \Exception('Gagal menyimpan file audio');
            }

            // Get full path untuk Python script
            $fullPath = Storage::disk('public')->path($savedPath);
            
            // Konversi ke WAV jika perlu
            if (strtolower($extension) !== 'wav') {
                Log::info('🔄 Starting FFmpeg conversion...');
                
                try {
                    $convertedPath = $this->voiceAuthService->convertToWav($fullPath);
                    
                    Log::info('Conversion result path: ' . $convertedPath);
                    
                    // Cek apakah konversi benar-benar berhasil
                    if (!file_exists($convertedPath)) {
                        throw new \Exception('File WAV tidak ditemukan setelah konversi');
                    }
                    
                    if ($convertedPath === $fullPath) {
                        throw new \Exception('Konversi tidak menghasilkan file baru');
                    }
                    
                    $wavSize = filesize($convertedPath);
                    Log::info('✅ WAV created! Size: ' . $wavSize . ' bytes');
                    
                    if ($wavSize < 1000) {
                        throw new \Exception('File WAV terlalu kecil: ' . $wavSize . ' bytes');
                    }
                    
                    // Update path
                    $fullPath = $convertedPath;
                    $savedPath = 'voices/' . basename($convertedPath);
            }  catch (\Exception $e) {
                Log::error('❌ Konversi gagal: ' . $e->getMessage());
                throw new \Exception('Gagal konversi audio ke WAV: ' . $e->getMessage());
            }
        }
            
            // Call Python script to extract features
            $pythonPath = env('PYTHON_PATH', 'python3');
            $scriptPath = base_path('scripts/voice_processor.py');
            
            $command = escapeshellcmd("$pythonPath $scriptPath enroll " . escapeshellarg($fullPath));
            $output = shell_exec($command . ' 2>&1');
            
            if (!$output) {
                throw new \Exception('Python script tidak memberikan output');
            }
            
            $result = json_decode($output, true);
            
            if (!$result || !isset($result['success']) || !$result['success']) {
                throw new \Exception($result['error'] ?? 'Gagal mengekstrak fitur suara');
            }

            return [
                'success' => true,
                'voice_path' => $savedPath,
                'features' => $result['features'],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify voice against enrolled features
     */
    private function verifyVoice($audioFile, $enrolledFeatures)
    {
        try {
            // Ekstrak ekstensi
            $extension = $audioFile->getClientOriginalExtension() ?: 'wav';
            
            // Save temporary audio file
            $tempFilename = 'temp_' . uniqid() . '.' . $extension;
            $tempPath = Storage::disk('public')->putFileAs('temp', $audioFile, $tempFilename);
            
            if (!$tempPath) {
                throw new \Exception('Gagal menyimpan file audio sementara');
            }

            $fullPath = Storage::disk('public')->path($tempPath);
            
            // Konversi ke WAV jika perlu
            if (!in_array($extension, ['wav'])) {
                $fullPath = $this->voiceAuthService->convertToWav($fullPath);
            }
            
            // Call Python script to verify
            $pythonPath = env('PYTHON_PATH', 'python3');
            $scriptPath = base_path('scripts/voice_processor.py');
            
            $featuresJson = json_encode($enrolledFeatures);
            $command = escapeshellcmd("$pythonPath $scriptPath verify " . escapeshellarg($fullPath) . " " . escapeshellarg($featuresJson));
            $output = shell_exec($command . ' 2>&1');
            
            // Clean up temp file
            Storage::disk('public')->delete($tempPath);
            
            if (!$output) {
                throw new \Exception('Python script tidak memberikan output');
            }
            
            $result = json_decode($output, true);
            
            if (!$result || !isset($result['success'])) {
                throw new \Exception($result['error'] ?? 'Gagal memverifikasi suara');
            }

            return $result;

        } catch (\Exception $e) {
            // Clean up temp file if exists
            if (isset($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}