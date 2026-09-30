from datetime import datetime, timezone
from math import acos, cos, radians, sin
from typing import Optional

from fastapi import FastAPI, Query
from pydantic import BaseModel, Field

from .sky import VisibilityRequest, search_catalog, visible_objects

app = FastAPI(title="AstroHub Science", version="0.1.0-alpha")


class AngularSeparationRequest(BaseModel):
    ra1_deg: float = Field(ge=0, lt=360)
    dec1_deg: float = Field(ge=-90, le=90)
    ra2_deg: float = Field(ge=0, lt=360)
    dec2_deg: float = Field(ge=-90, le=90)


@app.get("/health")
def health() -> dict:
    return {"status":"ok","service":"astrohub-science","version":"0.1.0-alpha","time_utc":datetime.now(timezone.utc).isoformat()}


@app.post("/v1/coordinates/angular-separation")
def angular_separation(payload: AngularSeparationRequest) -> dict:
    ra1, dec1 = radians(payload.ra1_deg), radians(payload.dec1_deg)
    ra2, dec2 = radians(payload.ra2_deg), radians(payload.dec2_deg)
    cosine = sin(dec1)*sin(dec2)+cos(dec1)*cos(dec2)*cos(ra1-ra2)
    cosine = min(1.0,max(-1.0,cosine))
    return {"separation_deg":acos(cosine)*180.0/3.141592653589793,"reference_frame":"ICRS-compatible input assumption"}


@app.get("/v1/sky/search")
def sky_search(q: str = Query(default="", max_length=100), object_type: Optional[str] = None, max_magnitude: Optional[float] = None) -> dict:
    objects = search_catalog(q, object_type, max_magnitude)
    return {"query":q,"count":len(objects),"objects":objects,"catalog":"AstroHub Basic Sky v0.1"}


@app.post("/v1/sky/visible")
def sky_visible(payload: VisibilityRequest) -> dict:
    return visible_objects(payload)
