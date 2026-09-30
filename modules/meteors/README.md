# AstroHub Meteors

The first field workflow is a local-first one-tap meteor counter.

## Capture rules

Every tap creates its own UUID and records both an ISO UTC timestamp and Unix milliseconds. The original timestamp is immutable scientific source data; later classification (magnitude, shower, notes) is separate metadata.

The field UI stores events in browser local storage immediately, so a temporary network or Core outage does not stop counting. Server persistence uses `INSERT OR IGNORE` with the event UUID, allowing a client to retry uploads without intentionally duplicating the same observation.

## Input

The reference field counter supports touch and the Space key. The Core data model also records the input method so future Arduino/ESP32 buttons can submit events as `device` input.

## API

- `POST /api/v1/meteors/sessions`
- `GET /api/v1/meteors/sessions/{id}`
- `POST /api/v1/meteors/sessions/{id}/events`
- `POST /api/v1/meteors/sessions/{id}/end`
- `GET /api/v1/meteors/sessions/{id}/export/csv`
- `GET /api/v1/meteors/sessions/{id}/export/json`

## Next steps

The PWA will gain an IndexedDB sync queue, session controls, later event classification and PDF reports. Device API support will allow a physical button to use the same event model.
