import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    LayoutDashboard, 
    Wallet, 
    Target, 
    BarChart3, 
    Settings, 
    LogOut, 
    Menu, 
    X,
    Mic,
    CheckCircle2,
    AlertCircle
} from 'lucide-react';
import MeshGradient from '../Components/MeshGradient';

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

export default function AuthLayout({ children }) {
    const { url, props } = usePage();
    const { auth, flash } = props;
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const [showToast, setShowToast] = useState(true);

    // Reset toast state when flash message changes
    React.useEffect(() => {
        if (flash?.success || flash?.error) {
            setShowToast(true);
            const timer = setTimeout(() => setShowToast(false), 5000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const menuItems = [
        { href: '/dashboard', icon: LayoutDashboard, label: 'Dashboard' },
        { href: '/budgeting', icon: Wallet, label: 'Anggaran' },
        { href: '/goals', icon: Target, label: 'Target' },
        { href: '/laporan', icon: BarChart3, label: 'Laporan' },
        { href: '/settings', icon: Settings, label: 'Pengaturan' },
    ];

    return (
        <div className="min-h-screen flex relative">
            <MeshGradient />
            {/* Global Flash Notifications */}
            <AnimatePresence>
                {showToast && flash?.success && (
                    <Toast 
                        type="success" 
                        message={flash.success} 
                        onClear={() => setShowToast(false)} 
                    />
                )}
                {showToast && flash?.error && (
                    <Toast 
                        type="error" 
                        message={flash.error} 
                        onClear={() => setShowToast(false)} 
                    />
                )}
            </AnimatePresence>
            {/* Desktop Sidebar */}
            <aside className="w-72 bg-white/70 backdrop-blur-xl border-r border-white/20 hidden lg:flex flex-col p-6 sticky top-0 h-screen z-50">
                <Link href="/dashboard" className="flex items-center justify-center mb-12">
                    <img src="/images/voica-logo.png" alt="Voica" className="h-16 w-auto" />
                </Link>

                <nav className="flex-1 space-y-2">
                    {menuItems.map((item) => (
                        <SidebarItem 
                            key={item.href}
                            {...item}
                            active={url.startsWith(item.href)}
                        />
                    ))}
                </nav>

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
                        href="/logout" 
                        method="post" 
                        as="button"
                        className="w-full flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-50 rounded-2xl transition-all font-semibold"
                    >
                        <LogOut size={22} />
                        Keluar
                    </Link>
                </div>
            </aside>

            {/* Main Content */}
            <main className="flex-1 flex flex-col min-w-0 overflow-hidden">
                {/* Mobile Header */}
                <header className="lg:hidden h-20 bg-white/80 backdrop-blur-md border-b border-white/20 flex items-center justify-between px-6 sticky top-0 z-40">
                    <div className="w-10"></div> {/* Spacer for symmetry */}
                    <Link href="/dashboard" className="flex items-center justify-center flex-1">
                        <img src="/images/voica-logo.png" alt="Voica" className="h-14 w-auto" />
                    </Link>
                    <div className="w-10"></div> {/* Spacer for symmetry */}
                </header>

                <div className="flex-1 p-6 lg:p-10 pb-24 lg:pb-10 overflow-y-auto">
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

                {/* Mobile Bottom Nav */}
                <nav className="lg:hidden fixed bottom-0 w-full bg-white border-t border-slate-100 flex justify-around items-center h-20 px-4 pb-safe z-40">
                    {menuItems.map((item) => {
                        const Icon = item.icon;
                        const active = url.startsWith(item.href);
                        return (
                            <Link 
                                key={item.href}
                                href={item.href}
                                className={`flex flex-col items-center gap-1 ${active ? 'text-teal-600' : 'text-slate-400'}`}
                            >
                                <Icon size={24} strokeWidth={active ? 2.5 : 2} />
                                <span className="text-[10px] font-bold uppercase tracking-wider">{item.label}</span>
                            </Link>
                        );
                    })}
                </nav>
            </main>
        </div>
    );
}
