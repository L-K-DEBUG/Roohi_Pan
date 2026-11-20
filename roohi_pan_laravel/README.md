# Roohi Pan House

Laravel point-of-sale and stock-management application based on the original Roohi Pan PHP project.

## Requirements

- PHP 8.2 or newer with the MySQL, PDO, and SQLite extensions
- Composer
- Node.js and npm
- MySQL/MariaDB (XAMPP is supported)

## Local setup

1. Create a MySQL database named `roohi_pan_db`.
2. Copy `.env.example` to `.env` if needed and set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` for your local database.
3. From this directory, run:

   ```powershell
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   npm install
   npm run build
   php artisan serve
   ```

4. Open the URL printed by `php artisan serve` and register the first account. The first account is the owner; later registrations are employees. Owners can manage inventory and staff.

Migrations update the existing database schema; do not use `migrate:fresh` if you need to keep existing data.

## Features

- Username sign-in and staff registration
- POS orders, receipts, menu-stock deductions, and order cancellation
- Unpaid-order credit book and payment settlement
- Sales reports for day, week, month, and year
- Owner-managed measured ingredient and individual drink inventory with recipe-based deductions, plus staff roles
