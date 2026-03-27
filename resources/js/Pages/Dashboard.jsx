import React from 'react';
import { Head, router } from '@inertiajs/react';
import AuthLayout from '../Layouts/AuthLayout';
import { motion } from 'framer-motion';
import { 
    WalletIcon as Wallet, 
    ArrowTrendingUpIcon as TrendingUp, 
    ArrowTrendingDownIcon as TrendingDown, 
    TrophyIcon as Target, 
    ChevronRightIcon as ChevronRight,
    ArrowUpRightIcon as ArrowUpRight,
    ArrowDownLeftIcon as ArrowDownLeft,
    PlusIcon as Plus
} from '@heroicons/react/24/solid';

const StatCard = ({ title, amount, icon: Icon, color, trend }) => (
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
        className="relative group bg-white/40 backdrop-blur-xl p-7 rounded-[32px] border border-white/40 shadow-sm flex flex-col justify-between overflow-hidden"
    >
        {/* Inner Glow Decorative Element */}
        <div className={`absolute -right-4 -top-4 w-24 h-24 rounded-full blur-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-500 ${color.bg}`} />
        
        <div className="flex items-center justify-between mb-6 relative z-10">
            <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 ${color.bg} ${color.text}`}>
                <Icon className="w-6 h-6" />
            </div>
            {trend !== 0 && trend !== undefined && trend !== null ? (
                <span className={`text-xs font-bold px-2.5 py-1 rounded-full backdrop-blur-md border ${trend > 0 ? 'bg-teal-500/10 border-teal-500/20 text-teal-600' : 'bg-rose-500/10 border-rose-500/20 text-rose-600'}`}>
                    {trend > 0 ? '↑' : '↓'} {Math.abs(trend)}%
                </span>
            ) : null}
        </div>
        <div className="relative z-10">
            <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">{title}</p>
            <h3 className="text-3xl font-bold font-outfit tracking-tight text-slate-900">
                <span className="text-sm font-medium text-slate-400 mr-1">Rp</span>
                {new Intl.NumberFormat('id-ID').format(amount)}
            </h3>
        </div>
    </motion.div>
);

export default function Dashboard({ stats, analysis, goal, goalPercentage, recentTransactions }) {

    return (
        <AuthLayout>
            <Head title="Dashboard" />

            <div className="max-w-7xl mx-auto">
                {/* Header Area */}
                <motion.div 
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12"
                >
                    <div>
                        <h1 className="text-5xl font-bold font-outfit mb-3 tracking-tighter text-slate-900">Dashboard</h1>
                        <p className="text-slate-500 font-medium">Selamat datang kembali! Mari kendalikan finansial Anda hari ini.</p>
                    </div>
                </motion.div>

                {/* Stats Grid */}
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
                    className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12"
                >
                    <StatCard 
                        title="Saldo Total" 
                        amount={stats.saldo} 
                        icon={Wallet} 
                        color={{ bg: 'bg-indigo-500/10', text: 'text-indigo-600' }}
                    />
                    <StatCard 
                        title="Pemasukan" 
                        amount={stats.totalPemasukan} 
                        icon={TrendingUp} 
                        color={{ bg: 'bg-teal-500/10', text: 'text-teal-600' }}
                        trend={stats.pemasukanTrend}
                    />
                    <StatCard 
                        title="Pengeluaran" 
                        amount={stats.totalPengeluaran} 
                        icon={TrendingDown} 
                        color={{ bg: 'bg-rose-500/10', text: 'text-rose-600' }}
                        trend={stats.pengeluaranTrend}
                    />
                    {goal ? (
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 20 },
                                visible: { opacity: 1, y: 0 }
                            }}
                            whileHover={{ scale: 1.02, y: -5 }}
                            className="bg-slate-900 p-7 rounded-[32px] text-white flex flex-col justify-between shadow-2xl shadow-slate-900/40"
                        >
                            <div className="flex items-center justify-between mb-6">
                                <div className="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-teal-400">
                                    <Target className="w-6 h-6" />
                                </div>
                                <span className="text-xs font-bold text-teal-400">{Math.round(goalPercentage)}%</span>
                            </div>
                            <div>
                                <p className="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-2">Target: {goal.namaGoal}</p>
                                <div className="w-full h-2 bg-white/5 rounded-full mt-2 overflow-hidden">
                                    <motion.div 
                                        initial={{ width: 0 }}
                                        animate={{ width: `${goalPercentage}%` }}
                                        transition={{ duration: 1.5, ease: "easeOut" }}
                                        className="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full" 
                                    />
                                </div>
                            </div>
                        </motion.div>
                    ) : (
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 20 },
                                visible: { opacity: 1, y: 0 }
                            }}
                            className="bg-white/30 backdrop-blur-md border-2 border-dashed border-slate-200 p-7 rounded-[32px] flex flex-col items-center justify-center text-center group cursor-pointer hover:border-teal-600 hover:bg-teal-50/50 transition-all"
                        >
                            <Plus className="w-8 h-8 text-slate-300 group-hover:text-teal-600 mb-2 transition-transform group-hover:rotate-90" />
                            <p className="font-bold text-slate-400 group-hover:text-teal-600 uppercase tracking-widest text-[10px]">Tambah Target</p>
                        </motion.div>
                    )}
                </motion.div>

                <div className="grid lg:grid-cols-3 gap-10">
                    {/* Recent Transactions */}
                    <div className="lg:col-span-2">
                        <div className="flex items-center justify-between mb-8">
                            <h2 className="text-3xl font-bold font-outfit text-slate-900 tracking-tight">Recent Activity</h2>
                            <button className="text-[10px] font-black uppercase tracking-[0.2em] text-teal-600 hover:text-teal-700 transition-colors bg-teal-500/10 px-4 py-2 rounded-full border border-teal-500/10">Lihat Semua</button>
                        </div>
                        
                        <motion.div 
                            initial="hidden"
                            animate="visible"
                            variants={{
                                visible: {
                                    transition: {
                                        staggerChildren: 0.05
                                    }
                                }
                            }}
                            className="space-y-4"
                        >
                            {recentTransactions.map((tx, idx) => (
                                <motion.div 
                                    key={tx.id}
                                    variants={{
                                        hidden: { opacity: 0, x: -20 },
                                        visible: { opacity: 1, x: 0 }
                                    }}
                                    whileHover={{ x: 10 }}
                                    className="bg-white/40 backdrop-blur-md p-5 rounded-[28px] border border-white/40 flex items-center justify-between shadow-sm hover:shadow-md transition-all group cursor-pointer"
                                >
                                    <div className="flex items-center gap-5">
                                        <div className={`w-14 h-14 rounded-2xl flex items-center justify-center shadow-inner transition-transform group-hover:scale-110 ${
                                            tx.jenis === 'Pemasukan' ? 'bg-teal-500/10 text-teal-600' : 'bg-rose-500/10 text-rose-600'
                                        }`}>
                                            {tx.jenis === 'Pemasukan' ? <ArrowUpRight className="w-6 h-6" /> : <ArrowDownLeft className="w-6 h-6" />}
                                        </div>
                                        <div>
                                            <p className="font-bold text-slate-900 text-lg tracking-tight group-hover:text-teal-600 transition-colors">{tx.keterangan}</p>
                                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">{tx.kategori}</p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className={`font-bold text-xl font-outfit ${tx.jenis === 'Pemasukan' ? 'text-teal-600' : 'text-slate-900'}`}>
                                            {tx.jenis === 'Pemasukan' ? '+' : '-'} {new Intl.NumberFormat('id-ID').format(tx.jumlah)}
                                        </p>
                                        <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 opacity-60">
                                            {new Date(tx.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                        </p>
                                    </div>
                                </motion.div>
                            ))}
                        </motion.div>
                    </div>

                    {/* Organic Insight Panel */}
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ delay: 0.5 }}
                    >
                        <div className="bg-slate-900/90 backdrop-blur-2xl p-10 rounded-[48px] text-white overflow-hidden relative group border border-white/10 shadow-3xl">
                            {/* Animated sound-reactive pattern placeholder (subtle mesh glow) */}
                            <motion.div 
                                animate={{ 
                                    opacity: [0.1, 0.3, 0.1],
                                    scale: [1, 1.1, 1]
                                }}
                                transition={{ duration: 10, repeat: Infinity }}
                                className="absolute -top-20 -right-20 w-64 h-64 bg-teal-500/30 rounded-full blur-[80px] pointer-events-none"
                            />
                            
                            <div className="relative z-10">
                                <div className="inline-block px-4 py-1.5 mb-8 text-[10px] font-black tracking-widest uppercase bg-teal-500/20 text-teal-400 rounded-full border border-teal-500/20">
                                    Smart Insights
                                </div>
                                <h3 className="text-3xl font-bold font-outfit mb-6 leading-tight tracking-tight">{analysis.title}</h3>
                                <p className="text-slate-400 mb-10 leading-relaxed font-medium">{analysis.message}</p>
                                
                                <motion.button 
                                    whileHover={{ scale: 1.02 }}
                                    whileTap={{ scale: 0.98 }}
                                    onClick={() => router.get('/laporan')}
                                    className="w-full h-16 bg-white text-slate-900 rounded-2xl font-bold flex items-center justify-center gap-3 hover:bg-teal-50 transition-all shadow-xl shadow-white/5"
                                >
                                    Buka Laporan Penuh <ChevronRight className="w-[18px] h-[18px] text-teal-600" />
                                </motion.button>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </div>
        </AuthLayout>
    );
}
