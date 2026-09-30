from datetime import datetime, timezone
from math import acos, asin, atan2, cos, degrees, floor, radians, sin
from typing import Optional

from pydantic import BaseModel, Field

CATALOG = [
    {"id":"M31","name":"Andromedagalaxie","aliases":["Andromeda Galaxy","NGC 224"],"type":"galaxy","ra_deg":10.6847,"dec_deg":41.2690,"magnitude":3.44,"constellation":"Andromeda"},
    {"id":"M42","name":"Orionnebel","aliases":["Orion Nebula","NGC 1976"],"type":"nebula","ra_deg":83.8221,"dec_deg":-5.3911,"magnitude":4.0,"constellation":"Orion"},
    {"id":"M45","name":"Plejaden","aliases":["Pleiades","Seven Sisters"],"type":"open_cluster","ra_deg":56.75,"dec_deg":24.1167,"magnitude":1.6,"constellation":"Taurus"},
    {"id":"M13","name":"Herkuleshaufen","aliases":["Hercules Globular Cluster","NGC 6205"],"type":"globular_cluster","ra_deg":250.4235,"dec_deg":36.4613,"magnitude":5.8,"constellation":"Hercules"},
    {"id":"M57","name":"Ringnebel","aliases":["Ring Nebula","NGC 6720"],"type":"planetary_nebula","ra_deg":283.3962,"dec_deg":33.0292,"magnitude":8.8,"constellation":"Lyra"},
    {"id":"SIRIUS","name":"Sirius","aliases":["Alpha Canis Majoris"],"type":"star","ra_deg":101.2872,"dec_deg":-16.7161,"magnitude":-1.46,"constellation":"Canis Major"},
    {"id":"VEGA","name":"Vega","aliases":["Alpha Lyrae"],"type":"star","ra_deg":279.2347,"dec_deg":38.7837,"magnitude":0.03,"constellation":"Lyra"},
    {"id":"POLARIS","name":"Polarstern","aliases":["Polaris","Alpha Ursae Minoris"],"type":"star","ra_deg":37.9546,"dec_deg":89.2641,"magnitude":1.98,"constellation":"Ursa Minor"}
]

class VisibilityRequest(BaseModel):
    latitude: float = Field(ge=-90, le=90)
    longitude: float = Field(ge=-180, le=180)
    at: Optional[datetime] = None
    min_altitude_deg: float = Field(default=15, ge=-90, le=90)
    max_magnitude: Optional[float] = None
    object_type: Optional[str] = None


def julian_date(dt: datetime) -> float:
    dt = dt.astimezone(timezone.utc)
    year, month = dt.year, dt.month
    day = dt.day + (dt.hour + (dt.minute + (dt.second + dt.microsecond / 1e6) / 60) / 60) / 24
    if month <= 2:
        year -= 1; month += 12
    a = floor(year / 100); b = 2 - a + floor(a / 4)
    return floor(365.25 * (year + 4716)) + floor(30.6001 * (month + 1)) + day + b - 1524.5


def altitude_azimuth(ra_deg: float, dec_deg: float, lat_deg: float, lon_deg: float, dt: datetime) -> tuple[float, float]:
    jd = julian_date(dt)
    t = (jd - 2451545.0) / 36525.0
    gmst = 280.46061837 + 360.98564736629 * (jd - 2451545.0) + 0.000387933 * t*t - t*t*t/38710000
    lst = (gmst + lon_deg) % 360
    ha = radians((lst - ra_deg) % 360)
    if ha > 3.141592653589793: ha -= 2 * 3.141592653589793
    dec, lat = radians(dec_deg), radians(lat_deg)
    alt = asin(sin(dec)*sin(lat) + cos(dec)*cos(lat)*cos(ha))
    az = atan2(-sin(ha)*cos(dec), sin(dec)*cos(lat)-cos(dec)*sin(lat)*cos(ha))
    return degrees(alt), degrees(az) % 360


def search_catalog(query: str = "", object_type: Optional[str] = None, max_magnitude: Optional[float] = None) -> list[dict]:
    needle = query.casefold().strip()
    result = []
    for obj in CATALOG:
        searchable = [obj["id"], obj["name"], *obj["aliases"], obj["constellation"]]
        if needle and not any(needle in value.casefold() for value in searchable): continue
        if object_type and obj["type"] != object_type: continue
        if max_magnitude is not None and obj["magnitude"] > max_magnitude: continue
        result.append(obj)
    return sorted(result, key=lambda item: item["magnitude"])


def visible_objects(payload: VisibilityRequest) -> dict:
    at = payload.at or datetime.now(timezone.utc)
    if at.tzinfo is None: at = at.replace(tzinfo=timezone.utc)
    objects = []
    for obj in search_catalog(object_type=payload.object_type, max_magnitude=payload.max_magnitude):
        altitude, azimuth = altitude_azimuth(obj["ra_deg"], obj["dec_deg"], payload.latitude, payload.longitude, at)
        if altitude >= payload.min_altitude_deg:
            objects.append({**obj,"altitude_deg":round(altitude,2),"azimuth_deg":round(azimuth,2),"visible":True})
    objects.sort(key=lambda item: (-item["altitude_deg"], item["magnitude"]))
    return {"at_utc":at.astimezone(timezone.utc).isoformat(),"location":{"latitude":payload.latitude,"longitude":payload.longitude},"min_altitude_deg":payload.min_altitude_deg,"objects":objects,"calculation":"AstroHub Science sidereal-time/AltAz v0.1"}
