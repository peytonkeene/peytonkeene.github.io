# Project Structure Overview

This repository has been reorganized to improve maintainability while preserving existing public routes and behavior.

## Current layout

- `/*.php`  
  Public compatibility entry points kept at the web root. These filenames are preserved so existing links, bookmarks, form actions, and redirects continue to work.
- `app/pages/*.php`  
  Moved PHP page implementations and handlers.
- `app/config/database.php`  
  Canonical database connection implementation.
- `/db.php`  
  Backward-compatible loader that now requires `app/config/database.php`.

## Why this is cPanel-friendly

- Works with plain PHP includes (no Composer/Node/framework requirements).
- Supports direct upload to `public_html` while keeping existing URLs unchanged.
- Keeps MySQL session and form processing behavior in standard shared-hosting-compatible PHP.

## Intentional compatibility decisions

To avoid breaking live traffic and existing workflows:

- Generator files were intentionally left in place at the root:
  - `generate_narrative.php`
  - `gcems_narrative_generator.php`
  - `medical_necessity.php`
  - `narrative_generator_hughes.php`
- HTML pages were left in place at the root to preserve static link paths and direct navigation.
- Root PHP filenames were retained as stable public routes; each now forwards to implementation files under `app/pages/`.

## Deployment notes (GoDaddy/cPanel)

1. Upload the full repository contents into `public_html` (or a subdirectory).
2. Ensure `app/` directory uploads with PHP files intact.
3. Confirm PHP version supports mysqli and password hashing functions used by the app.
4. If you update DB credentials, update only `app/config/database.php`.
5. Keep `db.php` at the root for backwards compatibility with current includes.
