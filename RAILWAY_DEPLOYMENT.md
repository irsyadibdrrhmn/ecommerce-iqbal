# Railway Deployment Guide for Laravel E-Commerce Project

## Overview

This guide walks you through deploying your Laravel e-commerce application to Railway.

## Prerequisites

- GitHub account with your project repository
- Railway account (railway.app)
- A custom domain (optional but recommended)

---

## Step 1: Prepare Your Repository

### 1.1 Ensure all files are committed

```bash
git status
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

The following files have been added for Railway deployment:

- **Procfile** - Tells Railway how to run your application
- **.env** - Updated with production settings
- **.env.example** - Template for environment variables

---

## Step 2: Set Up Railway Project

### 2.1 Create Railway Account

1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub (recommended for easy integration)

### 2.2 Create New Project

1. Click "Create New Project"
2. Select "Deploy from GitHub repo"
3. Choose your repository

### 2.3 Configure Service

Railway will automatically detect it's a PHP project. Configure as follows:

- **Builder**: Heroku Buildpack (automatically selected)
- **Start Command**: Will use Procfile

---

## Step 3: Add MySQL Database

1. In Railway dashboard, click "Add Service"
2. Select "MySQL"
3. Railway will automatically provision a MySQL database

The database credentials will be available as environment variables:

- `DATABASE_URL` - Full connection string

---

## Step 4: Configure Environment Variables

### Required Variables to Set in Railway Dashboard

Go to **Variables** section and add the following:

#### Application Config

```
APP_NAME=YourAppName
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE  (copy from your local .env)
APP_URL=https://your-domain.com   (update with your actual domain)
```

#### Database

```
DB_CONNECTION=mysql
DB_URL=${{ DATABASE_URL }}  (Railway automatically provides this from MySQL service)
```

#### Cache & Sessions

```
CACHE_STORE=database
FILESYSTEM_DISK=public
SESSION_DRIVER=cookie
SESSION_ENCRYPT=true
```

#### Logging

```
LOG_CHANNEL=stack
LOG_LEVEL=info
```

#### Queue

```
QUEUE_CONNECTION=database
```

#### Mail Configuration

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io          (or your email provider)
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=YourAppName
```

#### Third-party Services

```
RAJAONGKIR_API_KEY=your_api_key
RAJAONGKIR_BASE_URL=https://api.rajaongkir.com/starter

MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_IS_PRODUCTION=false  (set to true when ready)
```

#### Broadcasting (Optional - for real-time features)

```
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
```

---

## Step 5: Configure Build Command

Railway will use `composer install` to install PHP dependencies and the Procfile to configure processes.

If needed, add a build command in the **Deployment** tab:

1. Set **Build Command**: (leave empty if using default)
2. Set **Start Command**: (leave empty if using Procfile)

---

## Step 6: Storage & File System

### Create Storage Symlink

Since you're using `FILESYSTEM_DISK=public`, Railway needs a storage link.

Add this to Railway **build environment** or create a `build.sh`:

```bash
php artisan storage:link
```

Or modify your Procfile to include:

```
web: php artisan migrate --force && php artisan storage:link && vendor/bin/heroku-php-apache2 public/
```

---

## Step 7: Run Initial Migrations

### Option A: Manual Migration (Recommended)

1. After first deployment, Railway will show logs
2. Open Railway shell or use deploy hooks
3. Run: `php artisan migrate --force`

### Option B: Add Migration to Procfile

Edit your **Procfile** to run migrations before starting:

```
web: php artisan migrate --force && vendor/bin/heroku-php-apache2 public/
queue: php artisan queue:work --wait=3
scheduler: php artisan schedule:work
```

---

## Step 8: Deploy

### 2.1 Enable Auto-Deploy (Recommended)

1. In Railway dashboard, go to **Settings**
2. Enable **Auto-deploy on push to main** (or your branch)
3. Every push to GitHub will trigger a new deployment

### 2.2 Manual Deployment

1. Click **Deploy** button in Railway dashboard
2. Select the branch to deploy

---

## Step 9: Verify Deployment

### Check Deployment Status

1. Go to **Deployments** tab
2. Wait for deployment to complete (look for green checkmark)
3. Check **Logs** for any errors

### Test Your Application

1. Click **Open in Browser** or use your custom domain
2. Test key features:
    - Homepage loads
    - Product catalog works
    - Cart functionality
    - Payment gateway integration (Midtrans)

### Monitor Logs

```
Railway Dashboard → Logs
```

---

## Step 10: Set Up Custom Domain (Optional)

1. In Railway dashboard, go to **Settings**
2. Under **Domains**, click **Add Domain**
3. Enter your custom domain (e.g., yourdomain.com)
4. Update your domain's DNS to point to Railway:
    - Follow Railway's DNS instructions
    - Typical setup: `CNAME` record pointing to Railway's provided domain
5. Wait for DNS propagation (can take up to 24 hours)

---

## Troubleshooting

### Database Connection Issues

- Verify `DATABASE_URL` is set correctly
- Check if MySQL service is running
- Run `php artisan migrate --force` manually in Railway shell

### Missing Environment Variables

- Ensure all variables from `.env.example` are set in Railway
- Check for typos in variable names
- Restart deployment after adding variables

### Build Failures

- Check **Build Logs** for errors
- Ensure `composer.json` and `package.json` are valid
- Verify all dependencies are properly declared

### Storage/File Upload Issues

- Confirm `FILESYSTEM_DISK=public` is set
- Ensure storage link is created
- Check `storage/` directory permissions

### Queue Jobs Not Processing

- Verify `QUEUE_CONNECTION=database` is set
- Check if queue worker process is running
- Review queue jobs in database

---

## Important Notes

1. **APP_KEY**: Use the same key from your local `.env`. If missing:

    ```bash
    php artisan key:generate
    ```

2. **Database Migrations**: Must be run after first deployment. You can:
    - Run manually in Railway shell
    - Add to Procfile web command with `php artisan migrate --force`

3. **Security**:
    - Never commit actual API keys to GitHub
    - Always use Railway's environment variables
    - Rotate keys regularly

4. **Storage**:
    - Public disk files are stored in `storage/app/public`
    - Create symlink with `php artisan storage:link`

5. **Queue Processing**:
    - The Procfile includes a queue worker
    - Processes background jobs from database
    - Monitor with `php artisan queue:work`

---

## Useful Commands

### Access Railway Shell

```bash
railway shell
# Then run any artisan command
php artisan tinker
php artisan migrate --fresh --seed
```

### View Logs

```bash
railway logs
```

### Restart Application

```bash
railway run php artisan config:cache
railway run php artisan cache:clear
```

---

## Next Steps

1. ✅ Deploy to Railway
2. ✅ Verify database is working
3. ✅ Test all payment integrations
4. ✅ Set up monitoring/alerts
5. ✅ Configure custom domain
6. ✅ Set up email service for production
7. ✅ Monitor logs regularly

---

## Support

- Railway Docs: https://docs.railway.app/
- Laravel Deployment: https://laravel.com/docs/deployment
- Midtrans: https://docs.midtrans.com/
