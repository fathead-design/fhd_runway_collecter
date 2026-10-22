# Installation

## Requirements

- Perch Runway 3.0 or later, with Collections enabled
- PHP 7.4 or later
- A Perch admin role permitted to activate apps and configure privileges

## Install the app

1. Copy the repository directory `fhd_runway_collecter` into `perch/addons/apps/`.
2. In Perch Admin, open **Settings → Apps** and activate **FHD Collection Organizer**.
3. Grant `Access FHD Collection Organizer` to editor roles that may organize items.
4. Grant `Configure FHD Collection Organizer` only to roles that may create or change profiles.
5. Open the app from Perch’s app menu and create the first profile.

Activation creates the app-owned `fhd_collecter_profiles` table. No Collection content is changed until an editor saves a filtered order.

## Upgrade guidance

Back up the Perch database before installing or upgrading an admin app. Review the repository `CHANGELOG.md` before each upgrade and validate changes on a non-production installation first.
