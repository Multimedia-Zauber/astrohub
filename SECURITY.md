# AstroHub Security Policy

AstroHub may handle local account data, precise observing locations, device credentials and scientific observations. Security and privacy therefore form part of the architecture rather than an optional add-on.

## Supported versions

AstroHub is currently pre-release software. Until the first stable release, security fixes target the latest development release.

## Reporting a vulnerability

Please do **not** publish exploitable security vulnerabilities as public issues. Contact the project maintainers privately through an available private security-reporting channel. A GitHub private vulnerability-reporting workflow is planned before public alpha distribution.

## Security principles

- Store passwords only as modern password hashes.
- Store device/API tokens as hashes where possible.
- Apply least privilege to users, plugins and devices.
- Keep the local Science service inaccessible from the public network by default.
- Validate downloaded updates and data packs.
- Do not silently install executable updates.
- Back up user data before migrations or core updates.
- Treat precise observing locations as private by default.
- Avoid sensitive data in application logs.

## Third-party plugins

Community plugins are not automatically trusted. AstroHub's planned plugin system will expose requested permissions and compatibility metadata before installation.
