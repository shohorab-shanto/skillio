# Google OAuth Testing Guide

This guide explains how to test the Google OAuth login functionality for the Skillo API.

## Overview

The API endpoint `POST /api/auth/google` accepts a Google ID token and creates/logs in users automatically.

**Endpoint:** `POST /api/auth/google`

**Request Body:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6..."
}
```

---

## Prerequisites

Before testing, ensure you have:

1. **Google OAuth Client ID and Secret** configured in `.env`:
   ```env
   GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-client-secret
   GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
   ```

2. **Google Cloud Console Setup:**
   - Go to: https://console.cloud.google.com/
   - Create a project (or select existing)
   - Enable "Google+ API" or "Google Identity"
   - Create OAuth 2.0 credentials
   - Add authorized redirect URIs

---

## Method 1: Using the Test Page (Easiest) ⭐

We've created a test page for you to easily get Google ID tokens.

### Steps:

1. **Edit the test file:**
   ```bash
   # Open: public/test-google-login.html
   # Replace 'YOUR_GOOGLE_CLIENT_ID' with your actual Client ID
   ```

2. **Access the test page:**
   ```
   http://localhost/test-google-login.html
   ```

3. **Click "Sign in with Google"**
   - Authenticate with your Google account
   - The page will display the ID token
   - Click "Test API" to automatically test the endpoint
   - Or copy the token for manual testing

4. **Use the token in Postman/cURL:**
   ```bash
   curl -X POST http://localhost/api/auth/google \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "id_token": "YOUR_ID_TOKEN_HERE"
     }'
   ```

---

## Method 2: Google OAuth Playground

This method gets you a real ID token without writing code.

### Steps:

1. **Visit Google OAuth 2.0 Playground:**
   ```
   https://developers.google.com/oauthplayground/
   ```

2. **Configure Settings (⚙️ icon):**
   - Check ✅ "Use your own OAuth credentials"
   - OAuth Client ID: `your-client-id.apps.googleusercontent.com`
   - OAuth Client Secret: `your-client-secret`
   - Click "Close"

3. **Select Scopes:**
   - In "Step 1: Select & authorize APIs"
   - Find "Google OAuth2 API v2"
   - Select:
     - ✅ `https://www.googleapis.com/auth/userinfo.email`
     - ✅ `https://www.googleapis.com/auth/userinfo.profile`
     - ✅ `openid`
   - Click "Authorize APIs"

4. **Authenticate:**
   - Login with your Google account
   - Grant permissions

5. **Get the Token:**
   - In "Step 2", click "Exchange authorization code for tokens"
   - Copy the `id_token` from the response

6. **Test the API:**
   ```bash
   curl -X POST http://localhost/api/auth/google \
     -H "Content-Type: application/json" \
     -d '{"id_token": "PASTE_ID_TOKEN_HERE"}'
   ```

---

## Method 3: Using Postman Collection

### Import the Postman Collection:

1. Open Postman
2. Import: `docs/Skillo_API_Collection.postman_collection.json`
3. Import environment: `docs/Skillo_API_Environment.postman_environment.json`

### Add Google OAuth Request:

Create a new request in Postman:

**Request Name:** Google OAuth Login

**Method:** POST

**URL:** `{{base_url}}/api/auth/google`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**
```json
{
  "id_token": "{{google_id_token}}"
}
```

**Pre-request Script:** (Optional - to get fresh token)
```javascript
// This would require additional setup with Google OAuth
// For now, manually paste the ID token
```

---

## Method 4: Mobile App Simulation (React Native / Flutter)

If you're testing for mobile apps:

### React Native (using @react-native-google-signin/google-signin):

```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// Configure
GoogleSignin.configure({
  webClientId: 'your-client-id.apps.googleusercontent.com',
});

// Sign in
async function signInWithGoogle() {
  await GoogleSignin.hasPlayServices();
  const userInfo = await GoogleSignin.signIn();
  const { idToken } = userInfo;
  
  // Send to your API
  const response = await fetch('http://your-api.com/api/auth/google', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_token: idToken })
  });
  
  const data = await response.json();
  console.log(data); // Contains user + token
}
```

### Flutter (using google_sign_in package):

```dart
import 'package:google_sign_in/google_sign_in.dart';
import 'package:http/http.dart' as http;

final GoogleSignIn _googleSignIn = GoogleSignIn(
  scopes: ['email', 'profile'],
);

Future<void> signInWithGoogle() async {
  final GoogleSignInAccount? account = await _googleSignIn.signIn();
  final GoogleSignInAuthentication auth = await account!.authentication;
  
  // Send to your API
  final response = await http.post(
    Uri.parse('http://your-api.com/api/auth/google'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({'id_token': auth.idToken}),
  );
  
  print(response.body);
}
```

---

## Testing Scenarios

### Test Case 1: First-Time User
**Expected:** Creates new user account

```bash
curl -X POST http://localhost/api/auth/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "NEW_USER_TOKEN"}'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Google authentication successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@gmail.com",
      "role": "user",
      "status": "active",
      "email_verified_at": "2025-10-13T12:00:00.000000Z"
    },
    "token": "1|abcdefgh123456789",
    "token_type": "Bearer"
  }
}
```

### Test Case 2: Existing User
**Expected:** Logs in existing user, updates google_id if needed

```bash
# Use same token from Test Case 1
# Should return same user but new token
```

### Test Case 3: Invalid Token
**Expected:** Error response

```bash
curl -X POST http://localhost/api/auth/google \
  -H "Content-Type: application/json" \
  -d '{"id_token": "invalid_token_123"}'
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Invalid Google token",
  "errors": {
    "id_token": "The provided Google token is invalid or expired"
  }
}
```

### Test Case 4: Expired Token
**Expected:** Error response (tokens expire after 1 hour)

```bash
# Use an old token from yesterday
# Should return 401 error
```

---

## Verifying Token Manually

You can decode and verify any ID token at:
```
https://jwt.io/
```

**What to check:**
1. **Algorithm:** Should be `RS256`
2. **Issuer (iss):** Should be `https://accounts.google.com`
3. **Audience (aud):** Should match your `GOOGLE_CLIENT_ID`
4. **Expiration (exp):** Should be in the future (Unix timestamp)
5. **Subject (sub):** This is the `google_id`
6. **Email:** User's email
7. **Email Verified:** Should be `true`

---

## Debugging Tips

### Check if Google verification is working:

```bash
# Direct verification with Google
curl "https://oauth2.googleapis.com/tokeninfo?id_token=YOUR_ID_TOKEN"
```

**Valid Response:**
```json
{
  "iss": "https://accounts.google.com",
  "sub": "108234567890",
  "email": "user@gmail.com",
  "email_verified": "true",
  "name": "John Doe",
  "picture": "https://...",
  "aud": "your-client-id.apps.googleusercontent.com",
  "exp": "1697234567",
  "iat": "1697230967"
}
```

### Common Issues:

1. **"Invalid Google token"**
   - Token expired (they expire after 1 hour)
   - Token not for your Client ID
   - Network issue connecting to Google

2. **"Missing required user information"**
   - Token doesn't contain email or sub (google_id)
   - Scopes not properly requested

3. **CORS errors (in browser)**
   - Add your domain to Google Console authorized origins
   - Check Laravel CORS configuration

---

## Database Verification

After successful login, verify the user was created:

```bash
# SSH into server / open terminal
php artisan tinker
```

```php
// Check if user exists
$user = \App\Models\User::where('email', 'test@gmail.com')->first();

// Verify fields
$user->google_id; // Should be the 'sub' from token
$user->email_verified_at; // Should not be null
$user->role; // Should be 'user'
$user->status; // Should be 'active'
```

---

## Security Notes

⚠️ **Important:**

1. **Never commit ID tokens to git** - they're sensitive
2. **ID tokens expire** - typically after 1 hour
3. **Backend verification is critical** - never trust tokens without verifying with Google
4. **HTTPS in production** - always use HTTPS for OAuth flows
5. **Client ID matching** - ensure token's `aud` matches your Client ID

---

## Next Steps

After successful testing:

1. ✅ Verify user created in database
2. ✅ Test subsequent logins with same Google account
3. ✅ Test API endpoints using the returned Bearer token
4. ✅ Implement mobile app integration
5. ✅ Set up production Google OAuth credentials
6. ✅ Configure proper redirect URIs for production

---

## Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: `APP_DEBUG=true` in `.env`
3. Verify Google Console settings
4. Test token at https://jwt.io/
5. Direct verify with Google: `https://oauth2.googleapis.com/tokeninfo?id_token=...`


