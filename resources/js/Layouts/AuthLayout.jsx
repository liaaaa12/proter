import React, { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    LayoutDashboard, 
    Wallet, 
    Target, 
    BarChart3, 
    Settings, 
    LogOut, 
    X,
    Mic,
    MicOff,
    CheckCircle2,
    AlertCircle,
    Ear,
    Loader2
} from 'lucide-react';
import MeshGradient from '../Components/MeshGradient';
import TransactionModal from '../Components/TransactionModal';
import { useVoiceCommand } from '../Hooks/useVoiceCommand';
import axios from 'axios';

// ─── Toast Component ─────────────────────────────────────────────────
const Toast = ({ type, message, onClear }) => (
    <motion.div
        initial={{ opacity: 0, y: -20, scale: 0.9 }}
        animate={{ opacity: 1, y: 0, scale: 1 }}
        exit={{ opacity: 0, scale: 0.9, transition: { duration: 0.2 } }}
        className={`fixed top-6 left-1/2 -translate-x-1/2 z-[100] flex items-center gap-3 px-6 py-4 rounded-2xl shadow-2xl border backdrop-blur-md ${
            type === 'success' 
            ? 'bg-teal-600/90 border-teal-500 text-white' 
            : 'bg-rose-600/90 border-rose-500 text-white'
        }`}
    >
        {type === 'success' ? <CheckCircle2 size={20} /> : <AlertCircle size={20} />}
        <span className="font-bold text-sm">{message}</span>
        <button onClick={onClear} className="ml-2 hover:bg-white/10 p-1 rounded-full transition-colors">
            <X size={16} />
        </button>
    </motion.div>
);

// ─── Voice Feedback Overlay ──────────────────────────────────────────
const VoiceFeedback = ({ feedback, transcript, onClear }) => {
    const colorMap = {
        listening:  'bg-indigo-600/90 border-indigo-400 text-white',
        processing: 'bg-amber-600/90 border-amber-400 text-white',
        success:    'bg-teal-600/90 border-teal-500 text-white',
        error:      'bg-rose-600/90 border-rose-500 text-white',
    };
    const iconMap = {
        listening: (
            <motion.div animate={{ scale: [1, 1.3, 1] }} transition={{ repeat: Infinity, duration: 1.2 }}>
                <Ear size={20} />
            </motion.div>
        ),
        processing: (
            <motion.div animate={{ rotate: 360 }} transition={{ repeat: Infinity, duration: 1, ease: 'linear' }}>
                <Loader2 size={20} />
            </motion.div>
        ),
        success: <CheckCircle2 size={20} />,
        error: <AlertCircle size={20} />,
    };

    return (
        <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.9 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 20, scale: 0.9, transition: { duration: 0.2 } }}
            className={`fixed bottom-28 lg:bottom-8 left-1/2 -translate-x-1/2 z-[100] flex items-center gap-3 px-6 py-4 rounded-2xl shadow-2xl border backdrop-blur-md min-w-[280px] max-w-[90vw] ${colorMap[feedback.type] || colorMap.error}`}
        >
            {iconMap[feedback.type] || iconMap.error}
            <div className="flex-1 min-w-0">
                <span className="font-bold text-sm block">{feedback.message}</span>
                {transcript && feedback.type === 'listening' && (
                    <motion.span key={transcript} initial={{ opacity: 0 }} animate={{ opacity: 0.8 }}
                        className="text-xs opacity-80 block mt-1 truncate">
                        "{transcript}"
                    </motion.span>
                )}
            </div>
            <button onClick={onClear} className="ml-2 hover:bg-white/10 p-1 rounded-full transition-colors flex-shrink-0">
                <X size={16} />
            </button>
        </motion.div>
    );
};

// ─── Sidebar Item ────────────────────────────────────────────────────
const SidebarItem = ({ href, icon: Icon, label, active }) => (
    <Link 
        href={href}
        className={`flex items-center gap-3 px-4 py-3 rounded-2xl transition-all ${
            active 
            ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/20' 
            : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900'
        }`}
    >
        <Icon size={22} />
        <span className="font-semibold">{label}</span>
    </Link>
);

// ─── Main Layout ─────────────────────────────────────────────────────
export default function AuthLayout({ children }) {
    const { url, props } = usePage();
    const { auth, flash } = props;
    const [showToast, setShowToast] = useState(true);

    // Transaction modal state (for voice-triggered transactions)
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [transactionData, setTransactionData] = useState({
        jenis: 'Pengeluaran',
        kategori: 'Lainnya',
        jumlah: 0,
        keterangan: '',
        budget_id: null,
        goal_id: null,
    });
    const [modalBudgets, setModalBudgets] = useState([]);
    const [modalGoals, setModalGoals] = useState([]);

    // Unified voice command hook
    const { 
        isListening, isSupported, isProcessing,
        transcript, feedback,
        startListening, stopListening, clearFeedback 
    } = useVoiceCommand({
        onTransactionParsed: async (data) => {
            // Fetch budgets & goals on demand (AJAX — only when needed)
            try {
                const [budgetsRes, goalsRes] = await Promise.all([
                    axios.get('/api/budgets'),
                    axios.get('/api/goals'),
                ]);
                setModalBudgets(budgetsRes.data?.data || budgetsRes.data || []);
                setModalGoals(goalsRes.data?.data || goalsRes.data || []);
            } catch (e) {
                console.warn('Could not fetch budgets/goals for modal:', e);
            }
            setTransactionData(data);
            setIsModalOpen(true);
        },
    });

    const handleSubmitTransaction = async (e) => {
        e.preventDefault();
        try {
            await axios.post('/api/voice-transaction', transactionData, {
                headers: { 'Accept': 'application/json' }
            });
            setIsModalOpen(false);
            // Soft reload current page data without full navigation
            router.reload({ only: ['stats', 'recentTransactions', 'budgetsWithProgress', 'goals', 'allBudgets'] });
        } catch (err) {
            console.error('Transaction save error:', err);
        }
    };

    // Flash toast auto-dismiss
    React.useEffect(() => {
        if (flash?.success || flash?.error) {
            setShowToast(true);
            const t = setTimeout(() => setShowToast(false), 5000);
            return () => clearTimeout(t);
        }
    }, [flash]);

    const menuItems = [
        { href: '/dashboard', icon: LayoutDashboard, label: 'Dashboard' },
        { href: '/budgeting', icon: Wallet, label: 'Anggaran' },
        { href: '/goals', icon: Target, label: 'Target' },
        { href: '/laporan', icon: BarChart3, label: 'Laporan' },
        { href: '/settings', icon: Settings, label: 'Pengaturan' },
    ];

    const handleVoiceToggle = () => {
        if (isListening) stopListening();
        else startListening();
    };

    return (
        <div className="min-h-screen flex relative">
            <MeshGradient />

            {/* Flash Notifications */}
            <AnimatePresence>
                {showToast && flash?.success && (
                    <Toast type="success" message={flash.success} onClear={() => setShowToast(false)} />
                )}
                {showToast && flash?.error && (
                    <Toast type="error" message={flash.error} onClear={() => setShowToast(false)} />
                )}
            </AnimatePresence>

            {/* Voice Feedback Overlay */}
            <AnimatePresence>
                {feedback && (
                    <VoiceFeedback
                        feedback={feedback}
                        transcript={transcript}
                        onClear={() => { clearFeedback(); stopListening(); }}
                    />
                )}
            </AnimatePresence>

            {/* Transaction Modal (voice-triggered, available globally) */}
            <TransactionModal
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                data={transactionData}
                setData={setTransactionData}
                onSubmit={handleSubmitTransaction}
                budgets={modalBudgets}
                goals={modalGoals}
            />

            {/* ── Desktop Sidebar ─────────────────────────────────── */}
            <aside className="w-72 bg-white/70 backdrop-blur-xl border-r border-white/20 hidden lg:flex flex-col p-6 sticky top-0 h-screen z-50">
                <Link href="/dashboard" className="flex items-center justify-center mb-12">
                    <img src="/images/voica-logo.png" alt="Voica" className="w-40 h-auto" />
                </Link>

                <nav className="flex-1 space-y-2">
                    {menuItems.map((item) => (
                        <SidebarItem key={item.href} {...item} active={url.startsWith(item.href)} />
                    ))}
                </nav>

                {/* Desktop Voice Button */}
                {isSupported && (
                    <div className="py-4 border-t border-slate-100">
                        <button
                            onClick={handleVoiceToggle}
                            disabled={isProcessing}
                            className={`w-full flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-semibold ${
                                isListening
                                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                                    : isProcessing
                                    ? 'bg-amber-100 text-amber-600 cursor-wait'
                                    : 'text-indigo-600 hover:bg-indigo-50'
                            }`}
                        >
                            <motion.div
                                animate={isListening ? { scale: [1, 1.15, 1] } : {}}
                                transition={{ repeat: Infinity, duration: 1 }}
                            >
                                {isListening ? <MicOff size={22} /> : <Mic size={22} />}
                            </motion.div>
                            {isListening ? 'Mendengarkan...' : isProcessing ? 'Memproses...' : 'Perintah Suara'}
                        </button>
                    </div>
                )}

                <div className="mt-auto pt-6 border-t border-slate-100">
                    <div className="flex items-center gap-3 px-2 mb-6">
                        <div className="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-bold uppercase overflow-hidden">
                            {auth.user.avatar_url ? (
                                <img src={auth.user.avatar_url} alt={auth.user.name} className="w-full h-full object-cover" />
                            ) : (
                                auth.user.name[0]
                            )}
                        </div>
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-bold truncate">{auth.user.name}</p>
                            <p className="text-xs text-slate-400 truncate">{auth.user.email}</p>
                        </div>
                    </div>
                    <Link 
                        href="/logout" method="post" as="button"
                        className="w-full flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-50 rounded-2xl transition-all font-semibold"
                    >
                        <LogOut size={22} />
                        Keluar
                    </Link>
                </div>
            </aside>

            {/* ── Main Content ────────────────────────────────────── */}
            <main className="flex-1 flex flex-col min-w-0 overflow-hidden">
                {/* Mobile Header */}
                <header className="lg:hidden h-20 bg-white/80 backdrop-blur-md border-b border-white/20 flex items-center justify-between px-6 sticky top-0 z-40">
                    <Link href="/dashboard" className="flex items-center">
                        <img src="/images/voica-logo.png" alt="Voica" className="w-36 h-auto" />
                    </Link>
                    <Link href="/settings" className="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-bold uppercase overflow-hidden text-xs hover:ring-2 hover:ring-teal-500 transition-all">
                        {auth.user.avatar_url ? (
                            <img src={auth.user.avatar_url} alt={auth.user.name} className="w-full h-full object-cover" />
                        ) : (
                            auth.user.name[0]
                        )}
                    </Link>
                </header>

                <div className="flex-1 p-6 lg:p-10 pb-28 lg:pb-10 overflow-y-auto">
                    <AnimatePresence mode="wait">
                        <motion.div
                            key={url}
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -10 }}
                            transition={{ duration: 0.3 }}
                        >
                            {children}
                        </motion.div>
                    </AnimatePresence>
                </div>

                {/* ── Mobile Bottom Nav (2 + Mic + 2) ─────────────── */}
                <nav className="lg:hidden fixed bottom-0 w-full bg-white/95 backdrop-blur-lg border-t border-slate-100 z-40"
                     style={{ paddingBottom: 'env(safe-area-inset-bottom, 0px)' }}>
                    <div className="flex items-end justify-around h-20 px-4 relative">
                        {/* Left: Dashboard */}
                        {(() => {
                            const item = menuItems[0];
                            const Icon = item.icon;
                            const active = url.startsWith(item.href);
                            return (
                                <Link href={item.href}
                                    className={`flex flex-col items-center gap-1 pt-2 pb-2 min-w-[56px] transition-colors ${active ? 'text-teal-600' : 'text-slate-400'}`}>
                                    <Icon size={22} strokeWidth={active ? 2.5 : 1.8} />
                                    <span className="text-[10px] font-semibold">{item.label}</span>
                                </Link>
                            );
                        })()}

                        {/* Left: Anggaran */}
                        {(() => {
                            const item = menuItems[1];
                            const Icon = item.icon;
                            const active = url.startsWith(item.href);
                            return (
                                <Link href={item.href}
                                    className={`flex flex-col items-center gap-1 pt-2 pb-2 min-w-[56px] transition-colors ${active ? 'text-teal-600' : 'text-slate-400'}`}>
                                    <Icon size={22} strokeWidth={active ? 2.5 : 1.8} />
                                    <span className="text-[10px] font-semibold">{item.label}</span>
                                </Link>
                            );
                        })()}

                        {/* Center: Voice Button */}
                        {isSupported && (
                            <div className="flex flex-col items-center -mt-7 px-1">
                                <motion.button
                                    onClick={handleVoiceToggle}
                                    disabled={isProcessing}
                                    whileTap={{ scale: 0.9 }}
                                    className={`relative w-[56px] h-[56px] rounded-full flex items-center justify-center transition-all border-4 border-white ${
                                        isListening
                                            ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/40'
                                            : isProcessing
                                            ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30 cursor-wait'
                                            : 'bg-gradient-to-br from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-600/30'
                                    }`}
                                >
                                    {isListening && (
                                        <motion.span
                                            className="absolute inset-0 rounded-full border-2 border-teal-400"
                                            animate={{ scale: [1, 1.6, 1.6], opacity: [0.5, 0, 0] }}
                                            transition={{ repeat: Infinity, duration: 1.5, ease: 'easeOut' }}
                                        />
                                    )}
                                    <motion.div
                                        animate={isListening ? { scale: [1, 1.15, 1] } : {}}
                                        transition={{ repeat: Infinity, duration: 0.8 }}
                                    >
                                        {isProcessing ? <Loader2 size={24} className="animate-spin" /> : isListening ? <MicOff size={24} /> : <Mic size={24} />}
                                    </motion.div>
                                </motion.button>
                                <span className={`text-[10px] font-semibold mt-0.5 ${
                                    isListening ? 'text-indigo-600' : isProcessing ? 'text-amber-600' : 'text-slate-400'
                                }`}>
                                    {isProcessing ? '...' : isListening ? 'Stop' : 'Suara'}
                                </span>
                            </div>
                        )}

                        {/* Right: Target */}
                        {(() => {
                            const item = menuItems[2];
                            const Icon = item.icon;
                            const active = url.startsWith(item.href);
                            return (
                                <Link href={item.href}
                                    className={`flex flex-col items-center gap-1 pt-2 pb-2 min-w-[56px] transition-colors ${active ? 'text-teal-600' : 'text-slate-400'}`}>
                                    <Icon size={22} strokeWidth={active ? 2.5 : 1.8} />
                                    <span className="text-[10px] font-semibold">{item.label}</span>
                                </Link>
                            );
                        })()}

                        {/* Right: Laporan */}
                        {(() => {
                            const item = menuItems[3];
                            const Icon = item.icon;
                            const active = url.startsWith(item.href);
                            return (
                                <Link href={item.href}
                                    className={`flex flex-col items-center gap-1 pt-2 pb-2 min-w-[56px] transition-colors ${active ? 'text-teal-600' : 'text-slate-400'}`}>
                                    <Icon size={22} strokeWidth={active ? 2.5 : 1.8} />
                                    <span className="text-[10px] font-semibold">{item.label}</span>
                                </Link>
                            );
                        })()}
                    </div>
                </nav>
            </main>
        </div>
    );
}
