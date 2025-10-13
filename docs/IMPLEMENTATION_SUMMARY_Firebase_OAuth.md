# Firebase Google OAuth Implementation Summary

## ✅ What Was Implemented

This document summarizes the Firebase Google Authentication implementation for mobile apps with automatic account linking to web Google OAuth.

---

## 🎯 Problem Solved

**Challenge:** Users can log in via:
- **Web:** Google OAuth (direct)
- **Mobile:** Firebase Authentication (with Google provider)

**Solution:** Both methods now link to the **same user account** automatically!

---

## 📦 Files Created/Modified

### 1. **Database Migration**
**File:** `database/migrations/2025_10_13_140329_add_firebase_uid_to_users_table.php`

**What it does:**
- Adds `firebase_uid` column to `users` table
- Adds index for fast lookups
- Allows linking Firebase accounts to users

**To run:**
```bash
php artisan migrate
```

---

### 2. **User Model**
**File:** `app/Models/User.php`

**Changes:**
- Added `firebase_uid` to `$fillable` array
- Allows mass assignment of Firebase UID

---

### 3. **Configuration**
**File:** `config/services.php`

**Added:**
```php
'firebase' => [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'api_key' => env('FIREBASE_WEB_API_KEY'),
    'credentials' => env('FIREBASE_CREDENTIALS'), // Optional
],
```

**Environment Variables Needed:**
```env
FIREBASE_PROJECT_ID=your-firebase-project
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg
```

---

### 4. **API Controller**
**File:** `app/Http/Controllers/Api/AuthController.php`

**New Method:** `firebaseGoogleAuth()`
- Accepts Firebase ID token
- Verifies with Firebase servers
- Extracts Google ID from Firebase token
- Links accounts via Google ID matching
- Creates or updates user
- Returns API access token

**New Helper Methods:**
- `verifyFirebaseToken()` - Verifies token with Firebase REST API
- `verifyFirebaseTokenJWT()` - Fallback JWT verification

---

### 5. **API Routes**
**File:** `routes/api.php`

**New Route:**
```php
Route::post('auth/firebase/google', [AuthController::class, 'firebaseGoogleAuth']);
```

**Full Endpoint:** `POST /api/v1/auth/firebase/google`

---

### 6. **Documentation**

**Created/Updated:**
- `docs/Firebase_Google_Testing_Guide.md` - Complete testing guide
- `docs/api-documentation.md` - Added Firebase endpoint documentation
- `docs/IMPLEMENTATION_SUMMARY_Firebase_OAuth.md` - This file!

---

## 🔑 How Account Linking Works

### The Magic: Google ID Matching

**Database Structure:**
```
users table:
├── id
├── email
├── google_id ← Links web and mobile!
├── firebase_uid ← Only from Firebase
└── ...other fields
```

### Scenario 1: Web Login First
```
1. User logs in via web (Google OAuth)
   → Creates user with google_id: "123456789"

2. Same user logs in via mobile (Firebase)
   → Firebase token contains same google_id: "123456789"
   → API finds existing user by google_id
   → Updates user, adds firebase_uid
   → ✅ Same account!
```

### Scenario 2: Mobile Login First
```
1. User logs in via mobile (Firebase)
   → Creates user with google_id: "123456789" + firebase_uid

2. Same user logs in via web (Google OAuth)
   → Google token contains same google_id: "123456789"
   → API finds existing user by google_id
   → ✅ Same account!
```

### Code Logic (Simplified):
```php
// Find user by email OR google_id OR firebase_uid
$existingUser = User::where('email', $email)
    ->orWhere('google_id', $googleId)
    ->orWhere('firebase_uid', $firebaseUid)
    ->first();

// Update or create with both IDs
User::updateOrCreate(
    ['email' => $email],
    [
        'google_id' => $googleId,      // Links web and mobile
        'firebase_uid' => $firebaseUid, // Firebase-specific
        // ...other fields
    ]
);
```

---

## 🚀 API Endpoints Summary

### For Web (Direct Google OAuth)
**Endpoint:** `POST /api/v1/auth/social/google`

**Request:**
```json
{
  "id_token": "GOOGLE_ID_TOKEN"
}
```

**Token Source:** Google Sign-In SDK

---

### For Mobile (Firebase)
**Endpoint:** `POST /api/v1/auth/firebase/google`

**Request:**
```json
{
  "id_token": "FIREBASE_ID_TOKEN"
}
```

**Token Source:** Firebase Authentication

---

## 🔐 Security Features

✅ **Token Verification:**
- Web: Verified with Google's `oauth2.googleapis.com/tokeninfo`
- Mobile: Verified with Firebase's `identitytoolkit.googleapis.com`

✅ **No Duplicate Accounts:**
- Google ID extracted from both token types
- Automatic linking via database lookup

✅ **Email Verification:**
- Google verifies email
- Firebase verifies email
- Auto-set `email_verified_at`

✅ **Secure Token Generation:**
- Laravel Sanctum tokens for API access
- Tokens tied to specific user accounts

---

## 📱 Mobile Integration Examples

### React Native
```javascript
import auth from '@react-native-firebase/auth';

// 1. Sign in with Firebase
const userCredential = await auth().signInWithCredential(googleCredential);

// 2. Get Firebase token
const firebaseToken = await userCredential.user.getIdToken();

// 3. Send to API
const response = await fetch('/api/v1/auth/firebase/google', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ id_token: firebaseToken })
});

const { data } = await response.json();
// data.token = API access token
```

### Flutter
```dart
import 'package:firebase_auth/firebase_auth.dart';

// 1. Sign in with Firebase
final userCredential = await FirebaseAuth.instance
    .signInWithCredential(googleCredential);

// 2. Get Firebase token
final firebaseToken = await userCredential.user!.getIdToken();

// 3. Send to API
final response = await http.post(
  Uri.parse('/api/v1/auth/firebase/google'),
  body: jsonEncode({'id_token': firebaseToken}),
);

final data = jsonDecode(response.body);
// data['data']['token'] = API access token
```

---

## 🧪 Testing

### Quick Test Commands

**1. Test Web Login:**
```bash
curl -X POST http://localhost/api/v1/auth/social/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "GOOGLE_ID_TOKEN"}'
```

**2. Test Mobile Login:**
```bash
curl -X POST http://localhost/api/v1/auth/firebase/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "FIREBASE_ID_TOKEN"}'
```

**3. Verify Account Linking:**
```bash
php artisan tinker
```
```php
$user = \App\Models\User::where('email', 'test@gmail.com')->first();
$user->google_id;    // Should have value
$user->firebase_uid; // Should have value (if logged in via mobile)
```

**See detailed testing guide:** `docs/Firebase_Google_Testing_Guide.md`

---

## ⚙️ Configuration Required

### In `.env` file:

```env
# Firebase Configuration (for mobile)
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg

# Google OAuth (for web)
GOOGLE_CLIENT_ID=123456789-abc.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abc123
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

### In Firebase Console:

1. Enable Google Sign-In provider
2. Add your OAuth client ID (same as `GOOGLE_CLIENT_ID`)
3. Configure OAuth consent screen
4. Add authorized domains

---

## 📊 Database Changes

### Before:
```sql
users:
  - id
  - email
  - google_id
  - apple_id
  - ... (other fields)
```

### After:
```sql
users:
  - id
  - email
  - google_id ← Links both web and mobile
  - firebase_uid ← NEW! Firebase-specific
  - apple_id
  - ... (other fields)
```

**Migration to run:**
```bash
php artisan migrate
```

---

## 🎓 Key Concepts

### Google ID (the linker)
- Unique identifier from Google
- **Same for both OAuth and Firebase**
- Format: Long number like "108234567890"
- Found in:
  - Google token: `sub` field
  - Firebase token: `firebase.identities.google.com[0]`

### Firebase UID
- Unique identifier from Firebase
- Different from Google ID
- Format: Alphanumeric like "abc123xyz"
- Found in Firebase token: `user_id` or `sub` field

### Why Both?
- `google_id`: Links web and mobile logins
- `firebase_uid`: Enables Firebase-specific features (push notifications, analytics, etc.)

---

## 🐛 Common Issues & Solutions

### Issue: User Created Twice

**Symptom:** Different user accounts for web and mobile

**Cause:** Google ID not being extracted properly

**Solution:**
- Verify Firebase token contains `firebase.identities.google.com`
- Check that same Google account is used
- Inspect token at https://jwt.io/

---

### Issue: "Invalid Firebase token"

**Cause:** Token expired or wrong project

**Solution:**
- Get fresh token (they expire in 1 hour)
- Verify `FIREBASE_PROJECT_ID` matches Firebase Console
- Check Firebase API key is correct

---

### Issue: "Missing required user information"

**Cause:** Token doesn't contain email or user_id

**Solution:**
- Ensure Google provider is enabled in Firebase
- Request email scope in Firebase auth
- Check Firebase authentication settings

---

## 📝 Notes

### Firebase Admin SDK Not Required!

This implementation uses Firebase REST API for token verification, which doesn't require the Firebase Admin SDK package (`kreait/firebase-php`).

**Why?**
- ✅ Simpler setup (no composer package needed)
- ✅ Works with just API key
- ✅ Sufficient for token verification

**When you might need Admin SDK:**
- Advanced Firebase features
- Server-side Firebase operations
- Custom token generation

---

## ✅ Checklist for Production

Before deploying:

- [ ] Run database migration
- [ ] Add Firebase config to production `.env`
- [ ] Enable Google Sign-In in Firebase Console
- [ ] Configure OAuth consent screen
- [ ] Add production domain to Firebase authorized domains
- [ ] Test account linking with real accounts
- [ ] Verify HTTPS is enabled
- [ ] Update mobile app with production API URL
- [ ] Test both web and mobile login flows
- [ ] Monitor logs for authentication errors

---

## 📚 Related Documentation

- **Testing Guide:** `docs/Firebase_Google_Testing_Guide.md`
- **API Documentation:** `docs/api-documentation.md` (search "Firebase")
- **Google OAuth Testing:** `docs/Google_OAuth_Testing_Guide.md`
- **Quick Start:** `QUICKSTART_GOOGLE_OAUTH_TESTING.md`

---

## 🎉 Summary

**What you can now do:**

✅ Users can log in via **web** (Google OAuth)  
✅ Users can log in via **mobile** (Firebase)  
✅ Same Google account = **Same user account**  
✅ No duplicate users!  
✅ Automatic account linking  
✅ Secure token verification  
✅ Works with existing Google OAuth implementation  

**Next steps:**

1. Run `php artisan migrate`
2. Add Firebase config to `.env`
3. Test with mobile app
4. Deploy to production!

---

**Implementation completed successfully!** 🚀


