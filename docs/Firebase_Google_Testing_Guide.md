# Firebase Google Authentication Testing Guide

This guide explains how to test the Firebase Google authentication for mobile apps and understand how it links with web Google OAuth.

## Overview

**Two Login Methods, One User Account:**

| Platform | Method | Endpoint | Token Type |
|----------|--------|----------|------------|
| **Web** | Google OAuth | `/api/v1/auth/social/google` | Google ID Token |
| **Mobile** | Firebase Auth | `/api/v1/auth/firebase/google` | Firebase ID Token |

**Key Feature:** Both methods use the **same Google ID** to link accounts automatically!

---

## How Account Linking Works

### Scenario 1: User logs in via Web first
```
1. User clicks "Login with Google" on website
2. Google OAuth → API stores user with google_id: "123456789"
3. Later, user installs mobile app
4. User signs in with Google via Firebase
5. Firebase token contains same google_id: "123456789"
6. API finds existing user by google_id
7. ✅ Same user account! Just adds firebase_uid
```

### Scenario 2: User logs in via Mobile first
```
1. User installs mobile app
2. Signs in with Google via Firebase
3. API creates user with google_id: "123456789" + firebase_uid
4. Later, user visits website
5. Logs in with Google OAuth (same Google account)
6. API finds existing user by google_id: "123456789"
7. ✅ Same user account!
```

### Database Structure:
```
users table:
├── id (primary key)
├── email (john@gmail.com)
├── google_id (123456789) ← Links both methods!
├── firebase_uid (abc123xyz) ← Only from Firebase
├── name, role, status, etc.
```

---

## Prerequisites

### 1. Firebase Project Setup

1. **Create Firebase Project:**
   - Go to: https://console.firebase.google.com/
   - Create new project or select existing
   - Enable Google Sign-In provider

2. **Get Firebase Configuration:**
   - Project Settings → General
   - Copy **Project ID**
   - Copy **Web API Key**

3. **Enable Google Provider:**
   - Authentication → Sign-in method
   - Enable Google
   - Configure OAuth consent screen

4. **Add to `.env`:**
   ```env
   FIREBASE_PROJECT_ID=your-project-id
   FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg
   
   # Also keep Google OAuth for web
   GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-client-secret
   ```

---

## Testing Methods

### Method 1: Using Mobile App (Real Implementation)

#### React Native Example:
```javascript
import auth from '@react-native-firebase/auth';
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// Configure Google Sign-In
GoogleSignin.configure({
  webClientId: 'your-client-id.apps.googleusercontent.com',
});

async function signInWithGoogle() {
  try {
    // 1. Sign in with Google
    const { idToken } = await GoogleSignin.signIn();
    
    // 2. Create Firebase credential
    const googleCredential = auth.GoogleAuthProvider.credential(idToken);
    
    // 3. Sign in to Firebase
    const userCredential = await auth().signInWithCredential(googleCredential);
    
    // 4. Get Firebase ID token
    const firebaseToken = await userCredential.user.getIdToken();
    
    // 5. Send to your API
    const response = await fetch('http://your-api.com/api/v1/auth/firebase/google', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_token: firebaseToken })
    });
    
    const data = await response.json();
    console.log('User:', data.data.user);
    console.log('API Token:', data.data.token);
    
    // 6. Store API token for future requests
    await AsyncStorage.setItem('api_token', data.data.token);
    
  } catch (error) {
    console.error('Login error:', error);
  }
}
```

#### Flutter Example:
```dart
import 'package:firebase_auth/firebase_auth.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:http/http.dart' as http;

Future<void> signInWithGoogle() async {
  try {
    // 1. Trigger Google Sign-In flow
    final GoogleSignInAccount? googleUser = await GoogleSignIn().signIn();
    
    // 2. Obtain auth details
    final GoogleSignInAuthentication googleAuth = 
        await googleUser!.authentication;
    
    // 3. Create Firebase credential
    final credential = GoogleAuthProvider.credential(
      accessToken: googleAuth.accessToken,
      idToken: googleAuth.idToken,
    );
    
    // 4. Sign in to Firebase
    final userCredential = 
        await FirebaseAuth.instance.signInWithCredential(credential);
    
    // 5. Get Firebase ID token
    final firebaseToken = await userCredential.user!.getIdToken();
    
    // 6. Send to your API
    final response = await http.post(
      Uri.parse('http://your-api.com/api/v1/auth/firebase/google'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'id_token': firebaseToken}),
    );
    
    final data = jsonDecode(response.body);
    print('User: ${data['data']['user']}');
    print('API Token: ${data['data']['token']}');
    
    // 7. Store API token
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('api_token', data['data']['token']);
    
  } catch (error) {
    print('Login error: $error');
  }
}
```

---

### Method 2: Get Firebase Token Manually (For Testing)

You can get a Firebase token without writing mobile code:

#### Option A: Use Firebase Web SDK

Create a test HTML file:

```html
<!DOCTYPE html>
<html>
<head>
  <title>Firebase Auth Test</title>
  <script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-auth-compat.js"></script>
</head>
<body>
  <h1>Firebase Google Auth Test</h1>
  <button onclick="signIn()">Sign in with Google</button>
  <div id="result"></div>
  
  <script>
    // Replace with your Firebase config
    const firebaseConfig = {
      apiKey: "AIzaSyC1234567890abcdefg",
      authDomain: "your-project.firebaseapp.com",
      projectId: "your-project-id"
    };
    
    firebase.initializeApp(firebaseConfig);
    
    async function signIn() {
      const provider = new firebase.auth.GoogleAuthProvider();
      
      try {
        const result = await firebase.auth().signInWithPopup(provider);
        const token = await result.user.getIdToken();
        
        document.getElementById('result').innerHTML = `
          <h3>Firebase ID Token:</h3>
          <textarea style="width:100%;height:200px">${token}</textarea>
          <button onclick="testAPI('${token}')">Test API</button>
        `;
      } catch (error) {
        console.error(error);
        alert('Error: ' + error.message);
      }
    }
    
    async function testAPI(token) {
      const response = await fetch('http://localhost/api/v1/auth/firebase/google', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_token: token })
      });
      
      const data = await response.json();
      alert('API Response:\n' + JSON.stringify(data, null, 2));
    }
  </script>
</body>
</html>
```

#### Option B: Use Firebase CLI

```bash
# Install Firebase CLI
npm install -g firebase-tools

# Login
firebase login

# Get token (this gets an access token, not ID token - for reference)
firebase login:ci
```

---

### Method 3: Testing with cURL (Using Token)

Once you have a Firebase ID token:

```bash
curl -X POST http://localhost/api/v1/auth/firebase/google \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "id_token": "YOUR_FIREBASE_ID_TOKEN_HERE"
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
      "status": "active",
      "email_verified_at": "2025-10-13T..."
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

---

## Testing Account Linking

### Test Case 1: Web → Mobile (Same Google Account)

**Step 1: Login via Web**
```bash
# Get Google ID token (see Google_OAuth_Testing_Guide.md)
curl -X POST http://localhost/api/v1/auth/social/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "GOOGLE_ID_TOKEN"}'

# Response includes user with google_id
```

**Step 2: Login via Firebase (Same Google Account)**
```bash
# Get Firebase ID token
curl -X POST http://localhost/api/v1/auth/firebase/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "FIREBASE_ID_TOKEN"}'

# Response should show SAME user ID!
```

**Step 3: Verify in Database**
```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'test@gmail.com')->first();

// Check fields
$user->id;           // Same ID for both logins
$user->google_id;    // Google ID (from both methods)
$user->firebase_uid; // Firebase UID (only from Firebase)
```

---

### Test Case 2: Mobile → Web (Same Google Account)

**Step 1: Login via Firebase**
```bash
curl -X POST http://localhost/api/v1/auth/firebase/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "FIREBASE_ID_TOKEN"}'
```

**Step 2: Login via Web (Same Google Account)**
```bash
curl -X POST http://localhost/api/v1/auth/social/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "GOOGLE_ID_TOKEN"}'
```

**Result:** Same user account, both methods work!

---

## Firebase Token Structure

A Firebase ID token (decoded) looks like:

```json
{
  "iss": "https://securetoken.google.com/your-project-id",
  "aud": "your-project-id",
  "auth_time": 1697234567,
  "user_id": "firebase_uid_abc123",
  "sub": "firebase_uid_abc123",
  "iat": 1697234567,
  "exp": 1697238167,
  "email": "john@gmail.com",
  "email_verified": true,
  "firebase": {
    "identities": {
      "google.com": ["123456789"],  ← This is the Google ID!
      "email": ["john@gmail.com"]
    },
    "sign_in_provider": "google.com"
  },
  "name": "John Doe",
  "picture": "https://..."
}
```

**Key Fields:**
- `user_id` / `sub`: Firebase UID (stored in `firebase_uid`)
- `firebase.identities.google.com[0]`: Google ID (stored in `google_id`)
- Both fields are used for account linking!

---

## Verification Process

Backend verification flow:

```
Firebase ID Token
      ↓
1. Send to Firebase API
   https://identitytoolkit.googleapis.com/v1/accounts:lookup
      ↓
2. Firebase validates token
      ↓
3. Returns user info
      ↓
4. Extract google_id from token claims
      ↓
5. Search database for:
   - email match OR
   - google_id match OR
   - firebase_uid match
      ↓
6. If found: Update user (add firebase_uid if missing)
   If not found: Create new user
      ↓
7. Return API access token
```

---

## Common Errors and Solutions

### Error: "Invalid Firebase token"

**Causes:**
- Token expired (Firebase tokens expire after 1 hour)
- Token not from your Firebase project
- Network issue

**Solutions:**
- Get fresh token
- Verify `FIREBASE_PROJECT_ID` in `.env`
- Check Firebase console for project ID

---

### Error: "Missing required user information"

**Causes:**
- Token doesn't contain email or user_id
- Token malformed

**Solutions:**
- Ensure Google provider is enabled in Firebase
- Request email scope in Firebase auth
- Check token payload

---

### Error: User created twice (web and mobile separate)

**Causes:**
- Google ID not extracted from Firebase token
- Different Google accounts used

**Solutions:**
- Check that Firebase token contains `firebase.identities.google.com`
- Verify same Google account used on both platforms
- Check database: `google_id` should match

---

## Environment Variables Summary

Add these to your `.env`:

```env
# Firebase Configuration (for mobile)
FIREBASE_PROJECT_ID=your-firebase-project
FIREBASE_WEB_API_KEY=AIzaSyC1234567890abcdefg

# Google OAuth (for web)
GOOGLE_CLIENT_ID=123456789-abc.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abc123
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

---

## Database Migration

Run the migration to add `firebase_uid` column:

```bash
php artisan migrate
```

This adds:
- `firebase_uid` (nullable string)
- Index on `firebase_uid` for fast lookups

---

## Security Notes

🔒 **Important Security Features:**

1. **Token Verification:** Firebase tokens are verified with Firebase servers
2. **No Duplicate Accounts:** Google ID links web and mobile logins
3. **Email Verification:** Firebase verifies email through Google
4. **Token Expiration:** Both Firebase and API tokens expire
5. **HTTPS Required:** Always use HTTPS in production

⚠️ **Development Note:**

The fallback JWT verification method (`verifyFirebaseTokenJWT`) is for development only. In production, always use Firebase API verification with proper signature validation.

---

## Production Checklist

Before going live:

- [ ] Add `FIREBASE_WEB_API_KEY` to production `.env`
- [ ] Enable Google Sign-In in Firebase Console
- [ ] Configure OAuth consent screen
- [ ] Add production domain to Firebase authorized domains
- [ ] Run migration on production database
- [ ] Test account linking with real Google accounts
- [ ] Verify HTTPS is enabled
- [ ] Set up proper error logging

---

## Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Firebase Console logs
3. Decode token at: https://jwt.io/
4. Verify Firebase project configuration
5. Test with fresh tokens (they expire!)

---

## Summary

✅ **Web users:** Use `/api/v1/auth/social/google` (Google OAuth)  
✅ **Mobile users:** Use `/api/v1/auth/firebase/google` (Firebase Auth)  
✅ **Same Google account:** Automatically links to same user  
✅ **No duplicate accounts:** Linked via `google_id`  
✅ **Secure:** Both methods verify tokens with official servers


