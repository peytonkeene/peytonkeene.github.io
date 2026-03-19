# PulseChart EMS - Starter PHP Foundation

This repository now contains a clean PHP application shell designed for shared hosting (including GoDaddy/cPanel deployments).

## Demo Login
- **Email:** `demo@pulsechartems.com`
- **Password:** `EMSdemo!2026`

## Project Structure
- `index.php` - Routes users to login or dashboard based on session.
- `login.php` / `logout.php` - Session-based authentication entry and exit.
- `dashboard.php` - Authenticated landing page with core module links.
- `generators/` - Reserved folder for future narrative generators.
- `admin/`, `reports/`, `account/` - Placeholder modules for scalable expansion.
- `includes/` - Shared bootstrap and app shell layout files.
- `partials/` - Sidebar and topbar UI components.
- `config/` - App and auth configuration with DB-ready auth placeholders.
- `helpers/` - Utility functions.
- `assets/css/` and `assets/js/` - Shared styling and client-side enhancements.
- `pages/placeholder.php` - Reusable placeholder module template.

## Adding Future Narrative Generators
1. Create new files inside `generators/` (e.g., `generators/medical.php`).
2. Add links/cards in `generators/index.php`.
3. Keep business logic separated into helper or service files as features grow.

## cPanel Deployment Notes
1. Upload all files to your domain root (or `public_html`).
2. Confirm your hosting PHP version is current (PHP 8+ recommended).
3. If installed in a subdirectory, update `APP_URL` in `config/app.php` to that path.
4. No Composer or Node build steps are required.
