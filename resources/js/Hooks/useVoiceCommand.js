import { useState, useRef, useCallback, useEffect } from 'react';
import { router } from '@inertiajs/react';
import axios from 'axios';

/**
 * Unified Voice Command Hook
 * Single voice button that routes intent to either:
 *   - Page navigation ("buka dashboard", "open budget")
 *   - Transaction creation ("pengeluaran makan 50 ribu")
 */

// ─── Navigation Routes ───────────────────────────────────────────────
const NAVIGATION_ROUTES = [
    { path: '/dashboard', label: 'Dashboard', keywords: ['dashboard', 'dasbor', 'beranda', 'home', 'imah'] },
    { path: '/budgeting', label: 'Anggaran',  keywords: ['anggaran', 'budget', 'budgeting', 'keuangan', 'finance', 'duit', 'artos'] },
    { path: '/goals',     label: 'Target',    keywords: ['target', 'goals', 'goal', 'tujuan', 'sasaran', 'udagan'] },
    { path: '/laporan',   label: 'Laporan',   keywords: ['laporan', 'report', 'reports', 'statistik', 'statistics'] },
    { path: '/settings',  label: 'Pengaturan',keywords: ['pengaturan', 'settings', 'setting', 'setelan', 'profil', 'profile', 'pangaturan'] },
];

// Prefixes that indicate navigation intent
const NAV_PREFIXES = [
    // Indonesian
    'buka', 'ke', 'pergi ke', 'tampilkan', 'lihat',
    // English
    'open', 'go to', 'navigate to', 'show', 'take me to',
    // Sunda Priangan
    'ka', 'tempo', 'buka', 'tingali','lebet','asup'
];

// ─── Intent Classifier ──────────────────────────────────────────────
function classifyIntent(transcript) {
    const text = transcript.toLowerCase().trim();

    // Check if it starts with a navigation prefix
    for (const prefix of NAV_PREFIXES) {
        if (text.startsWith(prefix + ' ')) {
            const target = text.slice(prefix.length).trim();
            for (const route of NAVIGATION_ROUTES) {
                for (const keyword of route.keywords) {
                    if (target.includes(keyword)) {
                        return { type: 'navigate', path: route.path, label: route.label };
                    }
                }
            }
            // Has navigation prefix but unrecognized target
            return { type: 'unknown', text: transcript };
        }
    }

    // No navigation prefix → treat as transaction
    return { type: 'transaction', text: transcript };
}

// ─── Hook ────────────────────────────────────────────────────────────
export function useVoiceCommand({ onTransactionParsed, onTransactionError } = {}) {
    const [isListening, setIsListening] = useState(false);
    const [transcript, setTranscript] = useState('');
    const [feedback, setFeedback] = useState(null);
    const [isSupported, setIsSupported] = useState(true);
    const [isProcessing, setIsProcessing] = useState(false);
    const recognitionRef = useRef(null);
    const feedbackTimerRef = useRef(null);

    useEffect(() => {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) setIsSupported(false);
    }, []);

    const showFeedback = useCallback((type, message, duration = 3000) => {
        if (feedbackTimerRef.current) clearTimeout(feedbackTimerRef.current);
        setFeedback({ type, message });
        if (duration > 0) {
            feedbackTimerRef.current = setTimeout(() => setFeedback(null), duration);
        }
    }, []);

    const clearFeedback = useCallback(() => {
        if (feedbackTimerRef.current) clearTimeout(feedbackTimerRef.current);
        setFeedback(null);
    }, []);

    // ── Handle a final transcript ────────────────────────────────────
    const handleFinalTranscript = useCallback(async (text) => {
        const intent = classifyIntent(text);

        switch (intent.type) {
            case 'navigate':
                showFeedback('success', `Membuka ${intent.label}...`);
                setTimeout(() => router.visit(intent.path), 600);
                break;

            case 'transaction':
                showFeedback('processing', 'Memproses transaksi...', 0);
                setIsProcessing(true);
                try {
                    const res = await axios.post('/api/parse-voice-text', { text: intent.text });
                    if (res.data.success) {
                        showFeedback('success', 'Transaksi terdeteksi!');
                        onTransactionParsed?.(res.data.data);
                    } else {
                        showFeedback('error', res.data.message || 'Gagal memproses transaksi.');
                        onTransactionError?.(res.data.message);
                    }
                } catch (err) {
                    console.error('Voice transaction parse error:', err);
                    showFeedback('error', 'Gagal memproses perintah suara.');
                    onTransactionError?.(err.message);
                } finally {
                    setIsProcessing(false);
                }
                break;

            default:
                showFeedback('error', `"${text}" — Perintah tidak dikenali.`);
        }
    }, [showFeedback, onTransactionParsed, onTransactionError]);

    // ── Stop ─────────────────────────────────────────────────────────
    const stopListening = useCallback(() => {
        if (recognitionRef.current) {
            recognitionRef.current.abort();
            recognitionRef.current = null;
        }
        setIsListening(false);
    }, []);

    // ── Start ────────────────────────────────────────────────────────
    const startListening = useCallback(() => {
        if (!isSupported) {
            showFeedback('error', 'Browser tidak mendukung perintah suara.');
            return;
        }
        if (isListening) { stopListening(); return; }

        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SR();
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.maxAlternatives = 3;

        recognition.onstart = () => {
            setIsListening(true);
            setTranscript('');
            showFeedback('listening', 'Mendengarkan... Ucapkan perintah.', 0);
        };

        recognition.onresult = (event) => {
            let interim = '';
            let final = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                if (event.results[i].isFinal) final += event.results[i][0].transcript;
                else interim += event.results[i][0].transcript;
            }
            if (interim) setTranscript(interim);
            if (final) {
                setTranscript(final);
                handleFinalTranscript(final);
            }
        };

        recognition.onerror = (event) => {
            setIsListening(false);
            recognitionRef.current = null;
            const msgs = {
                'not-allowed': 'Izin mikrofon ditolak. Aktifkan di pengaturan browser.',
                'no-speech': 'Tidak ada suara terdeteksi. Coba lagi.',
                'network': 'Koneksi internet diperlukan untuk pengenalan suara.',
            };
            showFeedback('error', msgs[event.error] || 'Terjadi kesalahan. Coba lagi.');
        };

        recognition.onend = () => {
            setIsListening(false);
            recognitionRef.current = null;
        };

        recognitionRef.current = recognition;
        recognition.start();
    }, [isSupported, isListening, stopListening, showFeedback, handleFinalTranscript]);

    // Cleanup
    useEffect(() => {
        return () => {
            recognitionRef.current?.abort();
            if (feedbackTimerRef.current) clearTimeout(feedbackTimerRef.current);
        };
    }, []);

    return {
        isListening,
        isSupported,
        isProcessing,
        transcript,
        feedback,
        startListening,
        stopListening,
        clearFeedback,
    };
}
