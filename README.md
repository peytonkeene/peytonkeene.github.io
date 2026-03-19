# MedNarrate

Traditional PHP web application foundation for MedNarrate, designed for shared hosting environments (including GoDaddy cPanel) with no build step.

## App Structure
- Public pages: `index.php`, `solutions.php`, `contact.php`, `login.php`
- App pages: `dashboard.php`, `generators/`, `reports/`, `account/`, `admin/`
- Auth: `auth/login_process.php`, `auth/logout.php`, `auth/check_auth.php`

---

## Database Setup for GoDaddy Shared Hosting (cPanel)

This repository now includes a starter MySQL schema that is compatible with:
- GoDaddy shared hosting
- cPanel MySQL Databases
- phpMyAdmin import
- Plain PHP (no framework migrations)

## A) Files Added

1. **SQL schema and seed file**
   - `database/mednarrate.sql`
   - Contains table creation, indexes, foreign keys, and optional seed data.

2. **Database config example**
   - `config/database.example.php`
   - Copy to `config/database.php` and set your hosting credentials.

---

## B) Create the Database in cPanel (GoDaddy)

1. Log in to your GoDaddy hosting account.
2. Open **cPanel** for the site.
3. Open **MySQL Databases**.
4. Under **Create New Database**, create a database (example: `mednarrate`).
   - cPanel usually prefixes this automatically, for example: `cpaneluser_mednarrate`.
5. Under **MySQL Users**, create a new user and password.
6. Under **Add User to Database**, choose your new user and database.
7. Click **Add**.
8. Grant **ALL PRIVILEGES** and save.

> Important: cPanel usernames and database names are often prefixed. Use the full prefixed names in your PHP config.

---

## C) Import `mednarrate.sql` with phpMyAdmin

1. In cPanel, open **phpMyAdmin**.
2. In the left sidebar, select your new database.
3. Click the **Import** tab.
4. Click **Choose File** and select:
   - `database/mednarrate.sql`
5. Leave format as SQL.
6. Click **Go**.

If import succeeds, you should see tables:
- `agencies`
- `users`
- `narrative_generators`
- `generated_reports`
- `contact_messages`

---

## D) Connect the App to MySQL

1. Copy the config example:
   - from: `config/database.example.php`
   - to: `config/database.php`
2. Edit values in `config/database.php`:

```php
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'cpanelusername_mednarrate',
    'DB_USER' => 'cpanelusername_mednarrate_user',
    'DB_PASS' => 'your_real_password',
    'DB_CHARSET' => 'utf8mb4',
];
```

Notes:
- On GoDaddy shared hosting, `DB_HOST` is usually `localhost`.
- Use your exact cPanel-prefixed database and username.
- Keep `DB_CHARSET` as `utf8mb4`.

---

## E) Create the First Admin Login

The SQL seed includes one sample admin user in `database/mednarrate.sql`.

Before importing to production:
1. Open `database/mednarrate.sql`.
2. Find the sample admin insert in the seed section.
3. Replace the placeholder hash:
   - `$2y$10$REPLACE_WITH_REAL_PASSWORD_HASH_FROM_PHP_PASSWORD_HASH`
4. Generate a real hash in PHP using:
   - `password_hash('ChangeThisPasswordNow!', PASSWORD_DEFAULT)`
5. Save, then import.

If you already imported, you can run an `UPDATE users ...` query in phpMyAdmin to replace `password_hash`.

---

## F) Basic Troubleshooting

### 1) "Access denied" when connecting
- Verify `DB_USER` and `DB_PASS` exactly.
- Confirm the user was added to the database with **ALL PRIVILEGES**.

### 2) Database not found
- You may be using `mednarrate` instead of full prefixed name (for example `cpaneluser_mednarrate`).

### 3) Import errors in phpMyAdmin
- Ensure you selected the correct target database before import.
- Re-download/re-upload `database/mednarrate.sql` to avoid file corruption.
- Confirm your hosting account supports InnoDB and MySQL foreign keys (most do).

### 4) Foreign key creation fails
- Import into an empty database first.
- Make sure all tables were created with InnoDB.

### 5) Import size/time issues
- This starter SQL file is small and should import normally.
- For larger future dumps, split SQL files or import in parts.

---

## Quick Deployment Summary

- **Tables created:** `agencies`, `users`, `narrative_generators`, `generated_reports`, `contact_messages`.
- **SQL file location:** `database/mednarrate.sql`.
- **Database credentials file to edit:** `config/database.php` (copied from `config/database.example.php`).
- **Where to import in cPanel:** **phpMyAdmin → select database → Import → upload `database/mednarrate.sql` → Go**.
