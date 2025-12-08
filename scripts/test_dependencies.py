#!/usr/bin/env python3
"""
Test script untuk memastikan semua Python dependencies terinstall dengan benar
Jalankan di server cPanel sebelum deploy aplikasi
"""

import sys

print("=" * 60)
print("🔍 VOICA - Python Dependencies Checker")
print("=" * 60)
print()

# Check Python version
print("1. Python Version:")
print(f"   ✅ {sys.version}")
print()

# Check required libraries
required_libs = {
    'numpy': 'Numerical computing',
    'scipy': 'Scientific computing',
    'librosa': 'Audio processing',
}

print("2. Required Libraries:")
all_ok = True

for lib, desc in required_libs.items():
    try:
        module = __import__(lib)
        version = getattr(module, '__version__', 'unknown')
        print(f"   ✅ {lib:12} v{version:10} - {desc}")
    except ImportError as e:
        print(f"   ❌ {lib:12} NOT FOUND - {desc}")
        print(f"      Install: pip3 install --user {lib}")
        all_ok = False

print()

# Check voice_processor.py
print("3. Voice Processor Script:")
try:
    import voice_processor
    print("   ✅ voice_processor.py found and importable")
except ImportError:
    print("   ❌ voice_processor.py NOT FOUND")
    print("      Make sure voice_processor.py is in the same directory")
    all_ok = False

print()

# Final result
print("=" * 60)
if all_ok:
    print("✅ ALL CHECKS PASSED!")
    print("   Voice authentication ready to use.")
else:
    print("❌ SOME CHECKS FAILED!")
    print("   Please install missing dependencies:")
    print("   pip3 install --user numpy scipy librosa")
print("=" * 60)

sys.exit(0 if all_ok else 1)
