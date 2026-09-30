# My AstroHub

My AstroHub is the local personalisation layer. It stores explicit user choices separately from learned preference signals.

## Profiles

A local installation starts with the `local` profile. Profiles contain language and information-depth preferences. No cloud account is required.

## Workspaces

A profile can own any number of workspaces. Initial types include:

- `observation` — My Beobachtung
- `photo` — My Foto
- `meteors` — My Meteore
- `science` — My Science
- `custom` — community/user workflows

Workspaces are intentionally independent of interests: a user may select many interests and create several workspaces.

## Locations

A profile can store multiple observing locations with coordinates, optional elevation/timezone and a default flag. Location privacy defaults to `private`.

Precise coordinates must not be published or sent to unrelated providers automatically. Provider requests should receive only the location data necessary for the requested capability.

## Personal Astronomy Engine

The first engine foundation stores weighted local signals such as object views, searches, observations and explicit favourites. These signals can later rank relevant suggestions.

The engine is deliberately user-controlled:

1. Explicit interests remain authoritative.
2. Learned signals do not silently rewrite interests.
3. Recommendations should explain their reason when possible.
4. Signals remain local/self-hosted by default.
5. Users must be able to clear learned signals later.

The current API returns aggregated signal scores but does not yet generate astronomy recommendations. Recommendation logic will be added when Sky/Conditions data exists.

## API foundation

- `GET /api/v1/my-astrohub?profile=local`
- `POST /api/v1/my-astrohub/profile`
- `POST /api/v1/my-astrohub/workspaces`
- `POST /api/v1/my-astrohub/locations`
- `POST /api/v1/my-astrohub/signals`
