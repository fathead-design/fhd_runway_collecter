# Profile configuration

Each profile identifies one Collection and the exact filtering and ordering behavior for it.

| Setting | Required | Purpose |
| --- | --- | --- |
| Profile label | Yes | Editor-facing name, such as “Menu Items”. |
| Collection | Yes | The Perch Collection whose items can be ordered. |
| Primary category-set slug | Yes | The first grouping, such as a menu placement. |
| Secondary category-set slug | No | A second grouping, such as a menu section. |
| Item title field | No | Field shown in the organizer; `_title` is the fallback. |
| Public ordering field | Yes | Field updated when the filtered order is saved; defaults to `item_order`. |

Use category-set **slugs**, not display names. Use Collection field IDs for the title and ordering fields. The app permits only simple safe identifiers for field IDs and slugs.

## Public template requirement

The public template/query must sort by the configured ordering field. For example:

```php
perch_collection('Menu Items', [
    'sort' => 'item_order',
    'sort-order' => 'ASC',
]);
```

If the public output sorts by Perch’s default Collection order instead, organizer changes will not affect the site.
