# Skillo Mobile API Documentation

## Overview
This API provides endpoints for the Skillo mobile application, covering user authentication and onboarding functionality.

## Base URL
```
https://yourdomain.com/api/v1
```

## Authentication
The API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header:
```
Authorization: Bearer {token}
```

## Response Format
All API responses follow this standard format:
```json
{
  "success": true|false,
  "message": "Human readable message",
  "data": {...},
  "errors": null|{...}
}
```

## Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `422` - Validation Error
- `500` - Internal Server Error

---

## Authentication Endpoints

### Register User
**POST** `/auth/register`

Register a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123",
  "password_confirmation": "Password123",
  "role": "user",
  "gdpr_consent": true
}
```

**Note:** Only users can register through the API. Mentors cannot self-register and must be created by administrators.

**Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "status": "active",
      "email_verified_at": null
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Login User
**POST** `/auth/login`

Authenticate user or mentor and return access token.

**Note:** Both users and mentors can login through this endpoint.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "Password123",
  "remember": false
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "status": "active",
      "email_verified_at": null
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Logout User
**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

### Get Current User
**GET** `/auth/me`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "User data retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "status": "active",
      "email_verified_at": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}
```

### Refresh Token
**POST** `/auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "2|def456...",
    "token_type": "Bearer"
  }
}
```

### Forgot Password
**POST** `/auth/forgot-password`

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Password reset link sent to your email"
}
```

### Reset Password
**POST** `/auth/reset-password`

**Request Body:**
```json
{
  "token": "reset_token_here",
  "email": "john@example.com",
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

---

## Onboarding Endpoints

### Get Categories
**GET** `/onboarding/categories`

Get all available categories with their subcategories.

**Response (200):**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "Programming",
        "description": "Learn programming languages and frameworks",
        "image_url": "https://example.com/images/programming.jpg",
        "sub_categories": [
          {
            "id": 1,
            "name": "JavaScript",
            "description": "Learn JavaScript programming"
          },
          {
            "id": 2,
            "name": "Python",
            "description": "Learn Python programming"
          }
        ]
      }
    ]
  }
}
```

### Get Countries
**GET** `/onboarding/countries`

Get list of available countries.

**Response (200):**
```json
{
  "success": true,
  "message": "Countries retrieved successfully",
  "data": {
    "countries": [
      "United States",
      "United Kingdom",
      "Canada",
      "Germany",
      "France"
    ]
  }
}
```

### Save Category Selection
**POST** `/onboarding/category-selection`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "category_id": 1,
  "sub_category_id": 2,
  "custom_category_name": "Custom Category Name"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Category selection saved successfully",
  "data": {
    "category_id": 1,
    "sub_category_id": 2
  }
}
```

### Save Education Type
**POST** `/onboarding/education-type`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "education_type": "in-person"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Education type saved successfully",
  "data": {
    "education_type": "in-person"
  }
}
```

### Save Location
**POST** `/onboarding/location`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "country": "United States",
  "city": "New York"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Location saved successfully",
  "data": {
    "country": "United States",
    "city": "New York"
  }
}
```

### Save Online Options
**POST** `/onboarding/online-options`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "education_option": "both"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Online education options saved successfully",
  "data": {
    "education_option": "both",
    "wants_courses": true,
    "wants_mentoring": true
  }
}
```

### Get Onboarding Status
**GET** `/onboarding/status`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Onboarding status retrieved successfully",
  "data": {
    "status": {
      "category_selected": true,
      "education_type_selected": true,
      "location_selected": true,
      "online_options_selected": false
    },
    "is_complete": true,
    "next_step": null
  }
}
```

### Get User Preferences
**GET** `/onboarding/preferences`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "User preferences retrieved successfully",
  "data": {
    "preferences": {
      "category": {
        "id": 1,
        "name": "Programming"
      },
      "sub_category": {
        "id": 2,
        "name": "Python"
      },
      "education_type": "in-person",
      "country": "United States",
      "city": "New York",
      "wants_courses": true,
      "wants_mentoring": false,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}
```

---

## Error Responses

### Validation Error (422)
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

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": {
    "email": ["The provided credentials are incorrect"]
  }
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Registration failed",
  "errors": {
    "server": "An error occurred during registration"
  }
}
```

---

## Onboarding Flow

The typical onboarding flow for a user would be:

1. **Register/Login** - Create account or authenticate
2. **Get Categories** - Fetch available categories
3. **Save Category Selection** - User selects their skill category
4. **Save Education Type** - Choose between "in-person" or "online"
5. **Save Location** (if in-person) - Provide country and city
6. **Save Online Options** (if online) - Choose courses, mentoring, or both
7. **Check Status** - Verify onboarding is complete

---

## Notes

- All timestamps are in ISO 8601 format
- Password must contain at least 1 uppercase letter and 1 number, minimum 8 characters
- GDPR consent is required for registration
- Custom categories can be created under the "Others" category
- Social login endpoints (Google/Apple) are placeholders and need implementation
- All protected endpoints require valid Bearer token in Authorization header
- **Mentor Registration**: Mentors cannot self-register through the API. They must be created by administrators through the admin panel. However, existing mentors can login through the API.
