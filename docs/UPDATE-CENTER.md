# AstroHub Update Center

AstroHub uses GitHub Releases as the canonical application update channel while keeping the installed application usable offline.

## Behaviour

`GET /api/v1/updates` checks the latest public GitHub Release and compares it with the installed AstroHub version. A failed network request is not an application failure: the endpoint reports offline state and AstroHub continues to work.

`GET /api/v1/updates/data-packages` retrieves the versioned `data-packages/manifest.json`. Astronomy catalogues and reference datasets can therefore evolve independently of the desktop application.

## Safety rules

The update center must never silently execute downloaded code. Future desktop installers should show version, release notes and source before installation. Automatic background checks may be enabled by the user, but installation remains explicit.

Before automatic package installation is implemented, packages should gain SHA-256 checksums and optionally signed manifests. A failed verification must reject the package and retain the previous working version.

## Channels

Stable releases are the default. Prerelease/nightly channels may be added later as an explicit developer setting.

## Open-source principle

GitHub remains the transparent source of truth: users can inspect releases, commits and package manifests. Alternative mirrors can later implement the same manifest format so AstroHub is not permanently locked to one distribution provider.
