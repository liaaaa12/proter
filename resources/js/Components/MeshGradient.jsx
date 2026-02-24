import React from 'react';
import { motion } from 'framer-motion';

const MeshGradient = () => {
    return (
        <div className="fixed inset-0 -z-10 overflow-hidden pointer-events-none bg-slate-50">
            {/* Organic Blurry Orbs */}
            <motion.div
                animate={{
                    x: [0, 100, 0],
                    y: [0, 50, 0],
                    scale: [1, 1.2, 1],
                }}
                transition={{
                    duration: 20,
                    repeat: Infinity,
                    ease: "linear"
                }}
                className="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-teal-200/30 rounded-full blur-[120px]"
            />
            <motion.div
                animate={{
                    x: [0, -80, 0],
                    y: [0, 100, 0],
                    scale: [1.2, 1, 1.2],
                }}
                transition={{
                    duration: 25,
                    repeat: Infinity,
                    ease: "linear"
                }}
                className="absolute top-[20%] -right-[10%] w-[60%] h-[60%] bg-blue-200/20 rounded-full blur-[150px]"
            />
            <motion.div
                animate={{
                    x: [0, 50, 0],
                    y: [0, -100, 0],
                    scale: [1, 1.1, 1],
                }}
                transition={{
                    duration: 18,
                    repeat: Infinity,
                    ease: "linear"
                }}
                className="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] bg-slate-200/40 rounded-full blur-[100px]"
            />
            
            {/* Noise Texture Overlay */}
            <div className="absolute inset-0 opacity-[0.03] mix-blend-overlay pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]" />
        </div>
    );
};

export default MeshGradient;
