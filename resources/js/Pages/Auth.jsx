import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { MicrophoneIcon as Mic, PhoneIcon as Phone, LockClosedIcon as Lock, UserIcon, ArrowRightIcon as ArrowRight, XMarkIcon as X, CheckCircleIcon as CheckCircle2, ExclamationCircleIcon as AlertCircle, SpeakerWaveIcon as Volume2 } from '@heroicons/react/24/solid';
import VoiceVisualizer from '../Components/VoiceVisualizer';
import { useAudioRecorder } from '../Hooks/useAudioRecorder';

export default function Auth({ mode = 'login', status: propStatus }) {
    const { props } = usePage();
    const { flash, status: sessionStatus } = props;
    const status = propStatus || sessionStatus;
    
    const [authMode, setAuthMode] = useState(mode);
    const [isVoiceLogin, setIsVoiceLogin] = useState(false);
    const [voiceStatus, setVoiceStatus] = useState('idle'); // idle, recording, processing, success, error
    
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        phone: '',
        password: '',
        password_confirmation: '',
        voice_audio_base64: '',
    });

    const handleVoiceStop = (blob) => {
        const reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = () => {
            setData('voice_audio_base64', reader.result);
            setVoiceStatus('ready');
        };
    };

    const { isRecording, audioUrl, analyserRef, startRecording, stopRecording, clearAudio } = useAudioRecorder({ onStop: handleVoiceStop });

    const toggleMode = () => {
        const newMode = authMode === 'login' ? 'register' : 'login';
        setAuthMode(newMode);
        setIsVoiceLogin(false);
        setVoiceStatus('idle');
        clearAudio();
        reset();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (isVoiceLogin && authMode === 'login') {
            post('/voice-login');
        } else {
            post(authMode === 'login' ? '/login' : '/register');
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 flex items-center justify-center p-6 font-inter">
            <Head title={authMode === 'login' ? 'Login' : 'Daftar Akun'} />

            <div className="w-full max-w-[1000px] bg-white rounded-[40px] shadow-2xl shadow-slate-200 overflow-hidden flex flex-col md:flex-row">
                {/* Left Side: Branding & Biometric UI */}
                <div className="md:w-1/2 bg-teal-900 p-12 text-white relative flex flex-col justify-between overflow-hidden">
                    <div className="relative z-10">
                        <Link href="/" className="mb-14 block">
                            <img src="/images/voica-logo.png" alt="Voica" className="h-24 w-auto brightness-0 invert" />
                        </Link>

                        <h2 className="text-4xl lg:text-5xl font-bold font-outfit leading-tight mb-6">
                            {authMode === 'login' 
                                ? (isVoiceLogin ? 'Buka dengan Suara Anda.' : 'Selamat Datang Kembali.') 
                                : 'Pendaftaran Biometrik Suara.'}
                        </h2>
                        <p className="text-teal-100/60 text-lg leading-relaxed">
                            Teknologi enkripsi suara unik untuk keamanan finansial tingkat tinggi.
                        </p>
                    </div>

                    {/* Biometric Visualizer Box */}
                    <div className="relative z-10 mt-8 bg-white/5 border border-white/10 p-8 rounded-[32px] backdrop-blur-md">
                        <div className="flex items-center justify-between mb-6">
                            <div className="flex items-center gap-2">
                                <div className={`w-2 h-2 rounded-full ${isRecording ? 'bg-rose-500 animate-pulse' : 'bg-teal-500'}`}></div>
                                <span className="text-xs font-bold uppercase tracking-widest text-teal-400">
                                    {isRecording ? 'Merekam frekuensi...' : 'Sistem Siap'}
                                </span>
                            </div>
                            {data.voice_audio_base64 && <CheckCircle2 className="w-[18px] h-[18px] text-teal-400" />}
                        </div>
                        
                        <div className="h-24 flex items-center justify-center bg-black/20 rounded-2xl overflow-hidden mb-6">
                            <VoiceVisualizer isActive={isRecording} color={isRecording ? "#f43f5e" : "#2dd4bf"} analyserRef={analyserRef} />
                        </div>

                        <div className="flex items-center justify-center gap-3">
                            {!isRecording ? (
                                <button 
                                    onClick={startRecording}
                                    type="button"
                                    className="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-full text-sm font-bold transition-all border border-white/10 group"
                                >
                                    <span className="flex items-center gap-2">
                                        <Mic className="w-4 h-4 text-teal-400 group-hover:scale-110 transition-transform" />
                                        {data.voice_audio_base64 ? 'Rekam Ulang' : 'Mulai Perekaman'}
                                    </span>
                                </button>
                            ) : (
                                <button 
                                    onClick={stopRecording}
                                    type="button"
                                    className="px-6 py-3 bg-rose-500 hover:bg-rose-600 rounded-full text-sm font-bold transition-all shadow-lg shadow-rose-500/20"
                                >
                                    Berhenti & Simpan
                                </button>
                            )}
                            {audioUrl && !isRecording && (
                                <button
                                    onClick={() => new Audio(audioUrl).play()}
                                    type="button"
                                    className="p-3 bg-teal-500/20 hover:bg-teal-500/30 rounded-full transition-all border border-teal-400/20"
                                    title="Dengarkan rekaman"
                                >
                                    <Volume2 className="w-4 h-4 text-teal-400" />
                                </button>
                            )}
                        </div>
                        
                        <p className="mt-4 text-[11px] text-center text-teal-100/40 uppercase tracking-tighter font-bold">
                            Ucapkan: "Voica buka kunci dompet saya hari ini"
                        </p>
                    </div>

                    {/* Decorative Background */}
                    <div className="absolute -bottom-20 -left-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
                </div>

                {/* Right Side: Form */}
                <div className="md:w-1/2 p-8 md:p-16 flex flex-col justify-center bg-white">
                    <motion.div
                        key={authMode + (isVoiceLogin ? 'voice' : 'pass')}
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.4 }}
                    >
                        <div className="mb-10">
                            <h3 className="text-3xl font-bold font-outfit mb-2 text-slate-900">
                                {authMode === 'login' ? 'Masuk ke Akun' : 'Daftar Baru'}
                            </h3>
                            <p className="text-slate-400 font-medium text-sm">
                                {authMode === 'login' 
                                    ? 'Pilih metode masuk yang anda inginkan.' 
                                    : 'Isi data diri untuk memulai verifikasi suara.'}
                            </p>
                        </div>

                        {/* Flash Messages */}
                        <AnimatePresence>
                            {(status || flash?.success || flash?.error || errors.error) && (
                                <motion.div
                                    initial={{ opacity: 0, height: 0, marginBottom: 0 }}
                                    animate={{ opacity: 1, height: 'auto', marginBottom: 24 }}
                                    exit={{ opacity: 0, height: 0, marginBottom: 0 }}
                                    className={`p-4 rounded-2xl flex items-start gap-3 border ${
                                        (flash?.error || errors.error) ? 'bg-rose-50 border-rose-100 text-rose-700' : 'bg-teal-50 border-teal-100 text-teal-700'
                                    }`}
                                >
                                    {(flash?.error || errors.error) ? <AlertCircle className="w-[18px] h-[18px] shrink-0" /> : <CheckCircle2 className="w-[18px] h-[18px] shrink-0" />}
                                    <span className="text-sm font-bold leading-tight">
                                        {status || flash?.success || flash?.error || errors.error}
                                    </span>
                                </motion.div>
                            )}
                        </AnimatePresence>

                        {/* Mode Toggle (Only for Login) */}
                        {authMode === 'login' && (
                            <div className="flex p-1 bg-slate-50 rounded-2xl mb-8 border border-slate-100">
                                <button 
                                    onClick={() => setIsVoiceLogin(false)}
                                    className={`flex-1 py-3 rounded-xl text-sm font-bold transition-all ${!isVoiceLogin ? 'bg-white shadow-sm text-teal-600' : 'text-slate-400 hover:text-slate-600'}`}
                                >
                                    Kata Sandi
                                </button>
                                <button 
                                    onClick={() => setIsVoiceLogin(true)}
                                    className={`flex-1 py-3 rounded-xl text-sm font-bold transition-all ${isVoiceLogin ? 'bg-white shadow-sm text-teal-600' : 'text-slate-400 hover:text-slate-600'}`}
                                >
                                    Suara (Biometrik)
                                </button>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-5">
                            {authMode === 'register' && (
                                <div className="space-y-2">
                                    <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Nama Lengkap</label>
                                    <div className="relative">
                                        <UserIcon className="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                        <input 
                                            type="text"
                                            value={data.name}
                                            onChange={e => setData('name', e.target.value)}
                                            className="w-full h-12 pl-12 pr-4 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white outline-none transition-all font-semibold"
                                            placeholder="Nama anda"
                                            disabled={processing}
                                        />
                                    </div>
                                    {errors.name && <p className="text-rose-500 text-[10px] font-bold mt-1 ml-1">{errors.name}</p>}
                                </div>
                            )}

                            <div className="space-y-2">
                                <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Nomor Telepon</label>
                                <div className="relative">
                                    <Phone className="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input 
                                        type="tel"
                                        value={data.phone}
                                        onChange={e => setData('phone', e.target.value)}
                                        className="w-full h-12 pl-12 pr-4 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white outline-none transition-all font-semibold"
                                        placeholder="0812..."
                                        disabled={processing}
                                    />
                                </div>
                                {errors.phone && <p className="text-rose-500 text-[10px] font-bold mt-1 ml-1">{errors.phone}</p>}
                            </div>

                            {!isVoiceLogin && (
                                <div className="space-y-2">
                                    <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Kata Sandi</label>
                                    <div className="relative">
                                        <Lock className="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                        <input 
                                            type="password"
                                            value={data.password}
                                            onChange={e => setData('password', e.target.value)}
                                            className="w-full h-12 pl-12 pr-4 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white outline-none transition-all font-semibold"
                                            placeholder="••••••••"
                                            disabled={processing}
                                        />
                                    </div>
                                    {errors.password && <p className="text-rose-500 text-[10px] font-bold mt-1 ml-1">{errors.password}</p>}
                                </div>
                            )}

                            {authMode === 'register' && (
                                <div className="space-y-2">
                                    <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Konfirmasi Kata Sandi</label>
                                    <div className="relative">
                                        <Lock className="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                        <input 
                                            type="password"
                                            value={data.password_confirmation}
                                            onChange={e => setData('password_confirmation', e.target.value)}
                                            className="w-full h-12 pl-12 pr-4 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white outline-none transition-all font-semibold"
                                            placeholder="••••••••"
                                            disabled={processing}
                                        />
                                    </div>
                                </div>
                            )}

                            {/* Voice Enrollment Warning for Register or Voice Login Button Requirement */}
                            {(authMode === 'register' || isVoiceLogin) && (
                                <div className={`p-4 rounded-2xl text-xs font-medium border ${data.voice_audio_base64 ? 'bg-teal-50 border-teal-100 text-teal-700' : (errors.voice_audio ? 'bg-rose-50 border-rose-100 text-rose-700' : 'bg-slate-50 border-slate-200 text-slate-500')}`}>
                                    <p className="flex items-center gap-2">
                                        {data.voice_audio_base64 
                                            ? <CheckCircle2 className="w-3.5 h-3.5" /> 
                                            : (errors.voice_audio ? <AlertCircle className="w-3.5 h-3.5" /> : <Mic className="w-3.5 h-3.5" />)}
                                        {errors.voice_audio 
                                            ? errors.voice_audio 
                                            : (data.voice_audio_base64 
                                                ? 'Sampel suara terekam dengan sukses.' 
                                                : (authMode === 'register' ? 'Wajib merekam suara untuk pendaftaran.' : 'Silakan rekam suara untuk masuk.'))}
                                    </p>
                                </div>
                            )}

                            <button
                                type="submit"
                                disabled={processing || (isVoiceLogin && !data.voice_audio_base64) || (authMode === 'register' && !data.voice_audio_base64)}
                                className="w-full h-14 !mt-10 bg-teal-600 text-white rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-teal-700 transition-all shadow-xl shadow-teal-600/20 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Memproses...' : (authMode === 'login' ? 'Masuk Sekarang' : 'Daftar Akun')}
                                <ArrowRight className="w-5 h-5" />
                            </button>
                        </form>

                        <div className="mt-8 text-center">
                            <button onClick={toggleMode} className="text-sm font-bold text-slate-400 hover:text-teal-600 transition-colors">
                                {authMode === 'login' 
                                    ? "Belum punya akun? Daftar gratis" 
                                    : "Sudah punya akun? Login di sini"}
                            </button>
                        </div>
                    </motion.div>
                </div>
            </div>
        </div>
    );
}
