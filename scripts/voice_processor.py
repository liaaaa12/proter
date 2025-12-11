#!/usr/bin/env python3
"""
Voice Recognition Processor for Laravel
Extracts MFCC features and compares voice samples
"""

import sys
import json
import numpy as np
import librosa
from scipy.spatial.distance import cosine
from scipy.stats import pearsonr

def extract_mfcc_features(audio_path, n_mfcc=13, duration=None):
    """
    Extract MFCC features from audio file
    
    Args:
        audio_path: Path to audio file
        n_mfcc: Number of MFCC coefficients
        duration: Max duration to process (seconds)
    
    Returns:
        numpy array of MFCC features (averaged)
    """
    try:
        # Load audio file - 5 detik optimal untuk keamanan & kecepatan
        y, sr = librosa.load(audio_path, duration=duration, sr=16000)
        
        # 1. Extract MFCC features (karakteristik spektral)
        mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
        mfcc_mean = np.mean(mfcc, axis=1)
        mfcc_std = np.std(mfcc, axis=1)
        
        # 2. Extract Pitch (F0) - karakteristik fundamental frequency pita suara
        pitches, magnitudes = librosa.piptrack(y=y, sr=sr)
        pitch_mean = np.mean(pitches[pitches > 0]) if np.any(pitches > 0) else 0
        pitch_std = np.std(pitches[pitches > 0]) if np.any(pitches > 0) else 0
        
        # 3. Extract Spectral Contrast - perbedaan energi antar frekuensi
        spectral_contrast = librosa.feature.spectral_contrast(y=y, sr=sr)
        contrast_mean = np.mean(spectral_contrast, axis=1)
        contrast_std = np.std(spectral_contrast, axis=1)
        
        # 4. Extract Zero Crossing Rate - karakteristik perubahan sinyal
        zcr = librosa.feature.zero_crossing_rate(y)
        zcr_mean = np.mean(zcr)
        zcr_std = np.std(zcr)
        
        # Combine all features untuk fingerprint yang lebih unik
        features = np.concatenate([
            mfcc_mean, mfcc_std,           # 26 features
            [pitch_mean, pitch_std],       # 2 features
            contrast_mean, contrast_std,   # 14 features
            [zcr_mean, zcr_std]           # 2 features
        ])
        
        return features.tolist()
    
    except Exception as e:
        print(json.dumps({
            'success': False,
            'error': f'Error extracting features: {str(e)}'
        }))
        sys.exit(1)

def compare_voices(features1, features2):
    """
    Compare two voice samples using cosine similarity and correlation
    
    Args:
        features1: First voice features (list)
        features2: Second voice features (list)
    
    Returns:
        Similarity score (0-100)
    """
    try:
        f1 = np.array(features1)
        f2 = np.array(features2)
        
        # Cosine similarity (0 = different, 1 = same)
        cosine_sim = 1 - cosine(f1, f2)
        
        # Pearson correlation
        pearson_corr, _ = pearsonr(f1, f2)
        
        # Combine both metrics (weighted average)
        similarity = (cosine_sim * 0.6 + pearson_corr * 0.4)
        
        # Convert to percentage (0-100)
        similarity_percentage = max(0, min(100, similarity * 100))
        
        return similarity_percentage
    
    except Exception as e:
        return 0.0

def enroll_voice(audio_path):
    """
    Enroll a new voice sample
    
    Args:
        audio_path: Path to audio file
    
    Returns:
        JSON with features
    """
    # Durasi 5 detik - optimal untuk keamanan & kecepatan
    features = extract_mfcc_features(audio_path, duration=5)
    
    return {
        'success': True,
        'voice_path': audio_path,
        'features': features,
        'feature_count': len(features)
    }

def verify_voice(test_audio_path, enrolled_features):
    """
    Verify a voice against enrolled features
    
    Args:
        test_audio_path: Path to test audio
        enrolled_features: Previously enrolled features (list)
    
    Returns:
        JSON with similarity score
    """
    # Extract features from test audio - 5 detik (sama dengan enrollment)
    test_features = extract_mfcc_features(test_audio_path, duration=5)
    
    # Compare features
    similarity = compare_voices(enrolled_features, test_features)
    
    # Threshold dinaikkan ke 97% untuk keamanan lebih ketat
    is_match = bool(similarity >= 97.0)
    
    return {
        'success': True,
        'similarity': round(similarity, 2),
        'is_match': is_match,
        'threshold': 97.0
    }

def main():
    """
    Main CLI interface
    Usage:
        python voice_processor.py enroll <audio_path>
        python voice_processor.py verify <test_audio_path> <enrolled_features_json>
    """
    if len(sys.argv) < 2:
        print(json.dumps({
            'success': False,
            'error': 'Invalid arguments. Usage: voice_processor.py [enroll|verify] <args>'
        }))
        sys.exit(1)
    
    command = sys.argv[1]
    
    try:
        if command == 'enroll':
            if len(sys.argv) < 3:
                raise ValueError('Audio path required')
            
            audio_path = sys.argv[2]
            result = enroll_voice(audio_path)
            print(json.dumps(result))
        
        elif command == 'verify':
            if len(sys.argv) < 4:
                raise ValueError('Test audio path and enrolled features required')
            
            test_audio_path = sys.argv[2]
            enrolled_features_json = sys.argv[3]
            
            # Parse enrolled features
            enrolled_features = json.loads(enrolled_features_json)
            
            result = verify_voice(test_audio_path, enrolled_features)
            print(json.dumps(result))
        
        else:
            raise ValueError(f'Unknown command: {command}')
    
    except Exception as e:
        print(json.dumps({
            'success': False,
            'error': str(e)
        }))
        sys.exit(1)

if __name__ == '__main__':
    main()
