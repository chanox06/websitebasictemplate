# Template Content Seeding

This template includes a portable snapshot of the current Samson Styles block-theme content in `content/`. Block-theme templates, template parts, navigation, global styles, attachment records, and selected site options live in the WordPress database, so copying theme files alone is not enough to carry the current design into a new site.

The snapshot is not a full database dump. `bin/seed-content.php` recreates only the content records listed below and is safe to run repeatedly.

## What is included

```
content/site.json                  Source URL and target theme slug
content/templates/                 wp_template records + manifest
content/parts/                     wp_template_part records + manifest
content/navigation/                wp_navigation records + manifest
content/pages/                     Page files + manifest (currently empty)
content/media/manifest.json        Attachment metadata and upload paths
content/options.json               Selected site options
content/global-styles.json         Global styles post content
bin/export-content.php             Export a site's database content
bin/seed-content.php               Idempotent seeder
wp-content/uploads/                Starter media referenced by content/media
```

The content is currently adapted to the template's theme slug, `basic-site-template`. The source URLs still point at the Samson Styles development site; the seeder rewrites them to the new site's `home_url()` and uploads URL.

## Install starter content in a new site

1. Copy this directory to the new site's document root.
2. Create a new MySQL/MariaDB database and `wp-config.php` as described in `CLIENT-SETUP.md`.
3. Complete the WordPress installer and activate **Basic Site Main Template**.
4. Keep the bundled plugins active as needed (`super-block-slider`, WPForms, WP Mail SMTP).
5. Confirm that `wp-content/uploads/` is present. The template includes the starter media files.
6. Dry run the seed:

   ```powershell
   php bin/seed-content.php --dry-run
   ```

   With WP-CLI:

   ```powershell
   wp eval-file bin/seed-content.php -- --dry-run
   ```

7. Run the seed:

   ```powershell
   php bin/seed-content.php
   ```

8. Flush rewrites and caches:

   ```powershell
   php bin/seed-content.php --flush
   ```

9. Open the site and verify the homepage, header/menu, logo, images, footer, and forms.

If you want a blank site instead, skip steps 6-8. The theme's own `templates/` and `parts/` files remain available as generic placeholders and are used when no database template/part overrides them.

## Update this snapshot from the source site

The source site is the Samson Styles development site. After editing it in Gutenberg or the Site Editor:

1. Export directly into this template's content directory from the source project:

   ```powershell
   php bin/export-content.php --out="D:\xampp\htdocs\_basicWebsites\_basic_site_main_template\content" --theme="basic-site-template"
   ```

   The `--out` flag writes outside the source project's own `content/` directory. The `--theme` flag writes the target theme slug and retargets explicit `"theme":"samsonstyles"` block attributes so the snapshot works with `basic-site-template`.

2. Copy any new or changed files from the source `wp-content/uploads/` into this template's `wp-content/uploads/`. The media manifest references those relative paths.

3. Review the content and theme changes:

   ```powershell
   git status
   git diff -- content wp-content/themes/basic-site-template
   ```

4. Commit the template changes together.

Running the exporter without flags still refreshes the source project's own `content/` directory as before.

## What the seed changes

| Area | Behaviour |
|---|---|
| Templates | Creates or updates `wp_template` records for the active theme |
| Template parts | Creates or updates `wp_template_part` records and their areas |
| Navigation | Creates or updates `wp_navigation`; header `ref` values are remapped to the new ID |
| Global styles | Creates or updates the theme's `wp_global_styles` record |
| Media | Matches attachments by `_wp_attached_file`; creates missing ones; remaps old IDs in block markup and options |
| URLs | Replaces the source site URL and uploads URL with the target `home_url()` and uploads URL |
| Pages | Creates missing pages; existing pages are left alone unless `--update-pages` is passed |
| Options | Updates `blogname`, `blogdescription`, `show_on_front`, `page_on_front`, `page_for_posts`, `site_logo`, `site_icon`, and the privacy-policy page when present |
| Not touched | `home`/`siteurl`, users, plugins, the active theme setting, posts, other post types, and uploads files |

A second run on the same content reports `Unchanged` and does not duplicate records.

## Options

| Flag | Purpose |
|---|---|
| `--dry-run` | Report changes without writing |
| `--skip-media` | Match existing attachments but do not create/update them |
| `--skip-pages` | Do not seed pages |
| `--update-pages` | Update existing pages; default is create-only |
| `--flush` | Flush rewrite rules after seeding |
| `--allow-theme-mismatch` | Allow seeding when the active theme differs from `content/site.json` |
| `--help` | Show usage |

## Requirements

- PHP CLI, or WP-CLI.
- The active theme must be `basic-site-template` unless `--allow-theme-mismatch` is used.
- The plugins used by the content should be active. The seeder does not change plugin state.
- The media files under `wp-content/uploads/` must be present. They are included in this template and are no longer ignored by `.gitignore`.
- The database user must be able to create/update posts and options. No database dump or import is needed.

## Environment settings

The seeder deliberately does **not** change `home` or `siteurl`. Set these per environment in `wp-config.php`:

```php
define( 'WP_HOME',    'https://example.com' );
define( 'WP_SITEURL', 'https://example.com' );
```

If these constants are not used, update **Settings -> General** manually. The seeder uses the current `home_url()` as the target URL when rewriting content.

## Troubleshooting

- **Active theme mismatch**: activate `basic-site-template`, or pass `--allow-theme-mismatch` only when intentionally seeding into another theme.
- **Slug conflict**: another theme already has a database template/part with the same slug. The seeder stops instead of letting WordPress suffix the slug (`front-page-2`). Remove or rename the conflicting record, or start from a fresh database.
- **Missing media warnings**: the file is absent from `wp-content/uploads/`. Restore the starter media from this template.
- **Pages do not update**: pages are create-only by default. Pass `--update-pages` when you intentionally want to overwrite existing page content.
- **The site still shows the old layout**: database templates/parts override theme files. Run the seeder, or reset the template/part in the Site Editor.
- **404s after install**: flush rewrites (`--flush`) and confirm the web server rewrite rules are working.
- **Old localhost URLs remain**: confirm `content/site.json` has the correct `source_url` and `source_uploads_url`, and that the new site's `home_url()` is the intended target.
- **Starter branding appears**: the snapshot is the current Samson Styles demo content. Replace the site title, logo, copy, and images before launch.
