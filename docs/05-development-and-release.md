# Development and release

## Local checks

Run PHP syntax checks in an environment with PHP installed:

```sh
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Required acceptance checks

1. Configure both a one-filter and a two-filter profile against a disposable Collection.
2. Reorder one group and verify that no other group’s ordering field changes.
3. Verify the public template sorts by the configured field.
4. Submit a stale page or modified item ID and confirm the save is rejected.
5. Verify an editor without Collection edit permission cannot use the profile.
6. Verify an item-title link opens its native editor in a new tab without starting a drag.

## Release process

1. Update `CHANGELOG.md` and documentation.
2. Run the acceptance checks on a non-production Runway installation.
3. Commit all reviewed changes and push `main`.
4. Create an annotated `vX.Y.Z` tag and GitHub release.
5. Attach or link an installable source ZIP if appropriate.
