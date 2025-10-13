# Postman Collection Update - Firebase Google Authentication

## ✅ What Was Updated

The Postman collection has been updated to include the new Firebase Google authentication endpoint with complete request/response examples.

---

## 📦 Updated File

**File:** `docs/Skillo_API_Collection.postman_collection.json`

---

## 🆕 New Request Added

### **Firebase Google Login (Mobile)**

**Endpoint:** `POST /api/v1/auth/firebase/google`

**Location in Collection:**
```
Skillo Mobile API
└── Authentication
    ├── Register User
    ├── Login User
    ├── Get Current User
    ├── Logout User
    ├── Refresh Token
    ├── Forgot Password
    ├── Reset Password
    ├── Google Login (Web/Direct OAuth) ← Updated name
    ├── Firebase Google Login (Mobile) ← NEW!
    └── Apple Login (Mobile)
```

**Request Body:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjE2..."
}
```

**Description:**
> Authenticate user with Firebase ID token from mobile app (Firebase Authentication with Google provider). Automatically links accounts with web Google OAuth - same Google account gets same user record. The mobile app should use Firebase Authentication with Google Sign-In to obtain the Firebase ID token and send it to this endpoint. Returns user info and Bearer token for API access.

---

## 📋 Sample Responses Included

### 1. Success Response (200 OK)
```json
{
  "success": true,
  "message": "Firebase authentication successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "status": "active",
      "email_verified_at": "2025-10-13T10:30:00.000000Z"
    },
    "token": "1|abcdef123456789...",
    "token_type": "Bearer"
  }
}
```

### 2. Invalid Token (401 Unauthorized)
```json
{
  "success": false,
  "message": "Invalid Firebase token",
  "errors": {
    "id_token": "The provided Firebase token is invalid or expired"
  }
}
```

### 3. Missing User Info (400 Bad Request)
```json
{
  "success": false,
  "message": "Unable to retrieve user information from Firebase",
  "errors": {
    "firebase": "Missing required user information"
  }
}
```

---

## 🔄 Existing Request Updated

### **Google Login (Web/Direct OAuth)** (Previously: "Google Login (Mobile)")

**What changed:**
- **Name updated** to clarify it's for web/direct OAuth
- **Description updated** to mention automatic linking with Firebase logins

**New Description:**
> Authenticate user with Google ID token (Direct Google OAuth - for web applications). The web app should use Google Sign-In SDK to obtain the Google ID token and send it to this endpoint. Automatically links with mobile Firebase logins using same Google account. Returns user info and Bearer token for API access.

---

## 📝 Authentication Folder Description Updated

**Old:**
> Authentication endpoints for user registration, login, logout, password management, and social authentication (Google, Apple)

**New:**
> Authentication endpoints for user registration, login, logout, password management, and social authentication. Supports Google OAuth (web), Firebase Google Authentication (mobile with automatic account linking), and Apple Sign-In.

---

## 🧪 How to Use in Postman

### Step 1: Import the Updated Collection

1. Open Postman
2. Click **Import**
3. Select `docs/Skillo_API_Collection.postman_collection.json`
4. Click **Import** (if already imported, it will update)

### Step 2: Import Environment (if not already)

1. Click **Import**
2. Select `docs/Skillo_API_Environment.postman_environment.json`
3. Select the environment from the dropdown (top-right)

### Step 3: Get Firebase ID Token

You need a real Firebase ID token to test. Get one from:

**Option A: Mobile App**
```javascript
// React Native / Flutter
const firebaseToken = await auth().currentUser.getIdToken();
// Paste this token in Postman request
```

**Option B: Firebase Web SDK**
```html
<!-- See docs/Firebase_Google_Testing_Guide.md for complete example -->
<script>
  const token = await firebase.auth().currentUser.getIdToken();
  console.log(token); // Copy and paste in Postman
</script>
```

**Option C: OAuth Playground**
- See `docs/Firebase_Google_Testing_Guide.md` for detailed instructions

### Step 4: Test the Request

1. In Postman, navigate to:
   **Authentication** → **Firebase Google Login (Mobile)**

2. In the request body, replace the example token with your real token:
   ```json
   {
     "id_token": "YOUR_REAL_FIREBASE_TOKEN_HERE"
   }
   ```

3. Click **Send**

4. You should get a success response with user info and API token

5. The `auth_token` environment variable will be automatically set (if you have a test script)

---

## 🔑 Environment Variables

The existing environment variables are sufficient:

| Variable | Description | Example |
|----------|-------------|---------|
| `base_url` | API base URL | `http://localhost:8000` |
| `auth_token` | Bearer token from login | Auto-set after login |
| `user_id` | Current user ID | Auto-set after login |
| `user_email` | Test user email | `user@test.com` |
| `user_password` | Test user password | `Password123` |

**Note:** Firebase and Google tokens are obtained externally, so they're not stored as environment variables. You paste them directly into the request body.

---

## 🎯 Testing Both Methods

### Test Web Google Login:
1. Use: **Google Login (Web/Direct OAuth)**
2. Get Google ID token from OAuth Playground
3. Send request → Creates user with `google_id`

### Test Mobile Firebase Login (Same Google Account):
1. Use: **Firebase Google Login (Mobile)**
2. Get Firebase ID token from Firebase
3. Send request → Finds existing user by `google_id`
4. ✅ Same user, same ID!

### Verify Account Linking:
Check that both requests return the same `user.id` in the response.

---

## 📊 Collection Structure

```
Skillo Mobile API Collection
├── Authentication (9 requests)
│   ├── Standard Auth (5 requests)
│   │   ├── Register User
│   │   ├── Login User
│   │   ├── Get Current User
│   │   ├── Logout User
│   │   └── Refresh Token
│   ├── Password Management (2 requests)
│   │   ├── Forgot Password
│   │   └── Reset Password
│   └── Social Authentication (2 requests)
│       ├── Google Login (Web/Direct OAuth)
│       ├── Firebase Google Login (Mobile) ← NEW!
│       └── Apple Login (Mobile)
├── Onboarding - Public
├── Home Page Data
├── Course Details
├── Mentor Details
├── Reviews
├── Course Enrollment
├── Session Booking
└── ... (other endpoints)
```

---

## 🔗 Related Documentation

- **Implementation Guide:** `IMPLEMENTATION_COMPLETE.md`
- **Firebase Testing Guide:** `docs/Firebase_Google_Testing_Guide.md`
- **Environment Configuration:** `ENV_CONFIGURATION.md`
- **API Documentation:** `docs/api-documentation.md`

---

## ✅ Summary

**What's New:**
- ✅ Added Firebase Google Login endpoint
- ✅ Updated Google Login description
- ✅ Added 3 sample responses (success, invalid token, missing info)
- ✅ Updated folder description
- ✅ Complete request/response examples

**Ready to Use:**
- Import updated collection in Postman
- Get Firebase token from mobile app or web
- Test the endpoint
- Verify account linking works

---

## 💡 Pro Tips

### Tip 1: Save Tokens as Variables
After successful login, manually save the token:
```javascript
// In Postman Tests tab
pm.environment.set("auth_token", pm.response.json().data.token);
pm.environment.set("user_id", pm.response.json().data.user.id);
```

### Tip 2: Test Account Linking
1. Login via Web Google → Note the user ID
2. Login via Firebase Google (same account) → Compare user ID
3. Should be the same! ✅

### Tip 3: Token Expiration
Firebase tokens expire after 1 hour. If you get "Invalid token", get a fresh one.

---

**Postman Collection is ready for testing!** 🚀


