# AstroHub Architecture 1.0

## Architectural goals

AstroHub is modular, local-first, offline-capable and extensible. Astronomy features should not depend directly on a specific external provider, and user observations must remain usable when external services are unavailable.

## Logical layers

```text
Desktop / PWA / Browser
        |
   TypeScript UI
        |
    AstroHub API
        |
  PHP Application Core <----> Python Science Service
        |
 SQLite / MariaDB
        |
 Providers / Modules / Plugins / Device API
```

## Core responsibilities

Authentication, profiles, workspaces, permissions, settings, API, database abstraction, events, scheduler, sync, notifications, providers, modules/plugins, devices and updates.

The Core should not contain domain knowledge such as Perseid shower behaviour. That belongs in astronomy modules or the Science layer.

## Science service

Python handles calculations and scientific workflows such as coordinates, ephemerides, visibility, photometry, meteor calculations, minor planets and later spectroscopy/radio analysis. The service should bind locally by default and expose a versioned internal API.

## Storage

Desktop installations target SQLite. Server installations target MariaDB/MySQL. Modules use repositories/data-access abstractions rather than database-specific queries where practical.

Own observation data, downloaded catalog data and temporary/live provider data are logically separated.

## Provider principle

A feature asks for a capability, not a named external service. Example provider interfaces include WeatherProvider, SeeingProvider, SpaceWeatherProvider, EphemerisProvider, MeteorProvider, MinorPlanetProvider, SatelliteProvider and GeocodingProvider.

## Offline principle

Offline operation is a normal state. Cached data includes timestamps and freshness status. Observation creation never requires network access. Sync must not overwrite unsynchronised scientific observations silently.

## Extensions

Official features live in `modules/`. Community packages use the plugin system. Installation-specific code lives under `custom/` and must survive upgrades.

## Devices

Devices receive identities, tokens and capability-scoped permissions. Planned transports include HTTP, WebSocket, MQTT and serial adapters. Arduino/ESP32/Raspberry Pi integrations should be possible through published SDKs and protocol documentation.

## Updates

Core, modules, plugins and data packs have independent version metadata. Executable updates require integrity verification and backup/migration safeguards. Community code is not automatically trusted.

## Scientific provenance

Relevant imported, measured and derived data should preserve source, timestamps, licences/attribution, instrument/observer information, algorithm versions and uncertainty where applicable.
