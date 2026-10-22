---
project: fhd_runway_collecter
product_name: FHD Collection Organizer
repository: https://github.com/fathead-design/fhd_runway_collecter
status: pre-release
updated: 2026-10-21
---

# Project overview

FHD Collection Organizer is a standalone Perch Runway admin app for arranging only the items in a selected part of a Collection. It is intended for menu-like Collections where the normal Perch reorder screen is too broad for an editor to use comfortably.

## What it does

1. An administrator creates a profile for one Collection.
2. An editor chooses a primary category and, optionally, a secondary category.
3. The organizer displays only items that carry the selected category paths.
4. The editor drags those items into the intended public display order and saves.

The app writes a configured public ordering field, normally `item_order`. It deliberately does not change Perch’s global Collection `itemOrder` value.

## Boundaries

- It is an admin organizer, not a front-end menu editor.
- It supports one or two category-set filters, not arbitrary nested trees.
- It does not infer the public sort field; every profile configures one explicitly.
- It leaves all item content untouched except for the configured order field.

## Related material

- [Installation](01-installation.md)
- [Profile configuration](02-configuration.md)
- [Danny’s Pizza-style example](../examples/dannys-menu-config.md)
