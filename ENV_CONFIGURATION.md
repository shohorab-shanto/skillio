# Environment Variables Configuration

## Required `.env` Variables for Firebase + Google OAuth

Add these to your `.env` file:

---

## 🔥 Firebase Configuration (NEW - Required for Mobile)

```env
# Firebase Project Configuration
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg
```

### Where to Get These Values:

1. **Go to Firebase Console:**
   - URL: https://console.firebase.google.com/

2. **Select your project** (or create a new one)

3. **Go to Project Settings:**
   - Click the ⚙️ (gear icon) → "Project settings"

4. **Find the values:**
   - **Project ID:** In the "General" tab, under "Your project"
   - **Web API Key:** In the "General" tab, under "Web API Key"

### Example Values:
```env
FIREBASE_PROJECT_ID=skillo-app-12345
FIREBASE_WEB_API_KEY=AIzaSyDXk6L3mN7pQ9rT2vW5xY8zA1bC3dE4fG5
```

---

## 🌐 Google OAuth (Existing - Required for Web)

```env
# Google OAuth Credentials
GOOGLE_CLIENT_ID=123456789-abc.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abc123def456
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

### Where to Get These Values:

1. **Go to Google Cloud Console:**
   - URL: https://console.cloud.google.com/

2. **Go to APIs & Services → Credentials**

3. **Create OAuth 2.0 Client ID** (if you haven't already):
   - Application type: Web application
   - Authorized redirect URIs: Add your callback URL

4. **Copy the credentials:**
   - Client ID
   - Client Secret

### Example Values:
```env
GOOGLE_CLIENT_ID=123456789-abc123def456.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-a1b2c3d4e5f6g7h8
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

---

## 📝 Complete `.env` Section

Add this entire section to your `.env` file:

```env
# ============================================
# FIREBASE CONFIGURATION (For Mobile Apps)
# ============================================
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg

# ============================================
# GOOGLE OAUTH (For Web Login)
# ============================================
GOOGLE_CLIENT_ID=123456789-abc.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abc123def456
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

---

## 🔍 How to Verify Configuration

### Check if values are loaded:

```bash
php artisan tinker
```

```php
// Check Firebase config
config('services.firebase.project_id');
config('services.firebase.api_key');

// Check Google config
config('services.google.client_id');
config('services.google.client_secret');
```

If they return the correct values, you're all set! ✅

---

## 🚨 Important Notes

### 1. **Don't commit `.env` to Git**
Your `.env` file should already be in `.gitignore`. Never commit credentials!

### 2. **Production Environment**
For production, use different values:
```env
FIREBASE_PROJECT_ID=skillo-production
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

### 3. **Same OAuth Client**
⚠️ **Important:** Use the **SAME** Google OAuth Client ID in both:
- `.env` file (`GOOGLE_CLIENT_ID`)
- Firebase Console (Google Sign-In provider)

This ensures the `google_id` matches and accounts link properly!

---

## 📋 Setup Checklist

- [ ] Add `FIREBASE_PROJECT_ID` to `.env`
- [ ] Add `FIREBASE_WEB_API_KEY` to `.env`
- [ ] Verify `GOOGLE_CLIENT_ID` exists in `.env`
- [ ] Verify `GOOGLE_CLIENT_SECRET` exists in `.env`
- [ ] Verify `GOOGLE_REDIRECT_URI` exists in `.env`
- [ ] Run `php artisan config:clear` after adding variables
- [ ] Test configuration with `php artisan tinker`
- [ ] Enable Google Sign-In in Firebase Console
- [ ] Add same OAuth Client ID to Firebase

---

## 🔗 Firebase Console Setup

After adding `.env` variables, configure Firebase:

### 1. Enable Google Sign-In

1. Go to: Firebase Console → Authentication
2. Click "Sign-in method" tab
3. Enable "Google"
4. **Important:** In the "Web SDK configuration", enter your `GOOGLE_CLIENT_ID` from `.env`
5. Save

### 2. Add Authorized Domains

1. Still in Authentication → Settings tab
2. Under "Authorized domains", add:
   - `localhost` (for development)
   - Your production domain (for production)

---

## ✅ Quick Test

After configuration, test the Firebase endpoint:

```bash
# This should NOT error about missing config
curl -X POST http://localhost/api/v1/auth/firebase/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "test"}'

# Expected: Returns error about invalid token (which means config is loaded!)
# Not expected: Error about missing configuration
```

---

## 🆘 Troubleshooting

### Error: "Firebase configuration not found"
**Solution:** Add `FIREBASE_PROJECT_ID` and `FIREBASE_WEB_API_KEY` to `.env`

### Error: "Call to undefined method"
**Solution:** Run `php artisan config:clear` and restart server

### Values not loading
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📱 Mobile App Configuration

Your mobile app (React Native/Flutter) also needs Firebase config:

### React Native (`google-services.json` / `GoogleService-Info.plist`)
Download from Firebase Console → Project Settings → Your apps

### Flutter (`firebase_options.dart`)
Generate using:
```bash
flutterfire configure
```

**Important:** Mobile app Firebase config is separate from backend `.env`!

---

## 🎯 Summary

**Minimum required `.env` additions:**

```env
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_WEB_API_KEY=your-api-key
```

**Existing variables (should already be there):**

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
```

Add these, run `php artisan config:clear`, and you're ready to go! 🚀


