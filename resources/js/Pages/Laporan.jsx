import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthLayout from '../Layouts/AuthLayout';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    BarChart3, 
    Download, 
    Filter, 
    Search, 
    ArrowUpCircle, 
    ArrowDownCircle, 
    Wallet,
    Calendar,
    ChevronDown,
    FileText
} from 'lucide-react';
import { 
    BarChart, 
    Bar, 
    XAxis, 
    YAxis, 
    CartesianGrid, 
    Tooltip, 
    Legend, 
    ResponsiveContainer,
    PieChart,
    Pie,
    Cell,
    AreaChart,
    Area
} from 'recharts';

const SummaryCard = ({ title, amount, icon: Icon, color }) => (
    <motion.div 
        variants={{
            hidden: { opacity: 0, y: 20 },
            visible: { opacity: 1, y: 0 }
        }}
        whileHover={{ scale: 1.02, y: -5 }}
        className="relative group bg-white/40 backdrop-blur-xl p-7 rounded-[32px] border border-white/40 shadow-sm flex items-center gap-6 overflow-hidden"
    >
        <div className={`absolute -right-4 -top-4 w-24 h-24 rounded-full blur-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-500 ${color.split(' ')[0]}`} />
        
        <div className={`w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 relative z-10 ${color}`}>
            <Icon size={28} />
        </div>
        <div className="relative z-10">
            <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">{title}</p>
            <h3 className="text-2xl font-bold font-outfit text-slate-900 tracking-tight">
                <span className="text-sm font-medium text-slate-400 mr-1">Rp</span>
                {new Intl.NumberFormat('id-ID').format(amount)}
            </h3>
        </div>
    </motion.div>
);

const COLORS = ['#0d9488', '#0ea5e9', '#6366f1', '#8b5cf6', '#d946ef', '#f43f5e', '#f59e0b'];

export default function Laporan({ years }) {
    const today = new Date();
    const [filters, setFilters] = useState({
        bulan_dari: today.getMonth() + 1,
        tahun_dari: today.getFullYear(),
        bulan_sampai: today.getMonth() + 1,
        tahun_sampai: today.getFullYear(),
    });
    const [loading, setLoading] = useState(false);
    const [transactions, setTransactions] = useState([]);
    const [chartData, setChartData] = useState([]);
    const [categoryData, setCategoryData] = useState([]);
    const [summary, setSummary] = useState({
        total_pemasukan: 0,
        total_pengeluaran: 0,
        saldo_akhir: 0
    });

    const months = [
        { id: 1, name: 'Januari' }, { id: 2, name: 'Februari' }, { id: 3, name: 'Maret' },
        { id: 4, name: 'April' }, { id: 5, name: 'Mei' }, { id: 6, name: 'Juni' },
        { id: 7, name: 'Juli' }, { id: 8, name: 'Agustus' }, { id: 9, name: 'September' },
        { id: 10, name: 'Oktober' }, { id: 11, name: 'November' }, { id: 12, name: 'Desember' }
    ];

    const fetchLaporan = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/laporan/transactions', { params: filters });
            if (response.data.success) {
                setTransactions(response.data.transactions);
                setSummary(response.data.summary);
                setChartData(response.data.chart_data);
                setCategoryData(response.data.category_data);
            }
        } catch (err) {
            console.error('Failed to fetch reports:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchLaporan();
    }, []);

    const handleExport = () => {
        const queryParams = new URLSearchParams(filters).toString();
        window.location.href = `/api/laporan/export-pdf?${queryParams}`;
    };

    return (
        <AuthLayout>
            <Head title="Laporan Keuangan" />

            <div className="max-w-7xl mx-auto">
                {/* Header Area */}
                <motion.div 
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12"
                >
                    <div>
                        <h1 className="text-5xl font-bold font-outfit mb-3 tracking-tighter text-slate-900">Analisa Laporan</h1>
                        <p className="text-slate-500 font-medium">Pantau kesehatan finansial Anda melalui data transaksional yang akurat.</p>
                    </div>

                    <div className="flex flex-wrap items-center gap-4">
                        <motion.button 
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                            onClick={handleExport}
                            className="bg-slate-900 text-white px-8 py-4 rounded-2xl flex items-center justify-center gap-3 font-bold hover:bg-slate-800 transition-all shadow-2xl shadow-slate-900/40 group overflow-hidden relative"
                        >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite]" />
                            <Download size={18} className="relative z-10" />
                            <span className="relative z-10">Unduh PDF</span>
                        </motion.button>
                    </div>
                </motion.div>

                {/* Filters Area */}
                <motion.div 
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.1 }}
                    className="bg-white/40 backdrop-blur-xl p-8 rounded-[48px] border border-white/40 shadow-sm mb-12"
                >
                    <div className="flex items-center gap-3 mb-8">
                        <div className="w-10 h-10 bg-teal-500/10 rounded-xl flex items-center justify-center text-teal-600">
                            <Filter size={18} />
                        </div>
                        <h4 className="font-black text-slate-900 uppercase tracking-[0.2em] text-[10px]">Filter Periode</h4>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 items-end">
                        <div className="space-y-3">
                            <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari Bulan</label>
                            <select 
                                value={filters.bulan_dari}
                                onChange={e => setFilters({...filters, bulan_dari: e.target.value})}
                                className="w-full h-14 px-5 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-sm"
                            >
                                {months.map(m => <option key={m.id} value={m.id}>{m.name}</option>)}
                            </select>
                        </div>
                        <div className="space-y-3">
                            <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tahun</label>
                            <select 
                                value={filters.tahun_dari}
                                onChange={e => setFilters({...filters, tahun_dari: e.target.value})}
                                className="w-full h-14 px-5 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-sm"
                            >
                                {years.map(y => <option key={y} value={y}>{y}</option>)}
                            </select>
                        </div>
                        <div className="hidden lg:flex items-center justify-center h-14 text-slate-300">
                             <ArrowUpCircle className="rotate-90 opacity-40" size={24} />
                        </div>
                        <div className="space-y-3">
                            <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai Bulan</label>
                            <select 
                                value={filters.bulan_sampai}
                                onChange={e => setFilters({...filters, bulan_sampai: e.target.value})}
                                className="w-full h-14 px-5 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-sm"
                            >
                                {months.map(m => <option key={m.id} value={m.id}>{m.name}</option>)}
                            </select>
                        </div>
                        <div className="flex gap-3">
                            <div className="flex-1 space-y-3">
                                <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tahun</label>
                                <select 
                                    value={filters.tahun_sampai}
                                    onChange={e => setFilters({...filters, tahun_sampai: e.target.value})}
                                    className="w-full h-14 px-5 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-sm"
                                >
                                    {years.map(y => <option key={y} value={y}>{y}</option>)}
                                </select>
                            </div>
                            <motion.button 
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                onClick={fetchLaporan}
                                disabled={loading}
                                className="h-14 w-14 bg-teal-600 text-white rounded-2xl flex items-center justify-center hover:bg-teal-700 transition-all shadow-xl shadow-teal-600/30 disabled:opacity-50"
                            >
                                <Search size={22} />
                            </motion.button>
                        </div>
                    </div>
                </motion.div>

                {/* Chart Area */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-12">
                    {/* Trend Chart */}
                    <motion.div 
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: 0.2 }}
                        className="lg:col-span-2 bg-white/40 backdrop-blur-xl p-10 rounded-[48px] border border-white/40 shadow-sm"
                    >
                        <div className="flex items-center justify-between mb-10">
                            <div>
                                <h4 className="text-2xl font-bold font-outfit text-slate-900 tracking-tight">Financial Trends</h4>
                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2">Visualisasi arus kas harian</p>
                            </div>
                            <div className="w-12 h-12 bg-teal-500/10 rounded-2xl flex items-center justify-center text-teal-600">
                                <BarChart3 size={24} />
                            </div>
                        </div>
                        <div className="h-[350px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={chartData}>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                    <XAxis 
                                        dataKey="date" 
                                        axisLine={false} 
                                        tickLine={false} 
                                        tick={{fill: '#94a3b8', fontSize: 10, fontWeight: '700'}}
                                        tickFormatter={(str) => {
                                            const d = new Date(str);
                                            return `${d.getDate()}/${d.getMonth()+1}`;
                                        }}
                                    />
                                    <YAxis 
                                        axisLine={false} 
                                        tickLine={false} 
                                        tick={{fill: '#94a3b8', fontSize: 10, fontWeight: '700'}}
                                        tickFormatter={(val) => `Rp ${val/1000}k`}
                                    />
                                    <Tooltip 
                                        cursor={{fill: 'rgba(20, 184, 166, 0.05)'}}
                                        contentStyle={{ 
                                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                            backdropFilter: 'blur(10px)',
                                            borderRadius: '24px', 
                                            border: '1px solid rgba(255,255,255,0.4)', 
                                            boxShadow: '0 20px 40px -10px rgba(0,0,0,0.1)' 
                                        }}
                                        formatter={(val) => `Rp ${new Intl.NumberFormat('id-ID').format(val)}`}
                                    />
                                    <Legend iconType="circle" />
                                    <Bar dataKey="pemasukan" name="Pemasukan" fill="#0d9488" radius={[6, 6, 0, 0]} />
                                    <Bar dataKey="pengeluaran" name="Pengeluaran" fill="#f43f5e" radius={[6, 6, 0, 0]} />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </motion.div>

                    {/* Distribution Chart */}
                    <motion.div 
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: 0.3 }}
                        className="bg-slate-900 border border-white/5 p-10 rounded-[48px] text-white shadow-3xl overflow-hidden relative"
                    >
                        <motion.div 
                            animate={{ opacity: [0.1, 0.2, 0.1] }}
                            transition={{ duration: 10, repeat: Infinity }}
                            className="absolute -top-20 -right-20 w-64 h-64 bg-indigo-500/20 rounded-full blur-[100px]" 
                        />
                        
                        <div className="flex items-center justify-between mb-10 relative z-10">
                            <div>
                                <h4 className="text-xl font-bold font-outfit text-white tracking-tight">Allocation</h4>
                                <p className="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mt-2">Distribusi Per Kategori</p>
                            </div>
                        </div>
                        <div className="h-[250px] w-full relative z-10">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie
                                        data={categoryData}
                                        innerRadius={70}
                                        outerRadius={95}
                                        paddingAngle={8}
                                        dataKey="value"
                                        stroke="none"
                                    >
                                        {categoryData.map((entry, index) => (
                                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                        ))}
                                    </Pie>
                                    <Tooltip 
                                        contentStyle={{ 
                                            backgroundColor: '#0f172a',
                                            borderRadius: '20px', 
                                            border: '1px solid rgba(255,255,255,0.1)', 
                                            boxShadow: '0 20px 40px rgba(0,0,0,0.4)',
                                            color: '#fff' 
                                        }}
                                        itemStyle={{ color: '#fff' }}
                                        formatter={(val) => `Rp ${new Intl.NumberFormat('id-ID').format(val)}`}
                                    />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                        <div className="mt-8 space-y-4 relative z-10">
                            {categoryData.length > 0 ? categoryData.slice(0, 4).map((item, index) => (
                                <div key={index} className="flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-2 h-2 rounded-full" style={{ backgroundColor: COLORS[index % COLORS.length] }} />
                                        <span className="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-white transition-colors">{item.name}</span>
                                    </div>
                                    <span className="font-outfit font-bold text-teal-400">
                                        {((item.value / summary.total_pengeluaran) * 100).toFixed(1)}%
                                    </span>
                                </div>
                            )) : (
                                <p className="text-center text-slate-500 text-[10px] font-black uppercase tracking-widest py-10">No data available</p>
                            )}
                        </div>
                    </motion.div>
                </div>

                {/* Summary Grid */}
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
                    className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12"
                >
                    <SummaryCard 
                        title="Pemasukan" 
                        amount={summary.total_pemasukan} 
                        icon={ArrowUpCircle} 
                        color="bg-teal-500/10 text-teal-600" 
                    />
                    <SummaryCard 
                        title="Pengeluaran" 
                        amount={summary.total_pengeluaran} 
                        icon={ArrowDownCircle} 
                        color="bg-rose-500/10 text-rose-600" 
                    />
                    <SummaryCard 
                        title="Saldo Akhir" 
                        amount={summary.saldo_akhir} 
                        icon={Wallet} 
                        color="bg-slate-900 text-white" 
                    />
                </motion.div>

                {/* Transactions Table */}
                <motion.div 
                    initial={{ opacity: 0, y: 30 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.4 }}
                    className="bg-white/40 backdrop-blur-xl rounded-[48px] border border-white/40 shadow-sm overflow-hidden"
                >
                    <div className="p-10 border-b border-white/40 flex items-center justify-between">
                        <div>
                            <h4 className="text-2xl font-bold font-outfit text-slate-900 tracking-tight">Financial History</h4>
                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2">Daftar transaksi dalam periode</p>
                        </div>
                        <span className="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] bg-teal-500/10 px-4 py-2 rounded-full border border-teal-500/10">{transactions.length} Entri Ditemukan</span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="bg-slate-900/5">
                                    <th className="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tanggal</th>
                                    <th className="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aktivitas</th>
                                    <th className="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kategori</th>
                                    <th className="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Nominal</th>
                                    <th className="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {loading ? (
                                    <tr>
                                        <td colSpan="5" className="px-10 py-24 text-center">
                                            <div className="flex flex-col items-center gap-6">
                                                <div className="w-12 h-12 border-4 border-teal-600 border-t-transparent rounded-full animate-spin shadow-lg"></div>
                                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Memuat data laporan...</p>
                                            </div>
                                        </td>
                                    </tr>
                                ) : transactions.length > 0 ? (
                                    transactions.map((t, idx) => (
                                        <motion.tr 
                                            key={t.id} 
                                            initial={{ opacity: 0, x: -10 }}
                                            animate={{ opacity: 1, x: 0 }}
                                            transition={{ delay: 0.05 * idx }}
                                            className="hover:bg-teal-50/30 transition-all group cursor-pointer"
                                        >
                                            <td className="px-10 py-6 text-sm font-bold text-slate-400">{t.tanggal}</td>
                                            <td className="px-10 py-6 text-sm font-bold text-slate-900 group-hover:text-teal-600 transition-colors tracking-tight">{t.keterangan}</td>
                                            <td className="px-10 py-6">
                                                <span className="px-4 py-1.5 bg-white shadow-sm border border-slate-100 rounded-full text-slate-600 font-black text-[9px] uppercase tracking-widest">
                                                    {t.kategori}
                                                </span>
                                            </td>
                                            <td className={`px-10 py-6 text-sm font-bold text-right font-outfit ${t.jenis === 'Pemasukan' ? 'text-teal-600' : 'text-slate-900 text-opacity-80'}`}>
                                                {t.jenis === 'Pemasukan' ? '+' : '-'}{t.jumlah_formatted}
                                            </td>
                                            <td className="px-10 py-6 text-sm font-bold text-slate-900 text-right font-outfit">{t.saldo_formatted}</td>
                                        </motion.tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-10 py-32 text-center text-slate-400">
                                            <div className="flex flex-col items-center gap-6">
                                                <FileText className="text-slate-100" size={64} />
                                                <p className="text-[10px] font-black uppercase tracking-[0.2em]">Tidak ada data untuk periode ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </motion.div>
            </div>
        </AuthLayout>
    );
}
