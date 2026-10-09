# Danny’s Pizza-style profile

This is an example only; it does not modify Danny’s Pizza or require its project files.

| Setting | Value |
| --- | --- |
| Profile label | Menu Items |
| Collection | Danny's Menu Items |
| Primary category-set slug | `dannys-menu-pages` |
| Secondary category-set slug | `dannys-menu-sections` |
| Item title field | `item_name` |
| Public ordering field | `item_order` |

Editor flow: choose **Menu Items**, then **Food Menu**, then **Starters**. The list contains only Collection items carrying both selected category paths. Saving writes `1`, `2`, `3`, and so on into `item_order` for that group’s current items and refreshes the `item_order` Collection index entries.

The public menu template must sort by `item_order`; Perch’s built-in Collection reorder value is deliberately not used.
