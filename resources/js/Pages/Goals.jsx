import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AuthLayout from '../Layouts/AuthLayout';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    Target, 
    Plus, 
    Calendar, 
    TrendingUp, 
    ChevronRight, 
    Trophy,
    X,
    Save,
    MoreHorizontal,
    AlertCircle
} from 'lucide-react';

const GoalCard = ({ goal }) => {
    const percentage = Math.round((goal.nominalBerjalan / goal.targetNominal) * 100);
    const isAchieved = percentage >= 100;

    return (
        <motion.div 
            variants={{
                hidden: { opacity: 0, y: 20 },
                visible: { opacity: 1, y: 0 }
            }}
            whileHover={{ 
                scale: 1.02, 
                y: -5,
                boxShadow: "0 20px 40px rgba(0,0,0,0.05)"
            }}
            className="relative group bg-white/40 backdrop-blur-xl p-8 rounded-[48px] border border-white/40 shadow-sm flex flex-col justify-between h-full overflow-hidden"
        >
            {/* Inner Glow Decorative Element */}
            <div className={`absolute -right-4 -top-4 w-32 h-32 rounded-full blur-3xl opacity-0 group-hover:opacity-20 transition-opacity duration-500 ${isAchieved ? 'bg-teal-500' : 'bg-indigo-500'}`} />

            {isAchieved && (
                <div className="absolute top-6 right-6 z-20">
                    <motion.div 
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        className="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center text-white shadow-xl shadow-teal-500/40"
                    >
                        <Trophy size={24} />
                    </motion.div>
                </div>
            )}

            <div className="mb-10 relative z-10">
                <div className="w-16 h-16 bg-white shadow-inner rounded-[24px] flex items-center justify-center text-slate-400 group-hover:scale-110 group-hover:rotate-6 group-hover:text-teal-600 transition-all duration-500 mb-8">
                    <Target size={32} />
                </div>
                <h4 className="text-2xl font-bold text-slate-900 mb-2 tracking-tight">{goal.namaGoal}</h4>
                <div className="flex items-center gap-2 text-slate-400">
                    <Calendar size={14} />
                    <span className="text-[10px] font-black uppercase tracking-[0.2em]">Target: {new Date(goal.tanggalTarget).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}</span>
                </div>
            </div>

            <div className="space-y-8 relative z-10">
                <div>
                    <div className="flex items-end justify-between mb-4">
                        <span className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Progress Tabungan</span>
                        <span className={`text-lg font-bold font-outfit ${isAchieved ? 'text-teal-600' : 'text-slate-900'}`}>{percentage}%</span>
                    </div>
                    <div className="h-3 bg-slate-200/50 rounded-full overflow-hidden">
                        <motion.div 
                            initial={{ width: 0 }}
                            animate={{ width: `${Math.min(percentage, 100)}%` }}
                            transition={{ duration: 1.5, ease: "easeOut" }}
                            className={`h-full rounded-full ${isAchieved ? 'bg-teal-500 shadow-[0_0_15px_rgba(20,184,166,0.4)]' : 'bg-indigo-600 shadow-[0_0_15px_rgba(79,70,229,0.4)]'}`}
                        />
                    </div>
                </div>

                <div className="flex items-center justify-between pt-6 border-t border-white/40">
                    <div>
                        <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Terkumpul</p>
                        <p className="font-bold text-slate-900 text-lg">
                            <span className="text-xs font-normal text-slate-400 mr-1">Rp</span>
                            {new Intl.NumberFormat('id-ID').format(goal.nominalBerjalan)}
                        </p>
                    </div>
                    <div className="text-right">
                        <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Target</p>
                        <p className="font-bold text-slate-400 text-lg">
                            <span className="text-xs font-normal text-slate-400/60 mr-1">Rp</span>
                            {new Intl.NumberFormat('id-ID').format(goal.targetNominal)}
                        </p>
                    </div>
                </div>
            </div>
        </motion.div>
    );
};

export default function Goals({ goals, allBudgets }) {
    const [isFormOpen, setIsFormOpen] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        namaGoal: '',
        targetNominal: '',
        nominalBerjalan: 0,
        tanggalTarget: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/goals', {
            onSuccess: () => {
                setIsFormOpen(false);
                reset();
            },
        });
    };

    return (
        <AuthLayout>
            <Head title="Target Keuangan" />

            <div className="max-w-7xl mx-auto">
                {/* Header Area */}
                <motion.div 
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12"
                >
                    <div>
                        <h1 className="text-5xl font-bold font-outfit mb-3 tracking-tighter text-slate-900">Financial Goals</h1>
                        <p className="text-slate-500 font-medium">Wujudkan impian Anda dengan perencanaan masa depan yang matang.</p>
                    </div>

                    <motion.button 
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        onClick={() => setIsFormOpen(true)}
                        className="h-16 px-10 bg-teal-600 text-white rounded-2xl flex items-center justify-center gap-3 font-bold shadow-2xl shadow-teal-600/30 hover:bg-teal-700 transition-all group overflow-hidden relative"
                    >
                        <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite]" />
                        <Plus size={22} className="relative z-10" />
                        <span className="relative z-10">Buat Target Baru</span>
                    </motion.button>
                </motion.div>

                {/* Goals Summary Card (Hero Section) */}
                <motion.div 
                    initial={{ opacity: 0, scale: 0.98 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: 0.2 }}
                    className="bg-slate-900/90 backdrop-blur-3xl rounded-[48px] p-10 md:p-16 mb-12 text-white relative overflow-hidden border border-white/5 shadow-3xl"
                >
                    <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                        <div className="flex flex-col gap-4">
                            <span className="inline-block px-4 py-1.5 text-[10px] font-black tracking-widest uppercase bg-teal-500/20 text-teal-400 rounded-full border border-teal-500/20 w-fit">
                                Snapshot Progress
                            </span>
                            <h2 className="text-4xl md:text-5xl font-bold font-outfit leading-tight tracking-tight">Terus Melangkah, <br/>Wujudkan Masa Depan.</h2>
                            <p className="text-slate-400 max-w-md font-medium">Setiap nominal yang Anda tabung adalah langkah nyata menuju impian yang lebih besar.</p>
                        </div>
                        <div className="flex gap-16 text-center">
                            <div className="group">
                                <p className="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4 group-hover:text-white transition-colors">Aktif</p>
                                <p className="text-6xl font-bold font-outfit tracking-tighter transition-transform group-hover:scale-110">{goals.filter(g => (g.nominalBerjalan/g.targetNominal) < 1).length}</p>
                            </div>
                            <div className="w-px h-24 bg-gradient-to-b from-transparent via-white/10 to-transparent hidden md:block" />
                            <div className="group">
                                <p className="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4 group-hover:text-teal-400 transition-colors">Tercapai</p>
                                <p className="text-6xl font-bold font-outfit text-teal-400 tracking-tighter transition-transform group-hover:scale-110">{goals.filter(g => (g.nominalBerjalan/g.targetNominal) >= 1).length}</p>
                            </div>
                        </div>
                    </div>
                    {/* Animated Decorative Element */}
                    <motion.div 
                        animate={{ 
                            scale: [1, 1.2, 1],
                            opacity: [0.1, 0.2, 0.1]
                        }}
                        transition={{ duration: 8, repeat: Infinity }}
                        className="absolute -top-20 -right-20 w-96 h-96 bg-teal-500/20 rounded-full blur-[100px]" 
                    />
                </motion.div>

                {/* Goals Grid */}
                {goals.length > 0 ? (
                    <motion.div 
                        initial="hidden"
                        animate="visible"
                        variants={{
                            visible: {
                                transition: {
                                    staggerChildren: 0.1
                                }
                            }
                        }}
                        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                    >
                        {goals.map((goal) => (
                            <GoalCard key={goal.id} goal={goal} />
                        ))}
                    </motion.div>
                ) : (
                    <div className="bg-white rounded-[40px] p-24 text-center border-2 border-dashed border-slate-200">
                        <div className="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8">
                            <Target className="text-slate-200" size={48} />
                        </div>
                        <h3 className="text-2xl font-bold text-slate-900 mb-3">Mulai Target Pertama Anda</h3>
                        <p className="text-slate-500 max-w-sm mx-auto mb-10 leading-relaxed">Punya impian membeli rumah, kendaraan, atau sekadar tabungan darurat? Catat targetnya di sini.</p>
                        <button 
                            onClick={() => setIsFormOpen(true)}
                            className="px-10 py-5 bg-teal-600 text-white rounded-3xl font-bold shadow-xl shadow-teal-600/20 hover:bg-teal-700 transition-all"
                        >
                            Buat Target Sekarang
                        </button>
                    </div>
                )}
            </div>

            {/* Modal Form */}
            <AnimatePresence>
                {isFormOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-6">
                        <motion.div 
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setIsFormOpen(false)}
                            className="absolute inset-0 bg-slate-900/40 backdrop-blur-md"
                        />
                        <motion.div 
                            initial={{ opacity: 0, scale: 0.9, y: 20 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.9, y: 20 }}
                            className="relative w-full max-w-lg bg-white rounded-[40px] shadow-2xl overflow-hidden"
                        >
                            <div className="p-8 md:p-10 border-b border-slate-50 flex items-center justify-between">
                                <div>
                                    <h3 className="text-2xl font-bold font-outfit">Target Baru</h3>
                                    <p className="text-slate-400 font-medium text-sm">Rencanakan kesuksesan Anda hari ini.</p>
                                </div>
                                <button onClick={() => setIsFormOpen(false)} className="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                                    <X size={24} />
                                </button>
                            </div>

                            <form onSubmit={submit} className="p-8 md:p-10 space-y-6">
                                <div className="space-y-2">
                                    <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Nama Target</label>
                                    <input 
                                        type="text"
                                        value={data.namaGoal}
                                        onChange={e => setData('namaGoal', e.target.value)}
                                        className="w-full h-14 px-5 bg-slate-50 border-0 rounded-2xl focus:ring-2 focus:ring-teal-500 font-semibold transition-all"
                                        placeholder="Contoh: Beli Mobil Baru"
                                        required
                                    />
                                    {errors.namaGoal && <p className="text-rose-500 text-[10px] font-bold ml-1">{errors.namaGoal}</p>}
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Target Dana (Rp)</label>
                                        <input 
                                            type="number"
                                            value={data.targetNominal}
                                            onChange={e => setData('targetNominal', e.target.value)}
                                            className="w-full h-14 px-5 bg-slate-50 border-0 rounded-2xl focus:ring-2 focus:ring-teal-500 font-bold"
                                            placeholder="0"
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Batas Waktu</label>
                                        <input 
                                            type="date"
                                            value={data.tanggalTarget}
                                            onChange={e => setData('tanggalTarget', e.target.value)}
                                            className="w-full h-14 px-5 bg-slate-50 border-0 rounded-2xl focus:ring-2 focus:ring-teal-500 font-bold"
                                            required
                                        />
                                    </div>
                                </div>

                                <div className="bg-amber-50 border border-amber-100 p-4 rounded-2xl flex gap-3">
                                    <AlertCircle className="text-amber-600 shrink-0" size={18} />
                                    <p className="text-[11px] text-amber-800 font-medium leading-relaxed">
                                        Target akan otomatis terhitung dari transaksi yang dialokasikan ke goal ini saat checkout suara.
                                    </p>
                                </div>

                                <div className="pt-6">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full h-16 bg-teal-600 text-white rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-teal-700 transition-all shadow-xl shadow-teal-600/20"
                                    >
                                        <Save size={20} />
                                        Mulai Target
                                    </button>
                                </div>
                            </form>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </AuthLayout>
    );
}
