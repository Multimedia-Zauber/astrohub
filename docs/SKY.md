# AstroHub Sky

Sky is AstroHub's local-first object discovery and visibility layer.

## Basic Sky v0.1

The first embedded catalogue is deliberately small and validates the architecture with bright/popular targets such as M31, M42, M45, M13, M57, Sirius, Vega and Polaris. It is not intended to replace a full astronomical catalogue.

The next Data Pack can expand this to Messier, NGC/IC, bright-star and Solar System datasets without changing the search API.

## Search

`GET /v1/sky/search?q=M31`

Search covers object IDs, common names, aliases and constellations. Optional filters support object type and maximum magnitude. Because the basic catalogue is local, search remains available without internet access.

## Visibility

`POST /v1/sky/visible`

Example body:

```json
{
  "latitude": 46.95,
  "longitude": 7.44,
  "min_altitude_deg": 20,
  "max_magnitude": 8
}
```

The Science service converts catalogue equatorial coordinates to local altitude/azimuth for the supplied observer position and UTC time. Results retain the calculation identifier so derived recommendations can later explain how they were produced.

## Intelligent search direction

Future ranking combines objective visibility with explicit user context:

- current/specified observing location
- date and time
- altitude and transit window
- brightness
- Moon separation/illumination
- weather/seeing/transparency
- telescope/camera field of view
- workspace and explicit interests
- learned Personal Astronomy Engine signals

The raw astronomical values remain visible. AstroHub should explain recommendations rather than returning an opaque AI score.
