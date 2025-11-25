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
        # Load audio file
        y, sr = librosa.load(audio_path, duration=duration, sr=16000)
        
        # Extract MFCC features
        mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
        
        # Calculate statistics (mean and std for each coefficient)
        mfcc_mean = np.mean(mfcc, axis=1)
        mfcc_std = np.std(mfcc, axis=1)
        
        # Combine mean and std
        features = np.concatenate([mfcc_mean, mfcc_std])
        
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
    features = extract_mfcc_features(audio_path, duration=10)
    
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
    # Extract features from test audio
    test_features = extract_mfcc_features(test_audio_path, duration=10)
    
    # Compare features
    similarity = compare_voices(enrolled_features, test_features)
    
    # Threshold for authentication (80%)
    is_match = similarity >= 80.0
    
    return {
        'success': True,
        'similarity': round(similarity, 2),
        'is_match': is_match,
        'threshold': 80.0
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
