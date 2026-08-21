# Namecheap Shared Hosting Deployment

This folder is the deploy-ready Laravel app. The static frontend has already been copied into `public`, so the same domain serves both the website and `/api`.

## 1. Upload Location

Upload this entire `namecheap-deploy` folder to your hosting account, preferably outside `public_html`, for example:

```text
/home/cpaneluser/namecheap-deploy
```

Set your domain document root in cPanel to:

```text
/home/cpaneluser/namecheap-deploy/public
```

The `public` folder contains:

- Laravel `index.php`
- Laravel `.htaccess`
- Frontend pages such as `index.html`, `login.html`, `dashboard.html`
- Admin pages under `public/admin`
- Static assets under `public/temp`, `public/dash`, and `public/storage`

## 2. Create MySQL Database

In cPanel, open **MySQL Databases** and create:

- A database
- A database user
- Add the user to the database with **All Privileges**

Namecheap/cPanel database names are usually prefixed, for example:

```text
cpaneluser_holdrisex
cpaneluser_holdrisex_user
```

## 3. Create Production `.env`

On the server, copy:

```bash
cp .env.production.example .env
```

Edit `.env` and replace:

```env
APP_URL=https://yourdomain.com
DB_DATABASE=cpaneluser_database
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=replace_with_database_password
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=replace_with_a_strong_admin_password
```

Use a strong admin password before running the seeder.

## 4. Run Laravel Setup

SSH into Namecheap, go to the app folder, then run:

```bash
cd /home/cpaneluser/namecheap-deploy
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

If Composer is unavailable on the server, upload the included `vendor` folder with the project.

## 5. Test URLs

Open these after deployment:

```text
https://yourdomain.com
https://yourdomain.com/login.html
https://yourdomain.com/admin/login.html
https://yourdomain.com/api/plans
```

`/api/plans` should return JSON. If it returns a 404 or HTML error page, the document root is probably not pointing to `public`.

## 6. Important Notes

- PHP must be 8.2 or newer.
- The frontend is already configured to call `https://yourdomain.com/api`.
- Keep `.env` private. Never place the app root itself inside a public web folder unless the document root is specifically set to the `public` directory.
- `APP_DEBUG` must stay `false` in production.
