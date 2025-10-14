# Firebase Apple Authentication Implementation

## ✅ What Was Implemented

This document describes the Firebase Apple Authentication implementation for mobile apps with automatic account linking to web Apple OAuth.

---

## 🎯 Problem Solved

**Challenge:** Users can log in via:
- **Web:** Apple OAuth (direct)
- **Mobile:** Firebase Authentication (with Apple provider)

**Solution:** Both methods now link to the **same user account** automatically!

---

## 📦 Files Created/Modified

### 1. **API Controller**
**File:** `app/Http/Controllers/Api/AuthController.php`

**New Method:** `firebaseAppleAuth()`
- Accepts Firebase ID token
- Verifies with Firebase servers
- Extracts Apple ID from Firebase token
- Links accounts via Apple ID matching
- Creates or updates user
- Returns API access token

---

### 2. **API Routes**
**File:** `routes/api.php`

**New Route:**
```php
Route::post('auth/firebase/apple', [AuthController::class, 'firebaseAppleAuth']);
```

**Full Endpoint:** `POST /api/v1/auth/firebase/apple`

---

### 3. **Postman Collection**
**File:** `docs/Skillo_API_Collection.postman_collection.json`

**Added:**
- Firebase Apple Login (Mobile) endpoint
- Sample request/response examples
- Error response scenarios

---

## 🔑 How Account Linking Works

### The Magic: Apple ID Matching

**Database Structure:**
```
users table:
├── id
├── email
├── apple_id ← Links web and mobile!
├── firebase_uid ← Only from Firebase
└── ...other fields
```

### Scenario 1: Web Login First
```
1. User logs in via web (Apple OAuth)
   → Creates user with apple_id: "001234.abcdef123456789.0123"

2. Same user logs in via mobile (Firebase)
   → Firebase token contains same apple_id: "001234.abcdef123456789.0123"
   → API finds existing user by apple_id
   → Updates user, adds firebase_uid
   → ✅ Same account!
```

### Scenario 2: Mobile Login First
```
1. User logs in via mobile (Firebase)
   → Creates user with apple_id: "001234.abcdef123456789.0123" + firebase_uid

2. Same user logs in via web (Apple OAuth)
   → Apple token contains same apple_id: "001234.abcdef123456789.0123"
   → API finds existing user by apple_id
   → ✅ Same account!
```

### Code Logic (Simplified):
```php
// Find user by email OR apple_id OR firebase_uid
$existingUser = User::where('email', $email)
    ->orWhere('apple_id', $appleId)
    ->orWhere('firebase_uid', $firebaseUid)
    ->first();

// Update or create with both IDs
User::updateOrCreate(
    ['email' => $email],
    [
        'apple_id' => $appleId,        // Links web and mobile
        'firebase_uid' => $firebaseUid, // Firebase-specific
        // ...other fields
    ]
);
```

---

## 🚀 API Endpoints Summary

### For Web (Direct Apple OAuth)
**Endpoint:** `POST /api/v1/auth/social/apple`

**Request:**
```json
{
  "id_token": "APPLE_ID_TOKEN"
}
```

**Token Source:** Apple Sign-In SDK

---

### For Mobile (Firebase)
**Endpoint:** `POST /api/v1/auth/firebase/apple`

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
- Web: Verified with Apple's servers
- Mobile: Verified with Firebase's `identitytoolkit.googleapis.com`

✅ **No Duplicate Accounts:**
- Apple ID extracted from both token types
- Automatic linking via database lookup

✅ **Email Verification:**
- Apple verifies email
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
import { appleAuth } from '@invertase/react-native-apple-authentication';

// 1. Sign in with Apple via Firebase
const appleAuthRequestResponse = await appleAuth.performRequest({
  requestedOperation: appleAuth.Operation.LOGIN,
  requestedScopes: [appleAuth.Scope.EMAIL, appleAuth.Scope.FULL_NAME],
});

const { identityToken, nonce } = appleAuthRequestResponse;
const appleCredential = auth.AppleAuthProvider.credential(identityToken, nonce);

// 2. Sign in to Firebase
const userCredential = await auth().signInWithCredential(appleCredential);

// 3. Get Firebase token
const firebaseToken = await userCredential.user.getIdToken();

// 4. Send to API
const response = await fetch('/api/v1/auth/firebase/apple', {
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
import 'package:sign_in_with_apple/sign_in_with_apple.dart';

// 1. Sign in with Apple
final credential = await SignInWithApple.getAppleIDCredential(
  scopes: [
    AppleIDAuthorizationScopes.email,
    AppleIDAuthorizationScopes.fullName,
  ],
);

// 2. Create Firebase credential
final oAuthProvider = OAuthProvider('apple.com');
final authCredential = oAuthProvider.credential(
  idToken: credential.identityToken,
  accessToken: credential.authorizationCode,
);

// 3. Sign in to Firebase
final userCredential = await FirebaseAuth.instance
    .signInWithCredential(authCredential);

// 4. Get Firebase token
final firebaseToken = await userCredential.user!.getIdToken();

// 5. Send to API
final response = await http.post(
  Uri.parse('/api/v1/auth/firebase/apple'),
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
curl -X POST http://localhost/api/v1/auth/social/apple \
  -H "Content-Type: application/json" \
  -d '{"id_token": "APPLE_ID_TOKEN"}'
```

**2. Test Mobile Login:**
```bash
curl -X POST http://localhost/api/v1/auth/firebase/apple \
  -H "Content-Type: application/json" \
  -d '{"id_token": "FIREBASE_ID_TOKEN"}'
```

**3. Verify Account Linking:**
```bash
php artisan tinker
```
```php
$user = \App\Models\User::where('email', 'test@privaterelay.appleid.com')->first();
$user->apple_id;     // Should have value
$user->firebase_uid; // Should have value (if logged in via mobile)
```

---

## ⚙️ Configuration Required

### In `.env` file:

```env
# Firebase Configuration (for mobile)
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg

# Apple OAuth (for web - if implemented)
APPLE_CLIENT_ID=com.yourapp.service
APPLE_CLIENT_SECRET=your_apple_client_secret
APPLE_REDIRECT_URI=https://yourapp.com/auth/apple/callback
```

### In Firebase Console:

1. Enable Apple Sign-In provider
2. Configure Service ID
3. Add your domain to authorized domains
4. Set up Apple Developer account integration

---

## 📊 Database Schema

The `users` table already has the required fields:
- `apple_id` - For Apple authentication linking
- `firebase_uid` - For Firebase-specific features

No new migration needed!

---

## 🎓 Key Concepts

### Apple ID (the linker)
- Unique identifier from Apple
- **Same for both OAuth and Firebase**
- Format: Like "001234.abcdef123456789.0123"
- Found in:
  - Apple token: `sub` field
  - Firebase token: `firebase.identities.apple.com[0]`

### Firebase UID
- Unique identifier from Firebase
- Different from Apple ID
- Format: Alphanumeric like "abc123xyz"
- Found in Firebase token: `user_id` or `sub` field

### Why Both?
- `apple_id`: Links web and mobile logins
- `firebase_uid`: Enables Firebase-specific features (push notifications, analytics, etc.)

---

## 🐛 Common Issues & Solutions

### Issue: User Created Twice

**Symptom:** Different user accounts for web and mobile

**Cause:** Apple ID not being extracted properly

**Solution:**
- Verify Firebase token contains `firebase.identities.apple.com`
- Check that same Apple account is used
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
- Ensure Apple provider is enabled in Firebase
- Request email scope in Apple auth
- Check Firebase authentication settings
- Note: Apple may use private relay emails

---

## 📝 Notes

### Apple Private Relay Emails

Apple allows users to hide their email using private relay. The email will be in format:
`random_string@privaterelay.appleid.com`

Your app should handle these emails normally - they work just like regular emails.

### Firebase Admin SDK Not Required!

This implementation uses Firebase REST API for token verification, which doesn't require the Firebase Admin SDK package.

---

## ✅ Checklist for Production

Before deploying:

- [ ] Enable Apple Sign-In in Firebase Console
- [ ] Configure Apple Services ID
- [ ] Add production domain to Apple Developer account
- [ ] Add production domain to Firebase authorized domains
- [ ] Test account linking with real accounts
- [ ] Verify HTTPS is enabled
- [ ] Update mobile app with production API URL
- [ ] Test both web and mobile login flows
- [ ] Handle Apple private relay emails properly
- [ ] Monitor logs for authentication errors

---

## 📚 Related Documentation

- **Firebase Google Auth:** `docs/IMPLEMENTATION_SUMMARY_Firebase_OAuth.md`
- **API Documentation:** `docs/api-documentation.md`
- **Postman Collection:** `docs/Skillo_API_Collection.postman_collection.json`

---

## 🎉 Summary

**What you can now do:**

✅ Users can log in via **web** (Apple OAuth)  
✅ Users can log in via **mobile** (Firebase)  
✅ Same Apple account = **Same user account**  
✅ No duplicate users!  
✅ Automatic account linking  
✅ Secure token verification  
✅ Works with existing Apple OAuth implementation  
✅ Handles Apple private relay emails  

**API Endpoints:**

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/api/v1/auth/social/apple` | Web Apple OAuth |
| POST | `/api/v1/auth/firebase/apple` | Mobile Firebase Apple |

---

**Implementation completed successfully!** 🚀

