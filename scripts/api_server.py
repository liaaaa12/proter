import sys
import os
import traceback
from contextlib import asynccontextmanager
from typing import List, Optional, Dict, Any

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn

# Ensure the script directory is in path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Import the actual processing functions
from voice_processor_ecapa import (
    get_verifier,
    enroll_voice,
    verify_voice,
    verify_secure,
    verify_secure_with_challenge,
    transcribe_audio,
    compare_files,
    DEVICE
)

# Optional: Load anti_spoofing detector at startup as well
from anti_spoofing import get_detector


@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup: Load the heavy ML models into RAM/VRAM
    print(f"Loading models on {DEVICE}...", file=sys.stderr)
    try:
        # Pre-load ECAPA-TDNN model
        get_verifier()
        # Pre-load AASIST Anti-Spoofing model
        get_detector()
        print("✅ Models loaded successfully into memory!", file=sys.stderr)
    except Exception as e:
        print(f"❌ Failed to load models: {str(e)}", file=sys.stderr)
        traceback.print_exc()
        
    yield
    
    # Shutdown
    print("Shutting down AI service...", file=sys.stderr)


app = FastAPI(
    title="Voica AI Processing Service", 
    description="Microservice for Fast Voice Enrollment & Verification",
    lifespan=lifespan
)

# --- Request Models ---

class EnrollRequest(BaseModel):
    audio_path: str

class VerifyRequest(BaseModel):
    test_audio_path: str
    enrolled_embedding: List[float]
    threshold: float = 0.25

class VerifySecureRequest(BaseModel):
    test_audio_path: str
    enrolled_embedding: List[float]
    threshold: float = 0.70

class VerifyChallengeRequest(BaseModel):
    test_audio_path: str
    enrolled_embedding: List[float]
    expected_text: str
    threshold: float = 0.70

class TranscribeRequest(BaseModel):
    audio_path: str
    language: str = 'id-ID'


# --- Endpoints ---

@app.get("/")
def health_check():
    return {
        "status": "online", 
        "device": DEVICE,
        "message": "Models are loaded in RAM and ready for instant inference."
    }

@app.post("/enroll")
def enroll(req: EnrollRequest):
    if not os.path.exists(req.audio_path):
        raise HTTPException(status_code=400, detail="Audio file not found")
        
    result = enroll_voice(req.audio_path)
    if not result.get('success', False):
        raise HTTPException(status_code=500, detail=result)
        
    return result

@app.post("/verify")
def verify(req: VerifyRequest):
    if not os.path.exists(req.test_audio_path):
        raise HTTPException(status_code=400, detail="Test audio file not found")
        
    result = verify_voice(req.test_audio_path, req.enrolled_embedding, req.threshold)
    return result

@app.post("/verify-secure")
def verify_secure_endpoint(req: VerifySecureRequest):
    if not os.path.exists(req.test_audio_path):
        raise HTTPException(status_code=400, detail="Test audio file not found")
        
    result = verify_secure(req.test_audio_path, req.enrolled_embedding, req.threshold)
    return result

@app.post("/verify-with-challenge")
def verify_with_challenge_endpoint(req: VerifyChallengeRequest):
    if not os.path.exists(req.test_audio_path):
        raise HTTPException(status_code=400, detail="Test audio file not found")
        
    result = verify_secure_with_challenge(
        req.test_audio_path, 
        req.enrolled_embedding, 
        req.expected_text, 
        req.threshold
    )
    return result

@app.post("/transcribe")
def transcribe_endpoint(req: TranscribeRequest):
    if not os.path.exists(req.audio_path):
        raise HTTPException(status_code=400, detail="Audio file not found")
        
    result = transcribe_audio(req.audio_path, req.language)
    return result

if __name__ == "__main__":
    # Allows running script directly with 'python api_server.py'
    uvicorn.run("api_server:app", host="127.0.0.1", port=8000, reload=True)
