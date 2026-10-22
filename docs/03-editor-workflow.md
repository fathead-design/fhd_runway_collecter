# Editor workflow

1. Open **FHD Collection Organizer** in Perch Admin.
2. Select the organizer profile.
3. Select the primary category. If configured, select the secondary category.
4. Drag the displayed items into the desired order.
5. Choose **Save filtered order**.

Only the matching items are displayed and written. Items without a current configured order appear after ordered items, using item ID as a stable tie-breaker.

## Edit an item

Click an item title to open its normal Perch Collection-item editor in a new browser tab. This protects the current organizer tab and its unsaved drag state. The link stops Perch’s sortable-list mouse and click handlers so the click is neither treated as a drag nor cancelled by Perch’s `.basic-sortable` behavior.

After changing a title or category, return to the organizer and refresh it before ordering again.
