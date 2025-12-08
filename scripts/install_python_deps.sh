#!/bin/bash
# Script untuk install Python dependencies di cPanel
# Jalankan via SSH: bash install_python_deps.sh

echo "=========================================="
echo "🚀 Voica - Python Dependencies Installer"
echo "=========================================="
echo ""

# Detect Python
if command -v python3 &> /dev/null; then
    PYTHON_CMD="python3"
    echo "✅ Python3 found: $(which python3)"
elif command -v python &> /dev/null; then
    PYTHON_CMD="python"
    echo "✅ Python found: $(which python)"
else
    echo "❌ Python not found!"
    echo "Please contact your cPanel admin to install Python 3"
    exit 1
fi

echo "Python version: $($PYTHON_CMD --version)"
echo ""

# Detect pip
if command -v pip3 &> /dev/null; then
    PIP_CMD="pip3"
    echo "✅ pip3 found: $(which pip3)"
elif command -v pip &> /dev/null; then
    PIP_CMD="pip"
    echo "✅ pip found: $(which pip)"
else
    echo "⚠️  pip not found, trying python -m pip"
    PIP_CMD="$PYTHON_CMD -m pip"
fi

echo ""
echo "=========================================="
echo "📦 Installing Python libraries..."
echo "=========================================="
echo ""

# Install libraries
echo "Installing numpy..."
$PIP_CMD install --user numpy

echo ""
echo "Installing scipy..."
$PIP_CMD install --user scipy

echo ""
echo "Installing librosa..."
$PIP_CMD install --user librosa

echo ""
echo "=========================================="
echo "✅ Installation Complete!"
echo "=========================================="
echo ""

# Verify installation
echo "Verifying installation..."
echo ""

$PYTHON_CMD -c "import numpy; print('✅ numpy:', numpy.__version__)"
$PYTHON_CMD -c "import scipy; print('✅ scipy:', scipy.__version__)"
$PYTHON_CMD -c "import librosa; print('✅ librosa:', librosa.__version__)"

echo ""
echo "=========================================="
echo "🎉 All dependencies installed successfully!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Test voice_processor.py:"
echo "   python3 scripts/voice_processor.py enroll /path/to/test.wav"
echo ""
echo "2. Update .env with Python path:"
echo "   PYTHON_PATH=$(which $PYTHON_CMD)"
echo ""
