import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { 
    Mic, 
    CheckCircle2, 
    ArrowRight, 
    ShieldCheck, 
    Smile,
    UserCircle2
} from 'lucide-react';

export default function Welcome({ auth }) {
    const steps = [
        {
            icon: Mic,
            title: "1. Bicara",
            desc: "Tekan tombol mic dan sebutkan belanjaan Anda. Misal: 'Beli beras 15 ribu dari uang belanja'."
        },
        {
            icon: CheckCircle2,
            title: "2. Catat",
            desc: "Aplikasi akan otomatis mencatat harga dan kategorinya tanpa Anda perlu mengetik."
        },
        {
            icon: Smile,
            title: "3. Selesai",
            desc: "Keuangan Anda kini tercatat dengan rapi dan aman. Sangat mudah, bukan?"
        }
    ];

    return (
        <div className="min-h-screen bg-slate-50 font-sans selection:bg-teal-100 selection:text-teal-900">
            <Head title="Voica - Catat Keuangan Pakai Suara" />

            {/* Navigasi Sederhana */}
            <nav className="fixed top-0 w-full z-50 bg-white border-b border-slate-200">
                <div className="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                    <Link href="/" className="flex items-center">
                        <img src="/images/voica-logo.png" alt="Voica" className="w-36 h-auto" />
                    </Link>

                    <div className="flex items-center gap-6">
                        {auth.user ? (
                            <Link href="/dashboard" className="text-lg font-bold text-teal-700 hover:underline">
                                Buka Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link href="/login" className="text-lg font-bold text-slate-600 hover:text-slate-900">
                                    Masuk
                                </Link>
                                <Link href="/register" className="px-8 py-3 bg-teal-600 text-white rounded-xl text-lg font-black shadow-lg hover:bg-teal-700 transition-all">
                                    Daftar Sini
                                </Link>
                            </>
                        )}
                    </div>
                </div>
            </nav>

            {/* Bagian Utama (Hero) */}
            <header className="pt-32 pb-20 px-6">
                <div className="max-w-4xl mx-auto text-center">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.8 }}
                    >
                        <h1 className="text-5xl md:text-7xl font-black text-slate-900 leading-tight mb-8">
                            Cara <span className="text-teal-600 text-6xl md:text-8xl">Paling Gampang</span> <br /> 
                            Catat Uang Keluar
                        </h1>
                        <p className="text-2xl md:text-3xl text-slate-500 mb-12 leading-relaxed">
                            Cukup bicara, aplikasi langsung bantu catat belanjaan Anda. <br className="hidden md:block" />
                            Gak perlu pusing ngetik angka kecil-kecil lagi.
                        </p>

                        <Link href="/register" className="inline-flex items-center gap-4 px-12 py-6 bg-slate-900 text-white rounded-3xl text-2xl font-black shadow-2xl hover:scale-105 transition-all group">
                            Mulai Sekarang - Gratis!
                            <ArrowRight size={32} className="group-hover:translate-x-2 transition-transform" />
                        </Link>
                    </motion.div>

                    {/* Ilustrasi Mic Besar */}
                    <motion.div 
                        initial={{ opacity: 0, scale: 0.5 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ delay: 0.3, type: "spring" }}
                        className="mt-20 w-32 h-32 md:w-48 md:h-48 bg-teal-500 rounded-full mx-auto flex items-center justify-center text-white shadow-2xl shadow-teal-500/20"
                    >
                        <Mic size={64} className="md:size-96" />
                    </motion.div>
                </div>
            </header>

            {/* Langkah-langkah */}
            <section className="py-20 bg-white border-y border-slate-100">
                <div className="max-w-6xl mx-auto px-6">
                    <h2 className="text-4xl font-black text-center mb-16 underline decoration-teal-500 decoration-8 underline-offset-8">Tiga Langkah Mudah</h2>
                    
                    <div className="grid md:grid-cols-3 gap-12">
                        {steps.map((step, i) => (
                            <motion.div 
                                key={i}
                                initial={{ opacity: 0, x: -20 }}
                                whileInView={{ opacity: 1, x: 0 }}
                                transition={{ delay: i * 0.2 }}
                                className="p-8 rounded-[40px] bg-slate-50 border border-slate-100"
                            >
                                <div className="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center text-teal-600 mb-6">
                                    <step.icon size={32} />
                                </div>
                                <h3 className="text-2xl font-black mb-4">{step.title}</h3>
                                <p className="text-xl text-slate-500 leading-relaxed font-medium">
                                    {step.desc}
                                </p>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Keamanan */}
            <section className="py-20 px-6">
                <div className="max-w-4xl mx-auto text-center">
                    <div className="inline-flex items-center gap-3 px-6 py-2 bg-teal-50 text-teal-700 rounded-full mb-8 font-bold">
                        <ShieldCheck size={20} />
                        Keamanan Terjamin
                    </div>
                    <h2 className="text-4xl font-black mb-6">Aman & Terpercaya</h2>
                    <p className="text-2xl text-slate-500 leading-relaxed max-w-2xl mx-auto">
                        Voica menggunakan pengenal suara yang sangat canggih. Data Anda aman dan tidak akan dibagikan ke siapapun.
                    </p>
                </div>
            </section>

            {/* Footer */}
            <footer className="py-16 bg-slate-900 text-white text-center">
                <div className="max-w-4xl mx-auto px-6">
                    <img src="/images/voica-logo.png" alt="Voica" className="h-10 w-auto mx-auto mb-8 brightness-0 invert opacity-50" />
                    <p className="text-lg opacity-60 mb-8 font-medium">
                        Dibuat khusus untuk memudahkan pengelolaan keuangan keluarga Anda.
                    </p>
                    <div className="flex justify-center gap-12 text-sm font-bold tracking-widest uppercase opacity-40">
                        <span>Tentang Kami</span>
                        <span>Bantuan</span>
                        <span>Privasi</span>
                    </div>
                    <div className="mt-12 pt-8 border-t border-white/5 opacity-40 font-bold">
                        © 2026 Voica Finance.
                    </div>
                </div>
            </footer>
        </div>
    );
}
