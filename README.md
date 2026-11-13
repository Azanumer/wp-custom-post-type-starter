# Custom Post Type Starter

A clean WordPress boilerplate for registering a custom post type with a companion taxonomy — the "Portfolio" example ships working, and you rename it in one place.

## Features

- **One-place rename** — change the `CPTS_*` constants at the top of the main file and the whole plugin (labels, slugs, templates, shortcode) follows.
- **Full registration** — public CPT with archive, featured image, excerpt, revisions and REST/block-editor support; hierarchical taxonomy with admin column.
- **Template fallbacks** — bundled `templates/archive-portfolio.php` and `templates/single-portfolio.php` are used only when the theme has no override.
- **Shortcode** — `[portfolio_list count="6" columns="3" category="web-design"]` renders a responsive grid anywhere.
- **Clean permalinks** — rewrite rules flushed on activation/deactivation, so URLs work immediately.

## Installation

1. Upload the `wp-custom-post-type-starter` folder to `/wp-content/plugins/`.
2. Activate it through **Plugins** in WordPress admin.
3. Find **Portfolio Items** in the admin menu and add your first item.

## Make it yours

Edit the constants at the top of `wp-custom-post-type-starter.php`:

```php
define( 'CPTS_POST_TYPE', 'portfolio' );
define( 'CPTS_TAXONOMY', 'portfolio_category' );
define( 'CPTS_SINGULAR', 'Portfolio Item' );
define( 'CPTS_PLURAL',   'Portfolio Items' );
```

Rename the two files in `templates/` to match your new slug (e.g. `archive-case-study.php`), and rename the shortcode tag in the `add_shortcode` call if you like.

## Uninstall

Deactivating keeps your portfolio items in the database (they just stop showing in the menu). Deleting the plugin does not remove content — remove the posts first if you want a clean slate.

## License

MIT — see [LICENSE](LICENSE).
