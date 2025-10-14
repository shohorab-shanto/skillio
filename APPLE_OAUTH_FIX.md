# Apple OAuth "invalid_client" Error - FIXED ✅

## 🔴 Problem

Error: `Client error: POST https://appleid.apple.com/auth/token resulted in a 400 Bad Request response: {"error":"invalid_client"}`

## ✅ Solution

Apple Sign-In uses a **different authentication method** than Google/Facebook. Instead of a simple `client_secret`, Apple requires:

1. **Private Key** (.p8 file)
2. **Team ID**
3. **Key ID**
4. **Service ID** (client_id)

## 🔧 What Was Fixed

### 1. Updated `config/services.php`

Changed from:
```php
'apple' => [
    'client_id' => env('APPLE_CLIENT_ID'),
    'client_secret' => env('APPLE_CLIENT_SECRET'),  // ❌ WRONG
    'redirect' => env('APPLE_REDIRECT_URI'),
],
```

To:
```php
'apple' => [
    'client_id' => env('APPLE_CLIENT_ID'),
    'team_id' => env('APPLE_TEAM_ID'),        // ✅ Required
    'key_id' => env('APPLE_KEY_ID'),          // ✅ Required
    'key_file' => env('APPLE_KEY_FILE'),      // ✅ Required
    'redirect' => env('APPLE_REDIRECT_URI'),
],
```

### 2. Environment Variables Required

Update your `.env` file with:

```env
# Apple Sign-In Configuration
APPLE_CLIENT_ID=com.yourapp.service          # Service ID from Apple Developer
APPLE_TEAM_ID=XXXXXXXXXX                     # 10-character Team ID
APPLE_KEY_ID=YYYYYYYYYY                      # Key ID from Apple Developer
APPLE_KEY_FILE=/full/path/to/AuthKey_XXX.p8  # Path to private key file
APPLE_REDIRECT_URI=https://skillio.pro/login/apple/callback
```

## 📋 Required Steps

### Quick Setup Checklist:

- [ ] **Get Team ID** from Apple Developer → Membership
- [ ] **Create Service ID** in Apple Developer Portal
- [ ] **Create Private Key** (.p8 file) and download it
- [ ] **Upload .p8 file** to server (e.g., `storage/app/apple/`)
- [ ] **Update .env** with all 5 Apple variables
- [ ] **Clear config cache**: `php artisan config:clear`
- [ ] **Test**: Visit `https://skillio.pro/login/apple`

## 📖 Full Documentation

See detailed setup instructions: `docs/Apple_SignIn_Setup_Guide.md`

## 🔍 How to Get Credentials

### Step 1: Get Team ID
1. Go to [Apple Developer](https://developer.apple.com/account)
2. Click **Membership**
3. Copy your **Team ID** (10 characters)

### Step 2: Create Service ID
1. Go to **Certificates, Identifiers & Profiles**
2. Click **Identifiers** → **+**
3. Select **Services IDs**
4. Create with identifier like: `com.skillio.web`
5. Enable **Sign In with Apple**
6. Configure domains and return URL

### Step 3: Create Private Key
1. Go to **Keys** → **+**
2. Enable **Sign In with Apple**
3. Download the `.p8` file ⚠️ (only chance!)
4. Note the **Key ID**

### Step 4: Configure Server
```bash
# Create directory
mkdir -p storage/app/apple

# Upload .p8 file
# Set permissions
chmod 600 storage/app/apple/AuthKey_*.p8
```

### Step 5: Update .env
```env
APPLE_CLIENT_ID=com.skillio.web                    # From Step 2
APPLE_TEAM_ID=ABC123XYZ                           # From Step 1
APPLE_KEY_ID=DEF456UVW                            # From Step 3
APPLE_KEY_FILE=/path/to/storage/app/apple/AuthKey_DEF456UVW.p8
APPLE_REDIRECT_URI=https://skillio.pro/login/apple/callback
```

### Step 6: Clear Cache & Test
```bash
php artisan config:clear
php artisan cache:clear
```

Visit: `https://skillio.pro/login/apple`

## 🐛 Common Issues

### Issue: Still getting "invalid_client"

**Check:**
1. ✅ Team ID is correct (from Membership page)
2. ✅ Key ID matches the one shown when you created the key
3. ✅ Client ID is the Service ID (not App ID)
4. ✅ .p8 file path is absolute and correct
5. ✅ .p8 file is readable by web server

**Test file access:**
```bash
# Check file exists
ls -la storage/app/apple/

# Test if web server can read it
sudo -u www-data cat storage/app/apple/AuthKey_*.p8
```

### Issue: "redirect_uri_mismatch"

**Fix:**
- Verify Service ID configuration in Apple Developer
- Ensure redirect URI matches exactly (including https://)
- Domain must be added to "Domains and Subdomains"

### Issue: Cannot find private key

**Fix:**
```bash
# Find the file
find . -name "*.p8"

# Update .env with correct absolute path
APPLE_KEY_FILE=/opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_XXX.p8
```

## 📝 Example Working Configuration

```env
# Working Example (with dummy values)
APPLE_CLIENT_ID=com.skillio.web
APPLE_TEAM_ID=A1B2C3D4E5
APPLE_KEY_ID=X9Y8Z7W6V5
APPLE_KEY_FILE=/opt/lampp/htdocs/WizTecBD/skillo/web_app/storage/app/apple/AuthKey_X9Y8Z7W6V5.p8
APPLE_REDIRECT_URI=https://skillio.pro/login/apple/callback
```

## ✅ Verification

Test in Laravel Tinker:
```php
php artisan tinker

config('services.apple.client_id');    // Should show Service ID
config('services.apple.team_id');      // Should show Team ID  
config('services.apple.key_id');       // Should show Key ID
config('services.apple.key_file');     // Should show file path

file_exists(config('services.apple.key_file'));  // Should be true
is_readable(config('services.apple.key_file'));  // Should be true
```

## 🎯 Summary

**The Fix:**
1. Changed `config/services.php` to use `team_id`, `key_id`, `key_file` instead of `client_secret`
2. Need to add 5 Apple environment variables to `.env`
3. Need to upload private key (.p8 file) to server
4. Need to configure Apple Developer Portal correctly

**Next Steps:**
1. Follow `docs/Apple_SignIn_Setup_Guide.md` for complete setup
2. Get credentials from Apple Developer Portal
3. Update `.env` file
4. Upload `.p8` file to server
5. Test the login

---

**Status:** Configuration updated ✅  
**Action Required:** Update `.env` with Apple credentials from Apple Developer Portal

---

See: `docs/Apple_SignIn_Setup_Guide.md` for complete step-by-step instructions.

