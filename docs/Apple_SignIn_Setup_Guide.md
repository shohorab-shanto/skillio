# Apple Sign-In Setup Guide

## 🍎 Overview

This guide explains how to properly configure Apple Sign-In for your Laravel application using Laravel Socialite.

---

## ⚠️ Important Note

Apple Sign-In is **different** from Google/Facebook OAuth. Instead of a simple client secret, Apple uses:
- **Private Key** (.p8 file)
- **Team ID**
- **Key ID**
- **Service ID** (client_id)

---

## 📋 Prerequisites

1. **Apple Developer Account** (paid membership required - $99/year)
2. **Domain with HTTPS** (Apple requires SSL)
3. **Email domain verified** (for private relay)

---

## 🔧 Step-by-Step Setup

### Step 1: Create an App ID

1. Go to [Apple Developer Portal](https://developer.apple.com/account)
2. Navigate to **Certificates, Identifiers & Profiles**
3. Click **Identifiers** → **+** (plus button)
4. Select **App IDs** → Continue
5. Fill in:
   - **Description**: `Skillio App`
   - **Bundle ID**: `com.skillio.app` (or your domain)
6. Under **Capabilities**, enable **Sign In with Apple**
7. Click **Continue** → **Register**

---

### Step 2: Create a Service ID (for Web)

1. In **Identifiers**, click **+** again
2. Select **Services IDs** → Continue
3. Fill in:
   - **Description**: `Skillio Web Service`
   - **Identifier**: `com.skillio.web` (this is your APPLE_CLIENT_ID)
4. Click **Continue** → **Register**
5. Click on the newly created Service ID
6. Enable **Sign In with Apple**
7. Click **Configure** next to "Sign In with Apple"
8. Configure:
   - **Primary App ID**: Select the App ID from Step 1
   - **Domains and Subdomains**: `skillio.pro`
   - **Return URLs**: `https://skillio.pro/login/apple/callback`
9. Click **Save** → **Continue** → **Save**

---

### Step 3: Create a Private Key

1. In the sidebar, click **Keys**
2. Click **+** (plus button)
3. Fill in:
   - **Key Name**: `Skillio Sign In with Apple Key`
4. Enable **Sign In with Apple**
5. Click **Configure** next to "Sign In with Apple"
6. Select your **Primary App ID** from Step 1
7. Click **Save** → **Continue** → **Register**
8. **Download the .p8 file** (⚠️ YOU CAN ONLY DOWNLOAD THIS ONCE!)
9. Save the file as `AuthKey_XXXXXXXXXX.p8`
10. **Note down the Key ID** (shown at the top, format: XXXXXXXXXX)

⚠️ **IMPORTANT**: Keep this .p8 file secure and backed up! You cannot download it again.

---

### Step 4: Get Your Team ID

1. In Apple Developer Portal, go to **Membership**
2. Your **Team ID** is displayed (10-character code)
3. Copy this value

---

### Step 5: Upload Private Key to Server

1. Create a secure directory on your server:
```bash
mkdir -p /opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple
chmod 700 /opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple
```

2. Upload your `.p8` file to this directory:
```bash
# Example using SCP (from your local machine)
scp AuthKey_XXXXXXXXXX.p8 user@server:/opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/
```

3. Set proper permissions:
```bash
chmod 600 /opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_XXXXXXXXXX.p8
```

---

### Step 6: Configure Environment Variables

Add these to your `.env` file:

```env
# Apple Sign-In Configuration
APPLE_CLIENT_ID=com.skillio.web
APPLE_TEAM_ID=XXXXXXXXXX
APPLE_KEY_ID=YYYYYYYYYY
APPLE_KEY_FILE=/opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_XXXXXXXXXX.p8
APPLE_REDIRECT_URI=https://skillio.pro/login/apple/callback
```

**Replace with your actual values:**
- `APPLE_CLIENT_ID` → Service ID from Step 2
- `APPLE_TEAM_ID` → Team ID from Step 4
- `APPLE_KEY_ID` → Key ID from Step 3 (shown when you created the key)
- `APPLE_KEY_FILE` → Full path to your .p8 file
- `APPLE_REDIRECT_URI` → Must match what you configured in Step 2

---

### Step 7: Install Required Package (if not already installed)

```bash
composer require socialiteproviders/apple
```

---

### Step 8: Update Service Provider (Laravel 11)

Since you're using Laravel 11, the configuration is already in `config/services.php` (we just updated it).

No additional service provider configuration needed!

---

### Step 9: Test the Implementation

1. Clear config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Visit: `https://skillio.pro/login/apple`
3. You should be redirected to Apple's Sign In page
4. After signing in, you'll be redirected back to your callback

---

## 🔍 Verification Checklist

- [ ] App ID created and Sign In with Apple enabled
- [ ] Service ID created with correct domain and return URL
- [ ] Private key (.p8) downloaded and uploaded to server
- [ ] Key ID noted down
- [ ] Team ID retrieved
- [ ] .env file updated with all 5 values
- [ ] .p8 file has correct permissions (600)
- [ ] HTTPS enabled on domain
- [ ] Domain matches in Apple Developer and .env

---

## 🐛 Troubleshooting

### Error: "invalid_client"

**Causes:**
1. Wrong `APPLE_CLIENT_ID` (should be Service ID, not App ID)
2. Wrong `APPLE_TEAM_ID`
3. Wrong `APPLE_KEY_ID`
4. Incorrect path to `.p8` file
5. Private key file not readable

**Solutions:**
```bash
# Check if file exists
ls -la /opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/

# Check file permissions
stat /opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_*.p8

# Test file is readable by web server
sudo -u www-data cat /opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_*.p8
```

---

### Error: "invalid_request" or "redirect_uri_mismatch"

**Causes:**
1. Redirect URI doesn't match what's configured in Apple Developer
2. Domain not added to Service ID configuration

**Solutions:**
1. Verify `.env` APPLE_REDIRECT_URI matches exactly (including https://)
2. Check Service ID configuration in Apple Developer Portal
3. Ensure domain is added to "Domains and Subdomains"

---

### Error: "unauthorized_client"

**Causes:**
1. Service ID not properly configured
2. App ID not selected as Primary App ID

**Solutions:**
1. Go back to Service ID configuration
2. Enable "Sign In with Apple"
3. Configure with correct Primary App ID

---

### Private Key Issues

**Cannot find .p8 file:**
```bash
# Find all .p8 files
find /opt/lampp/htdocs/WizTecBD/skillo/web_app -name "*.p8"

# Update .env with correct path
```

**Permission denied:**
```bash
# Set correct ownership (replace www-data with your web server user)
sudo chown www-data:www-data /path/to/AuthKey_*.p8
sudo chmod 600 /path/to/AuthKey_*.p8
```

---

## 📝 Example .env Configuration

```env
# Apple Sign-In (Example)
APPLE_CLIENT_ID=com.skillio.web
APPLE_TEAM_ID=A1B2C3D4E5
APPLE_KEY_ID=X9Y8Z7W6V5
APPLE_KEY_FILE=/opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_X9Y8Z7W6V5.p8
APPLE_REDIRECT_URI=https://skillio.pro/login/apple/callback
```

---

## 🔐 Security Best Practices

1. **Never commit .p8 file to Git**
   - Add to `.gitignore`: `storage/app/apple/*.p8`

2. **Restrict file permissions**
   ```bash
   chmod 600 AuthKey_*.p8  # Only owner can read/write
   ```

3. **Store outside web root** (already doing this in storage/app)

4. **Backup the .p8 file securely** (you can't download it again!)

5. **Use environment variables** (never hardcode credentials)

---

## 📱 Firebase Apple Authentication

For mobile apps using Firebase with Apple Sign-In:

1. The same App ID and Service ID can be used
2. Configure Firebase with your Apple credentials
3. Use the Firebase Apple endpoint: `POST /api/v1/auth/firebase/apple`
4. See `docs/Firebase_Apple_Implementation.md` for details

---

## 🎯 Quick Reference

| What | Where to Find |
|------|--------------|
| **Client ID** | Service ID Identifier (e.g., `com.skillio.web`) |
| **Team ID** | Apple Developer → Membership → Team ID |
| **Key ID** | Shown when creating key (10 characters) |
| **Private Key** | Download when creating key (.p8 file) |
| **Redirect URI** | Service ID → Configure → Return URLs |

---

## ✅ Final Check

After configuration, verify:

```bash
# Check all environment variables are set
php artisan tinker
```

```php
config('services.apple.client_id');    // Should show your Service ID
config('services.apple.team_id');      // Should show your Team ID
config('services.apple.key_id');       // Should show your Key ID
config('services.apple.key_file');     // Should show path to .p8
config('services.apple.redirect');     // Should show callback URL

// Check file exists and is readable
file_exists(config('services.apple.key_file'));  // Should return true
is_readable(config('services.apple.key_file'));  // Should return true
```

---

## 📚 Additional Resources

- [Apple Sign In Documentation](https://developer.apple.com/sign-in-with-apple/)
- [Laravel Socialite Docs](https://laravel.com/docs/socialite)
- [SocialiteProviders Apple](https://socialiteproviders.com/Apple/)

---

**Last Updated:** October 14, 2025

---

## 🆘 Still Having Issues?

If you're still encountering errors:

1. Double-check all IDs match exactly
2. Verify .p8 file is in the correct location
3. Ensure HTTPS is working on your domain
4. Check Laravel logs: `storage/logs/laravel.log`
5. Clear all caches: `php artisan optimize:clear`

Good luck! 🍎✨

