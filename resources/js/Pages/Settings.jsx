import React, { useState, useRef } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AuthLayout from '../Layouts/AuthLayout';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    Settings as SettingsIcon, 
    User, 
    Lock, 
    ShieldCheck, 
    Bell, 
    ChevronRight,
    Camera,
    Save,
    Phone,
    Mail,
    CheckCircle2,
    Mic,
    Image as ImageIcon,
    LogOut
} from 'lucide-react';
import { Link } from '@inertiajs/react';

const SettingsSection = ({ title, description, children, icon: Icon }) => (
    <motion.div 
        variants={{
            hidden: { opacity: 0, y: 20 },
            visible: { opacity: 1, y: 0 }
        }}
        className="bg-white/40 backdrop-blur-xl rounded-[48px] border border-white/40 shadow-sm overflow-hidden mb-10"
    >
        <div className="p-10 border-b border-white/40 flex items-center justify-between">
            <div className="flex items-center gap-6">
                <div className="w-14 h-14 bg-teal-500/10 rounded-2xl flex items-center justify-center text-teal-600 shadow-inner">
                    <Icon size={28} />
                </div>
                <div>
                    <h4 className="text-xl font-bold text-slate-900 tracking-tight">{title}</h4>
                    <p className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2">{description}</p>
                </div>
            </div>
        </div>
        <div className="p-10">
            {children}
        </div>
    </motion.div>
);

export default function Settings({ user }) {
    const fileInputRef = useRef(null);
    const [avatarPreview, setAvatarPreview] = useState(user.avatar_url);

    const { data, setData, post, processing, errors, reset, recentlySuccessful } = useForm({
        name: user.name,
        email: user.email,
        phone: user.phone,
        avatar_file: null,
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handleAvatarClick = () => {
        fileInputRef.current.click();
    };

    const handleAvatarChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('avatar_file', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setAvatarPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        
        // Use post with _method: 'PUT' if needed, or just stay with POST for Laravel's handling of files
        post('/settings/update', {
            forceFormData: true,
            onSuccess: () => {
                reset('current_password', 'password', 'password_confirmation', 'avatar_file');
            },
        });
    };

    return (
        <AuthLayout>
            <Head title="Pengaturan Akun" />

            <div className="max-w-4xl mx-auto">
                {/* Header */}
                <motion.div 
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="mb-12"
                >
                    <h1 className="text-5xl font-bold font-outfit mb-3 tracking-tighter text-slate-900">Pengaturan</h1>
                    <p className="text-slate-500 font-medium">Kelola profil, keamanan, dan preferensi akun Anda.</p>
                </motion.div>

                {/* Profile Snapshot (Hero Banner) */}
                <motion.div 
                    initial={{ opacity: 0, scale: 0.98 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: 0.1 }}
                    className="bg-slate-900/90 backdrop-blur-3xl rounded-[48px] p-10 md:p-14 mb-12 text-white relative overflow-hidden border border-white/5 shadow-3xl"
                >
                    {/* Animated Decorative Element */}
                    <motion.div 
                        animate={{ 
                            scale: [1, 1.2, 1],
                            opacity: [0.1, 0.2, 0.1]
                        }}
                        transition={{ duration: 10, repeat: Infinity }}
                        className="absolute -top-20 -right-20 w-96 h-96 bg-teal-500/20 rounded-full blur-[100px]" 
                    />

                    <div className="relative z-10 flex flex-col md:flex-row items-center gap-10">
                        <motion.div 
                            whileHover={{ scale: 1.05 }}
                            className="relative group cursor-pointer" 
                            onClick={handleAvatarClick}
                        >
                            <div className="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center text-white text-4xl font-bold border-4 border-white/10 shadow-2xl overflow-hidden relative">
                                {avatarPreview ? (
                                    <img src={avatarPreview} alt={user.name} className="w-full h-full object-cover" />
                                ) : (
                                    user.name[0]
                                )}
                                <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <Camera size={28} className="text-white" />
                                </div>
                            </div>
                            <input 
                                type="file" 
                                ref={fileInputRef} 
                                onChange={handleAvatarChange} 
                                className="hidden" 
                                accept="image/*"
                            />
                        </motion.div>
                        <div className="text-center md:text-left">
                            <h3 className="text-4xl font-bold font-outfit mb-2 tracking-tight">{user.name}</h3>
                            <p className="text-slate-400 font-medium text-lg">{user.email || 'Email belum diatur'}</p>
                            <div className="flex flex-wrap justify-center md:justify-start gap-4 mt-6">
                                <span className="px-5 py-2 bg-white/5 border border-white/5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                    <ShieldCheck size={14} className="text-teal-400" />
                                    Account Verified
                                </span>
                                {user.voice_enrolled_at ? (
                                    <span className="px-5 py-2 bg-teal-500/20 text-teal-400 rounded-full text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2 border border-teal-500/20">
                                        <Mic size={14} />
                                        Voice ID: Active
                                    </span>
                                ) : (
                                    <span className="px-5 py-2 bg-rose-500/20 text-rose-400 rounded-full text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2 border border-rose-500/20">
                                        <Bell size={14} />
                                        Voice ID: Inactive
                                    </span>
                                )}
                            </div>
                        </div>
                    </div>
                </motion.div>

                <form onSubmit={submit}>
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
                        className="space-y-10"
                    >
                    {/* General Settings */}
                    <SettingsSection 
                        title="Informasi Profil" 
                        description="Perbarui informasi identitas publik Anda."
                        icon={User}
                    >
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-3 lg:col-span-2">
                                <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                <div className="relative">
                                    <User className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400" size={20} />
                                    <input 
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="w-full h-14 pl-14 pr-6 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                                        placeholder="Masukkan nama lengkap"
                                        required
                                    />
                                </div>
                                {errors.name && <p className="text-rose-500 text-[10px] font-black mt-2 ml-1 uppercase tracking-widest">{errors.name}</p>}
                            </div>
                            <div className="space-y-3">
                                <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
                                <div className="relative">
                                    <Mail className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400" size={20} />
                                    <input 
                                        type="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        className="w-full h-14 pl-14 pr-6 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                                        placeholder="example@email.com"
                                    />
                                </div>
                                {errors.email && <p className="text-rose-500 text-[10px] font-black mt-2 ml-1 uppercase tracking-widest">{errors.email}</p>}
                            </div>
                            <div className="space-y-3">
                                <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
                                <div className="relative">
                                    <Phone className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400" size={20} />
                                    <input 
                                        type="tel"
                                        value={data.phone}
                                        onChange={e => setData('phone', e.target.value)}
                                        className="w-full h-14 pl-14 pr-6 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                                        placeholder="08xxxxxxxxxx"
                                        required
                                    />
                                </div>
                                {errors.phone && <p className="text-rose-500 text-[10px] font-black mt-2 ml-1 uppercase tracking-widest">{errors.phone}</p>}
                            </div>
                        </div>
                    </SettingsSection>

                    {/* Security Settings */}
                    <SettingsSection 
                        title="Autentikasi & Keamanan" 
                        description="Proteksi akun dengan enkripsi kata sandi."
                        icon={Lock}
                    >
                        <div className="space-y-8">
                            <motion.div 
                                whileHover={{ scale: 1.01 }}
                                className="bg-slate-900 border border-white/5 rounded-[32px] p-6 flex gap-6 items-start shadow-2xl"
                            >
                                <div className="w-12 h-12 rounded-2xl bg-teal-500/10 flex items-center justify-center text-teal-400 flex-shrink-0 shadow-lg relative overflow-hidden group">
                                    <div className="absolute inset-0 bg-teal-500/10 animate-pulse" />
                                    <Mic size={24} className="relative z-10" />
                                </div>
                                <div>
                                    <h5 className="text-sm font-black text-white uppercase tracking-widest mb-2">Voice-Lock Active</h5>
                                    <p className="text-xs text-slate-400 leading-relaxed font-medium">
                                        Perubahan pada keamanan akan memerlukan verifikasi suara tambahan jika Anda mengaktifkan perlindungan ketat dari dashboard.
                                    </p>
                                </div>
                            </motion.div>

                            <div className="space-y-3">
                                <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi Saat Ini</label>
                                <input 
                                    type="password"
                                    value={data.current_password}
                                    onChange={e => setData('current_password', e.target.value)}
                                    className="w-full h-14 px-6 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                                    placeholder="Wajib diisi untuk proses pembaruan"
                                />
                                {errors.current_password && <p className="text-rose-500 text-[10px] font-black mt-2 ml-1 uppercase tracking-widest">{errors.current_password}</p>}
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div className="space-y-3">
                                    <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi Baru</label>
                                    <input 
                                        type="password"
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        className="w-full h-14 px-6 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                                        placeholder="Minimal 8 karakter"
                                    />
                                    {errors.password && <p className="text-rose-500 text-[10px] font-black mt-2 ml-1 uppercase tracking-widest">{errors.password}</p>}
                                </div>
                                <div className="space-y-3">
                                    <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Sandi Baru</label>
                                    <input 
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={e => setData('password_confirmation', e.target.value)}
                                        className="w-full h-14 px-6 bg-white border-0 rounded-2xl shadow-inner focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                                        placeholder="Ulangi sandi baru"
                                    />
                                </div>
                            </div>
                        </div>
                    </SettingsSection>

                    {/* Save Changes Floating Bar */}
                    <motion.div 
                        variants={{
                            hidden: { opacity: 0, y: 20 },
                            visible: { opacity: 1, y: 0 }
                        }}
                        className="flex items-center justify-between bg-white/40 backdrop-blur-xl p-8 rounded-[48px] border border-white/40 shadow-sm"
                    >
                        <div className="flex items-center gap-3">
                            <AnimatePresence>
                                {recentlySuccessful && (
                                    <motion.p 
                                        initial={{ opacity: 0, scale: 0.9, x: -10 }}
                                        animate={{ opacity: 1, scale: 1, x: 0 }}
                                        exit={{ opacity: 0, x: 10 }}
                                        className="text-teal-600 font-black text-[10px] uppercase tracking-[0.2em] flex items-center gap-2 bg-teal-500/10 px-6 py-3 rounded-full border border-teal-500/10 shadow-sm"
                                    >
                                        <CheckCircle2 size={16} />
                                        Data berhasil diperbarui
                                    </motion.p>
                                )}
                            </AnimatePresence>
                        </div>
                        <motion.button
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                            type="submit"
                            disabled={processing}
                            className="h-16 px-12 bg-teal-600 text-white rounded-2xl font-black text-sm uppercase tracking-[0.2em] flex items-center justify-center gap-3 hover:bg-teal-700 transition-all shadow-2xl shadow-teal-600/40 disabled:opacity-50 group overflow-hidden relative"
                        >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite]" />
                            <Save size={20} className="relative z-10" />
                            <span className="relative z-10">{processing ? 'Memproses...' : 'Simpan Perubahan'}</span>
                        </motion.button>
                    </motion.div>

                    {/* Logout Section */}
                    <motion.div 
                        variants={{
                            hidden: { opacity: 0, y: 20 },
                            visible: { opacity: 1, y: 0 }
                        }}
                        className="mt-12 mb-24 lg:hidden"
                    >
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button"
                            className="w-full h-20 bg-rose-500/10 border border-rose-500/20 text-rose-600 rounded-[32px] font-black text-sm uppercase tracking-[0.2em] flex items-center justify-center gap-4 hover:bg-rose-500/20 transition-all"
                        >
                            <LogOut size={24} />
                            Keluar dari Sesi
                        </Link>
                    </motion.div>
                    </motion.div>
                </form>
            </div>
        </AuthLayout>
    );
}
