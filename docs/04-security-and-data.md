# Security and data model

## Access control

An editor must have both:

- the `fhd_runway_collecter` app privilege; and
- Perch’s normal permission to edit the profile’s Collection.

Profile configuration additionally requires `fhd_runway_collecter.configure`.

## Save validation

Before saving, the app re-queries the Collection’s current revision using the chosen category paths. It rejects a submission unless the submitted item IDs exactly match the currently filtered set and every submitted position is numeric. This prevents a stale page or a modified request from reordering unrelated items.

## Data written

For each verified item, the app updates only:

1. the configured ordering field in the item’s JSON; and
2. the matching `collection_index` entry for that field and current item revision.

It does not update unrelated item fields or Perch’s global Collection reorder value.
