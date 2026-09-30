from datetime import datetime, timezone
from math import acos, cos, radians, sin

from fastapi import FastAPI
from pydantic import BaseModel, Field

app = FastAPI(title="AstroHub Science", version="0.1.0-alpha")


class AngularSeparationRequest(BaseModel):
    ra1_deg: float = Field(ge=0, lt=360)
    dec1_deg: float = Field(ge=-90, le=90)
    ra2_deg: float = Field(ge=0, lt=360)
    dec2_deg: float = Field(ge=-90, le=90)


@app.get("/health")
def health() -> dict:
    return {
        "status": "ok",
        "service": "astrohub-science",
        "version": "0.1.0-alpha",
        "time_utc": datetime.now(timezone.utc).isoformat(),
    }


@app.post("/v1/coordinates/angular-separation")
def angular_separation(payload: AngularSeparationRequest) -> dict:
    ra1, dec1 = radians(payload.ra1_deg), radians(payload.dec1_deg)
    ra2, dec2 = radians(payload.ra2_deg), radians(payload.dec2_deg)
    cosine = sin(dec1) * sin(dec2) + cos(dec1) * cos(dec2) * cos(ra1 - ra2)
    cosine = min(1.0, max(-1.0, cosine))

    return {
        "separation_deg": acos(cosine) * 180.0 / 3.141592653589793,
        "reference_frame": "ICRS-compatible input assumption",
    }
