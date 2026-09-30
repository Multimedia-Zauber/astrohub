# 🌌 AstroHub

**Open-source, local-first astronomy platform for observation, astrophotography, citizen science and open hardware.**

> Status: `0.1.0-alpha` — early development

AstroHub is designed to make astronomy easier without hiding the science. It combines observing tools, sky conditions, equipment, missions, meteor counting, alerts, offline workflows and extensibility in one modular platform.

**Developed by Multimedia Zauber.**

## Vision

AstroHub should work for beginners, experienced observers, astrophotographers, astronomy groups and citizen-science projects. Users choose what they want to do; AstroHub exposes additional complexity only when useful.

Core principles:

- Free and open source
- Local-first and offline-capable
- No mandatory cloud account
- Modular workspaces and features
- Transparent astronomical recommendations
- Red Astro Night mode to protect dark adaptation
- User-owned observation and measurement data
- Open provider architecture for public astronomy data
- Open hardware support for Arduino, ESP32, Raspberry Pi and community devices
- Extensible modules, plugins and `custom/` code

## AstroHub 0.1 Alpha

The first field-usable alpha targets this workflow:

1. Install AstroHub.
2. Complete onboarding and select astronomy interests.
3. Add observing locations and equipment.
4. Create workspaces such as **My Observation**, **My Photo** or **My Meteors**.
5. View current sky, weather, Moon and space-weather conditions.
6. Search astronomical objects and plan an observing mission.
7. Prepare the mission for offline use.
8. Switch to red Astro Night mode in the field.
9. Record observations and meteor events offline.
10. Synchronize and export the data later.

## Architecture

```text
astrohub/
├── apps/          Web/PWA and desktop clients
├── core/          Application core and API
├── modules/       Official feature modules
├── science/       Python astronomy/science engine
├── providers/     External and local data providers
├── custom/        User/site-specific extensions
├── sdk/           Open-hardware and integration SDKs
├── data-packs/    Versioned offline astronomy data
├── docs/          Architecture and documentation
├── tests/         Automated tests
└── docker/        Server deployment
```

Planned technology direction:

- **Core:** PHP 8.x
- **Frontend/PWA:** TypeScript
- **Science engine:** Python
- **Desktop database:** SQLite
- **Server database:** MariaDB/MySQL
- **Offline web data:** IndexedDB
- **Hardware:** HTTP, WebSocket, MQTT and serial adapters
- **Server deployment:** Docker

## Planned 0.1 modules

Observe · Weather · Space Weather · Equipment · Meteors · Toolbox · Workspaces · Locations · Alerts · Update Center

Later releases are intended to expand into astrophotography, minor planets, comets, satellites, occultations, variable stars, radio astronomy, spectroscopy, solar observing, observatory automation and scientific workflows.

## Personal Astronomy Engine

AstroHub is intended to adapt to selected interests, workspaces, equipment and observing habits. Personalisation remains under user control and should work locally without requiring a cloud AI service.

Examples include relevant observing suggestions and optional notifications for aurora conditions, meteor-shower activity or particularly suitable observing nights.

## Data & providers

AstroHub will use replaceable providers rather than hard-coding individual services into feature modules. Scientific data should retain provenance information including source, licence/attribution, retrieval time and calculation metadata where relevant.

## Open hardware

A long-term goal is a simple AstroHub Device API and SDK so community projects can integrate sensors and instruments without modifying AstroHub Core. The first reference hardware project is planned to be an **ESP32 Meteor Button**.

## Updates

The AstroHub Update Center is planned to manage Core, official modules, community plugins, astronomy data packs and optional compatible device firmware. Updates should be verifiable, backed up before installation and never overwrite `custom/` extensions.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) before submitting changes. Security issues should be handled according to [SECURITY.md](SECURITY.md).

## Project status

AstroHub is currently in the foundation phase. APIs, schemas and module interfaces may change before version 1.0.

## Licence

AstroHub source code is licensed under the **GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later)**. Third-party astronomy datasets and providers retain their respective licences and attribution requirements.

See [LICENSE](LICENSE).

---

**AstroHub** · Free & Open Source · Local First  
**Developed by Multimedia Zauber**
