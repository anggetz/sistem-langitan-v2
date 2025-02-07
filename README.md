# Laravel + InertiaJS (Vue) + Metronic Theme

Proyek Laravel 11 + InertiaJS (Vue) + Metronic Theme.

## Setup Development

### Requirements

- Node LTS
- PHP 8.2
- Composer

### Step

1. Install Node modules
   ```
   $ npm i
   ```

2. Install Composer vendors
   ```
   $ composer install
   ```

3. Setup .env
   ```
   $ composer run post-root-package-install
   ```
   or copy file `.env.example` to `.env`.

4. Create App Key
   ```
   $ php artisan key:generate
   ```

5. Build Metronic theme assets.
   
   a. Delete `type` on line 3 from `package.json`:
      ```
      {
        ...
        "type": "module",
        ...
      }
      ```
   b. Run:
      ```
      $ npm run metronic:build
      ```
   c. Restore `type` line:
      ```
      {
        ...
        "type": "module",
        ...
      }
      ```

6. Run development server
   ```
   $ composer run dev
   ```
