# Basic Site Main Template

This directory is a reusable WordPress file-and-starter-content template for new client sites.
It includes WordPress core 7.1.4, reusable plugins, the current `basic-site-template` block theme
(including the latest Samson Styles design update), reusable custom form handlers, and a
portable snapshot of the current block-theme database content in `content/`. The starter media
referenced by that snapshot is included under `wp-content/uploads/`. It intentionally does not
include a full database dump, `wp-config.php`, client secrets, installed configuration, or backup files.

## Requirements

- PHP 7.4 or newer. WordPress 7.1.4 is tested here with PHP 8.2.12.
- MySQL 5.5.5 or newer, or MariaDB 10.4 or newer. Tested locally with MariaDB 10.4.32.
- A web server with URL rewriting enabled for pretty permalinks (Apache with `mod_rewrite` or equivalent).

## Installation

1. Copy this directory to the new site's document root or local XAMPP path.
2. Create a new empty MySQL/MariaDB database.
3. Create `wp-config.php` with per-site values. Use one of these methods:

   **Let the WordPress installer create it** - open:

   ```text
   http://localhost/your-site/wp-admin/setup-config.php
   ```

   Enter the database details there. WordPress writes `wp-config.php` and
   generates eight unique authentication keys/salts automatically.

   **Or generate it from the command line** - this also creates fresh random salts:

   ```powershell
   php bin/create-wp-config.php --db-name="site_db" --db-user="root" --db-password="" --db-host="localhost"
   ```

   Optional flags: `--prefix="wp_"`, `--debug`,
   `--recaptcha-site-key="..."`, `--recaptcha-secret-key="..."`, and `--force`
   (replaces an existing config and logs out current sessions).

   Do not copy `wp-config-sample.php` to `wp-config.php` by hand. Its salt
   values are placeholders and are not unique per site.

4. Keep the default `wp_` table prefix, or change it before installation.
5. Add site-specific Google reCAPTCHA v2 keys only if the public forms will use reCAPTCHA.
6. Open the site's `/wp-admin/install.php` and complete the WordPress installer.
7. Activate **Basic Site Main Template** under **Appearance > Themes**.
8. Keep the bundled plugins active as needed for the starter content
   (`super-block-slider`, WPForms, and WP Mail SMTP).
9. Optional but recommended: seed the included starter content so the homepage, header,
   footer, template parts, navigation, global styles, and media records match the current
   Samson Styles design:

    ```powershell
    php bin/seed-content.php --dry-run
    php bin/seed-content.php --flush
    ```

    With WP-CLI:

    ```powershell
    wp eval-file bin/seed-content.php -- --dry-run
    wp eval-file bin/seed-content.php -- --flush
    ```

    Skip this step for a blank theme-based site. See `CONTENT-DEPLOYMENT.md` for details and
    for the command that refreshes the snapshot from the source site.

## Customize the new site

1. Set the site title, tagline, WordPress URLs, timezone, and admin email under **Settings > General**.
2. Edit `wp-content/themes/basic-site-template/inc/site-config.php` with the company name,
   tagline, description, phone, email, address, social links, and brand colors. Replace
   every `__PLACEHOLDER__` value before launch.
3. Replace the starter logo and favicon with the client's assets under **Appearance > Editor**
   or **Appearance > Customize**, depending on the active WordPress interface.
4. Edit the homepage in the **Site Editor** and replace the Samson Styles starter headline,
   sections, cards, imagery, proof points, and calls to action. If the starter content was not
   seeded, edit `templates/front-page.html` instead.
5. Replace or remove the starter media under `wp-content/uploads/`, the starter site title,
   and any remaining demo copy before launch.
6. Create the required pages and assign the appropriate forms/shortcodes from the retained
   files in `wp-content/themes/basic-site-template/forms/`.
7. Configure the form recipient in `forms/config.php` or use the WordPress admin email fallback.
8. Configure SMTP and test every form before launch.
9. Go to **Settings > Permalinks** and click **Save Changes** after the site path is finalized.

## Important

- Do not copy a client's `wp-config.php`, database export, cache, or backup archive into this template.
- `content/` and `wp-content/uploads/` contain the intentionally included Samson Styles starter
  snapshot and media, not a client database. Replace the branding and remove unused media before launch.
- The form files are reusable starting points, but each new account must receive its own recipient,
  reCAPTCHA keys, branding, content, and legal pages.