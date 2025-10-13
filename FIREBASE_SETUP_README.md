# 🔥 Firebase Google Authentication Setup Guide

## Quick Overview

Your API now supports **two Google login methods**:

1. **Web:** Direct Google OAuth → `/api/v1/auth/social/google`
2. **Mobile:** Firebase Google Auth → `/api/v1/auth/firebase/google`

**✨ Magic:** Both use the **same Google ID** to prevent duplicate users!

---

## 🚀 Quick Start (3 Steps)

### Step 1: Run Database Migration

```bash
cd /opt/lampp/htdocs/WizTecBD/skillo/web_app
php artisan migrate
```

This adds the `firebase_uid` column to your `users` table.

---

### Step 2: Add Firebase Configuration

Add these to your `.env` file:

```env
# Firebase Configuration (get from Firebase Console)
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg
```

**Where to find these:**
1. Go to https://console.firebase.google.com/
2. Select your project (or create one)
3. Project Settings → General
4. Copy **Project ID** and **Web API Key**

---

### Step 3: Enable Google Sign-In in Firebase

1. Go to Firebase Console → Authentication
2. Click "Sign-in method" tab
3. Enable "Google" provider
4. Save

**Done!** 🎉

---

## 📍 How It Works

### The Account Linking Flow

```
Same Google Account = Same User in Database

Web Login (Google OAuth)
  ↓
Creates user with google_id: "123456789"
  ↓
Later... Mobile Login (Firebase)
  ↓
Firebase token contains same google_id: "123456789"
  ↓
API finds existing user
  ↓
Updates user, adds firebase_uid
  ↓
✅ SAME USER ACCOUNT!
```

**No duplicate users!** The system automatically links accounts using the Google ID.

---

## 📱 Mobile App Integration

### React Native Example

```javascript
import auth from '@react-native-firebase/auth';

// Sign in with Firebase
const userCredential = await auth().signInWithCredential(googleCredential);

// Get Firebase ID token
const firebaseToken = await userCredential.user.getIdToken();

// Send to your API
const response = await fetch('https://your-api.com/api/v1/auth/firebase/google', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ id_token: firebaseToken })
});

const { data } = await response.json();
// Use data.token for subsequent API requests
```

### Flutter Example

```dart
import 'package:firebase_auth/firebase_auth.dart';

// Sign in with Firebase
final userCredential = await FirebaseAuth.instance
    .signInWithCredential(googleCredential);

// Get Firebase ID token
final firebaseToken = await userCredential.user!.getIdToken();

// Send to your API
final response = await http.post(
  Uri.parse('https://your-api.com/api/v1/auth/firebase/google'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({'id_token': firebaseToken}),
);

final data = jsonDecode(response.body);
// Use data['data']['token'] for subsequent API requests
```

---

## 🌐 Web Integration (Already Working!)

Your existing web Google OAuth continues to work:

**Endpoint:** `POST /api/v1/auth/social/google`

No changes needed on the web side!

---

## 🧪 Testing

### Test the Firebase Endpoint

```bash
# Get a Firebase ID token (see Firebase_Google_Testing_Guide.md)
# Then test:

curl -X POST http://localhost/api/v1/auth/firebase/google \
  -H "Content-Type: application/json" \
  -d '{
    "id_token": "YOUR_FIREBASE_ID_TOKEN"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Firebase authentication successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@gmail.com",
      "role": "user",
      "status": "active"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

---

## 📊 Database Structure

After migration, your `users` table has:

```
users:
  ├── id
  ├── email
  ├── name
  ├── google_id ← Links web and mobile logins!
  ├── firebase_uid ← Only set from Firebase login
  ├── apple_id
  ├── password
  ├── role
  ├── status
  └── ... (other fields)
```

---

## 🔍 Verify Account Linking

```bash
php artisan tinker
```

```php
// Find a user
$user = \App\Models\User::where('email', 'test@gmail.com')->first();

// Check linking
$user->google_id;    // Set from both web and mobile
$user->firebase_uid; // Set only from mobile login
$user->id;           // Same ID for both login methods!
```

---

## 📚 Complete Documentation

- **Implementation Summary:** `docs/IMPLEMENTATION_SUMMARY_Firebase_OAuth.md`
- **Firebase Testing Guide:** `docs/Firebase_Google_Testing_Guide.md`
- **Google OAuth Testing:** `docs/Google_OAuth_Testing_Guide.md`
- **API Documentation:** `docs/api-documentation.md`

---

## ⚠️ Important Notes

### Security
✅ Firebase tokens are verified with Firebase servers  
✅ Google tokens are verified with Google servers  
✅ No fake tokens can pass validation  
✅ Tokens expire after 1 hour (security best practice)  

### Account Linking
✅ Automatic linking via Google ID  
✅ Works even if user logs in from different platforms first  
✅ No duplicate accounts created  
✅ User preferences/data preserved across platforms  

---

## 🎯 What Changed?

### New Files:
- `database/migrations/2025_10_13_*_add_firebase_uid_to_users_table.php`
- `docs/Firebase_Google_Testing_Guide.md`
- `docs/IMPLEMENTATION_SUMMARY_Firebase_OAuth.md`
- `FIREBASE_SETUP_README.md` (this file)

### Modified Files:
- `app/Models/User.php` (added `firebase_uid` to fillable)
- `app/Http/Controllers/Api/AuthController.php` (added `firebaseGoogleAuth()` method)
- `config/services.php` (added Firebase config)
- `routes/api.php` (added Firebase route)
- `docs/api-documentation.md` (documented Firebase endpoint)

---

## ✅ Production Checklist

Before deploying:

- [ ] Run `php artisan migrate` on production
- [ ] Add `FIREBASE_PROJECT_ID` to production `.env`
- [ ] Add `FIREBASE_WEB_API_KEY` to production `.env`
- [ ] Enable Google Sign-In in Firebase Console
- [ ] Add production domain to Firebase authorized domains
- [ ] Test with real mobile app
- [ ] Verify account linking works
- [ ] Enable HTTPS

---

## 💡 FAQ

**Q: Do I need the Firebase Admin SDK package?**  
A: No! We use Firebase REST API, which only needs an API key.

**Q: Will existing users be affected?**  
A: No, the migration only adds a new column. Existing users continue to work.

**Q: Can users switch between web and mobile?**  
A: Yes! They'll have the same account on both platforms automatically.

**Q: What if a user logs in via mobile first?**  
A: No problem! When they later log in via web, it will link to the same account.

**Q: Do tokens expire?**  
A: Yes, both Firebase tokens and API tokens expire for security. The mobile app should handle token refresh.

---

## 🆘 Troubleshooting

### "Invalid Firebase token"
- Token expired (get a fresh one)
- Wrong `FIREBASE_PROJECT_ID` in `.env`
- Firebase project not configured properly

### "User created twice"
- Check that same Google account is used
- Verify `google_id` is being extracted from Firebase token
- Check Firebase Console → Authentication → Users

### "Missing required user information"
- Enable Google provider in Firebase
- Ensure email scope is requested
- Check Firebase authentication settings

---

## 🎉 You're All Set!

Run the migration, add the config, and you're ready to support both web and mobile Google logins with automatic account linking!

**Questions?** Check the detailed guides in the `docs/` folder.


