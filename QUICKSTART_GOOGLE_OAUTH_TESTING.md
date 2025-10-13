# 🚀 Quick Start: Test Google OAuth in 5 Minutes

## Prerequisites
Make sure your `.env` file has:
```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
```

---

## ⚡ Fastest Method: Use the Test Page

### Step 1: Configure the Test Page
```bash
# Edit this file:
nano public/test-google-login.html

# Find line 91 and replace with your actual Google Client ID:
data-client_id="YOUR_GOOGLE_CLIENT_ID"
# Change to:
data-client_id="123456789-abc.apps.googleusercontent.com"
```

### Step 2: Access the Test Page
```
http://localhost/test-google-login.html
```

### Step 3: Login and Test
1. Click "Sign in with Google"
2. Authenticate with your Google account
3. The page will display your ID token
4. Click "🧪 Test API" button
5. ✅ Done! You should see the API response with user + token

---

## 🔧 Alternative: Manual Testing with cURL

### Get ID Token from Google OAuth Playground:

1. Visit: https://developers.google.com/oauthplayground/
2. Click ⚙️ (settings) → Check "Use your own OAuth credentials"
3. Enter your Client ID and Secret
4. Select scopes: `email`, `profile`, `openid`
5. Click "Authorize APIs" → Login
6. Click "Exchange authorization code for tokens"
7. Copy the `id_token`

### Test with cURL:
```bash
curl -X POST http://localhost/api/v1/auth/social/google \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "id_token": "PASTE_YOUR_ID_TOKEN_HERE"
  }'
```

### Expected Response:
```json
{
  "success": true,
  "message": "Google authentication successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Your Name",
      "email": "your@gmail.com",
      "role": "user",
      "status": "active",
      "email_verified_at": "2025-10-13T..."
    },
    "token": "1|randomAccessToken123...",
    "token_type": "Bearer"
  }
}
```

---

## ✅ Verify in Database

```bash
php artisan tinker
```

```php
// Check if user was created
$user = \App\Models\User::where('email', 'your@gmail.com')->first();

// Verify fields
$user->google_id;           // Should have a value like "108234567890"
$user->email_verified_at;   // Should not be null
$user->role;                // Should be 'user'
```

---

## 📖 Full Documentation

For detailed guides, see:
- **Full Testing Guide:** `docs/Google_OAuth_Testing_Guide.md`
- **API Documentation:** `docs/api-documentation.md` (search for "Google")
- **Postman Collection:** `docs/Skillo_API_Collection.postman_collection.json`

---

## 🐛 Troubleshooting

**Error: "Invalid Google token"**
- Token expired (they expire in 1 hour)
- Get a fresh token from OAuth Playground

**Error: "Missing required user information"**
- Make sure scopes include `email`, `profile`, `openid`

**CORS Error (in browser)**
- Add `http://localhost` to authorized origins in Google Console

**Network Error**
- Check if Laravel server is running
- Verify route: `php artisan route:list | grep google`

---

## 🔐 Security Note

The backend automatically:
✅ Verifies token with Google's servers
✅ Validates token signature and expiration  
✅ Checks token audience (client_id)
✅ Creates user securely with verified email

You can't fake a Google login! The token MUST be issued by Google.


