# Skillo API Postman Collection Setup Guide

## Overview
This guide will help you set up and use the Skillo Mobile API Postman collection for testing and development.

## Files Included
- `Skillo_API_Collection.postman_collection.json` - Complete API collection
- `Skillo_API_Environment.postman_environment.json` - Environment variables
- `Postman_Setup_Guide.md` - This setup guide

## Setup Instructions

### 1. Import Collection and Environment

1. **Open Postman**
2. **Import Collection:**
   - Click "Import" button
   - Select `Skillo_API_Collection.postman_collection.json`
   - Click "Import"

3. **Import Environment:**
   - Click "Import" button
   - Select `Skillo_API_Environment.postman_environment.json`
   - Click "Import"

4. **Select Environment:**
   - In the top-right corner, select "Skillo API Environment" from the environment dropdown

### 2. Configure Environment Variables

Update the following variables in the environment:

| Variable | Description | Example Value |
|----------|-------------|---------------|
| `base_url` | API base URL | `http://localhost:8000` |
| `user_email` | Test user email | `user@test.com` |
| `user_password` | Test user password | `Password123` |
| `auth_token` | Will be set automatically | (Leave empty) |

### 3. Testing the API

#### Step 1: Test Server Connection
1. Go to **Onboarding - Public** → **Get Categories**
2. Click "Send"
3. You should see a successful response with categories data

#### Step 2: Register a New User
1. Go to **Authentication** → **Register User**
2. Update the request body with your test data:
   ```json
   {
     "name": "Test User",
     "email": "test@example.com",
     "password": "Password123",
     "password_confirmation": "Password123",
     "role": "user",
     "gdpr_consent": true
   }
   ```
3. Click "Send"
4. Copy the `token` from the response

#### Step 3: Set Authentication Token
1. Go to the environment variables
2. Paste the token into the `auth_token` variable
3. Save the environment

#### Step 4: Test Protected Endpoints
1. Go to **Authentication** → **Get Current User**
2. Click "Send"
3. You should see your user information

#### Step 5: Test Onboarding Flow
1. **Save Category Selection:**
   - Go to **Onboarding - Protected** → **Save Category Selection**
   - Try selecting "Others" category with a custom name:
     ```json
     {
       "category_id": 6,
       "custom_category_name": "My Custom Skill"
     }
     ```

2. **Save Education Type:**
   - Go to **Onboarding - Protected** → **Save Education Type**
   - Choose between "in-person" or "online"

3. **Check Onboarding Status:**
   - Go to **Onboarding - Protected** → **Get Onboarding Status**
   - See your progress and next steps

## Collection Structure

### Authentication
- **Register User** - Create new user account
- **Login User** - Authenticate and get token
- **Get Current User** - Get user information
- **Logout User** - Invalidate token
- **Refresh Token** - Get new token
- **Forgot Password** - Request password reset
- **Reset Password** - Reset password with token

### Onboarding - Public
- **Get Categories** - List all categories with subcategories
- **Get Countries** - List available countries

### Onboarding - Protected
- **Save Category Selection** - Save user's category preference
- **Save Education Type** - Save education type (in-person/online)
- **Save Location** - Save location for in-person education
- **Save Online Options** - Save online education preferences
- **Get Onboarding Status** - Check onboarding progress
- **Get User Preferences** - Get saved user preferences
- **Get My Custom Subcategories** - Get user's custom subcategories

## Common Use Cases

### 1. Complete User Onboarding Flow
```
1. Register User
2. Login User (copy token to environment)
3. Get Categories
4. Save Category Selection (with custom subcategory)
5. Save Education Type
6. Save Location (if in-person) OR Save Online Options (if online)
7. Get Onboarding Status (should show complete)
8. Get User Preferences
```

### 2. Test Custom Subcategory Creation
```
1. Login User
2. Get Categories (see existing subcategories)
3. Save Category Selection with:
   - category_id: 6 (Others)
   - custom_category_name: "My Custom Skill"
4. Get My Custom Subcategories (see your custom subcategory)
5. Get Categories again (see your custom subcategory in the list)
```

### 3. Test Authentication Flow
```
1. Register User
2. Login User
3. Get Current User
4. Refresh Token
5. Get Current User (with new token)
6. Logout User
7. Get Current User (should fail with 401)
```

## Response Examples

### Successful Login Response
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 85,
      "name": "Test User",
      "email": "test@example.com",
      "role": "user",
      "status": "active",
      "email_verified_at": null
    },
    "token": "18|j3Kilo77cniDV90vuFsL1I3MCBExU0j6wVpafH5Ybdbe2938",
    "token_type": "Bearer"
  }
}
```

### Custom Subcategory Response
```json
{
  "success": true,
  "message": "Custom subcategories retrieved successfully",
  "data": {
    "custom_subcategories": [
      {
        "id": 20,
        "name": "My Custom Skill",
        "description": "Custom subcategory",
        "category": {
          "id": 6,
          "name": "Others"
        },
        "created_at": "2025-09-08T10:21:15.000000Z",
        "updated_at": "2025-09-08T10:21:15.000000Z"
      }
    ]
  }
}
```

## Error Handling

### Common Error Responses

#### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated",
  "errors": {
    "auth": "Authentication required"
  }
}
```

#### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

#### 500 Server Error
```json
{
  "success": false,
  "message": "Registration failed",
  "errors": {
    "server": "An error occurred during registration"
  }
}
```

## Tips for Testing

1. **Always set the auth_token** after login for protected endpoints
2. **Use the environment variables** to avoid hardcoding values
3. **Test the complete flow** from registration to onboarding completion
4. **Check the response structure** matches the API documentation
5. **Test error scenarios** like invalid tokens, validation errors, etc.

## Troubleshooting

### Common Issues

1. **401 Unauthorized on protected endpoints:**
   - Make sure you're logged in and have copied the token
   - Check that the token is set in the environment variables

2. **Connection refused:**
   - Make sure the Laravel server is running (`php artisan serve`)
   - Check the base_url is correct

3. **Validation errors:**
   - Check the request body format
   - Ensure all required fields are included
   - Verify data types match the API requirements

4. **Empty responses:**
   - Check the server logs for errors
   - Verify the API routes are properly registered

## Support

For issues or questions about the API:
1. Check the API documentation in `api-documentation.md`
2. Review the server logs
3. Test with the provided examples
4. Verify the database has the required data
