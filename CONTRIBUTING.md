# Contributing to AstroHub

Thank you for helping build AstroHub.

## Project principles

Contributions should preserve AstroHub's core goals: local-first operation, offline usability, user ownership of observations, modularity, transparent calculations, accessibility and open hardware interoperability.

## Before coding

1. Check existing issues and the roadmap.
2. Open or comment on an issue for substantial changes.
3. Keep Core small; astronomy-specific behaviour belongs in modules or providers where possible.
4. Do not introduce mandatory cloud services for core functionality.
5. Preserve provenance and licence information for external scientific data.
6. Never overwrite the `custom/` area during updates.

## Development areas

- `apps/` — user-facing clients
- `core/` — framework, API, auth, workspaces, sync and platform services
- `modules/` — official astronomy features
- `science/` — scientific calculations
- `providers/` — replaceable data-source adapters
- `sdk/` — hardware/integration SDKs
- `custom/` — installation-specific extensions

## Pull requests

Keep pull requests focused. Describe what changed, why it changed, how it was tested and whether database/API compatibility is affected.

## Scientific contributions

Document assumptions, units, coordinate frames, time standards, algorithms, source datasets and uncertainty where relevant. Prefer reproducible calculations over opaque scores.

## Hardware integrations

Hardware contributions should document protocol, capabilities, permissions, timestamp source and failure/offline behaviour.

## Licence

By contributing, you agree that your contribution may be distributed under AstroHub's AGPL-3.0-or-later licence unless a clearly documented third-party component requires separate treatment.
