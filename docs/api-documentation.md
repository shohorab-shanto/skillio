# Skillo Mobile API Documentation

## Overview
This API provides comprehensive endpoints for the Skillo mobile application, covering user authentication, onboarding functionality, home page dynamic content with personalization, course details, mentor details, and category filtering features.

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

## Payment Methods

The API uses **Stripe** for secure payment processing with the following features:

### Supported Payment Methods
- **Credit/Debit Cards**: Visa, Mastercard, American Express, Discover
- **Security**: 3D Secure authentication when required
- **Integration**: Stripe Elements for secure card input

### Payment Processing Details
- **Fees**: 2.9% + $0.30 per transaction (handled automatically)
- **Revenue Sharing**: 80% to mentor, 20% to platform
- **Currency**: USD
- **Payment Status**: `pending`, `paid`, `failed`, `refunded`

### Payment Method ID
When making payments, you need to provide a `payment_method_id` obtained from Stripe's frontend integration. This ID represents the customer's payment method and is created securely on the client side.

---

## Table of Contents
1. [Authentication Endpoints](#authentication-endpoints)
2. [Onboarding Endpoints](#onboarding-endpoints)
3. [Home Page Data Endpoints](#home-page-data-endpoints)
4. [Course Details Endpoints](#course-details-endpoints)
5. [Mentor Details Endpoints](#mentor-details-endpoints)
6. [Review Endpoints](#review-endpoints)
7. [Course Enrollment Endpoints](#course-enrollment-endpoints)
8. [Session Booking Endpoints](#session-booking-endpoints)
9. [Error Responses](#error-responses)
10. [Personalization Logic](#personalization-logic)

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
  "gdpr_consent": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abcdef123456789..."
  }
}
```

### Login User
**POST** `/auth/login`

Authenticate user and return access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "Password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": "2025-01-01T00:00:00.000000Z",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abcdef123456789..."
  }
}
```

### Logout User
**POST** `/auth/logout`

Logout user and revoke current token.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Get Current User
**GET** `/auth/me`

Get current authenticated user information.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response:**
```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": "2025-01-01T00:00:00.000000Z",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

### Refresh Token
**POST** `/auth/refresh`

Refresh the current access token.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response:**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "2|newabcdef123456789..."
  }
}
```

### Forgot Password
**POST** `/auth/forgot-password`

Send password reset email to user.

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password reset email sent successfully"
}
```

### Reset Password
**POST** `/auth/reset-password`

Reset user password using token from email.

**Request Body:**
```json
{
  "email": "john@example.com",
  "token": "reset_token_from_email",
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

### Social Authentication (Google)
**POST** `/auth/social/google`

Authenticate user using Google OAuth.

**Request Body:**
```json
{
  "access_token": "google_access_token"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Google authentication successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abcdef123456789..."
  }
}
```

### Social Authentication (Apple)
**POST** `/auth/social/apple`

Authenticate user using Apple Sign-In.

**Request Body:**
```json
{
  "identity_token": "apple_identity_token"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Apple authentication successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abcdef123456789..."
  }
}
```

---

## Onboarding Endpoints

### Get Categories
**GET** `/onboarding/categories`

Retrieve all available categories for user selection.

**Response:**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "E-Commerce & Online Stores",
      "sub_categories": [
        {
          "id": 1,
          "name": "Dropshipping"
        },
        {
          "id": 2,
          "name": "Print on Demand"
        }
      ]
    }
  ]
}
```

### Get Countries
**GET** `/onboarding/countries`

Retrieve all available countries for user selection.

**Response:**
```json
{
  "success": true,
  "message": "Countries retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "United States",
      "code": "US"
    },
    {
      "id": 2,
      "name": "United Kingdom",
      "code": "GB"
    }
  ]
}
```

### Save Category Selection
**POST** `/onboarding/category-selection`

Save user's selected category and sub-categories.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Request Body:**
```json
{
  "category_id": 1,
  "sub_category_id": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "Category selection saved successfully"
}
```

### Save Education Type
**POST** `/onboarding/education-type`

Save user's preferred education type.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Request Body:**
```json
{
  "education_type": "online"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Education type saved successfully"
}
```

### Save Location
**POST** `/onboarding/location`

Save user's location information.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Request Body:**
```json
{
  "country_id": 1,
  "city": "New York",
  "timezone": "America/New_York"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Location saved successfully"
}
```

### Save Online Options
**POST** `/onboarding/online-options`

Save user's online learning preferences.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Request Body:**
```json
{
  "preferred_mentor_type": "online",
  "learning_goals": "Career advancement",
  "experience_level": "beginner"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Online options saved successfully"
}
```

### Get Onboarding Status
**GET** `/onboarding/status`

Check user's onboarding completion status.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response:**
```json
{
  "success": true,
  "message": "Onboarding status retrieved successfully",
  "data": {
    "is_completed": false,
    "completed_steps": ["category_selection", "education_type"],
    "remaining_steps": ["location", "online_options"]
  }
}
```

### Get User Preferences
**GET** `/onboarding/preferences`

Retrieve user's saved preferences.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response:**
```json
{
  "success": true,
  "message": "User preferences retrieved successfully",
  "data": {
    "category_id": 1,
    "sub_category_id": 2,
    "education_type": "online",
    "country_id": 1,
    "city": "New York",
    "timezone": "America/New_York",
    "preferred_mentor_type": "online",
    "learning_goals": "Career advancement",
    "experience_level": "beginner"
  }
}
```

### Get My Custom Sub-categories
**GET** `/onboarding/my-custom-subcategories`

Retrieve user's custom sub-categories.

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response:**
```json
{
  "success": true,
  "message": "Custom sub-categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Custom Sub-category",
      "category_id": 1
    }
  ]
}
```

---

## Home Page Data Endpoints

### Get Top Mentors
**GET** `/home/top-mentors`

Retrieve top-rated mentors with preference matching for logged-in users and category filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (optional - for personalized results)

**Parameters:**
- `category_id` (query, optional) - Filter mentors by category ID

**Response:**
```json
{
  "success": true,
  "message": "Top mentors retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Mentor Name",
      "email": "mentor@gmail.com",
      "photo": "http://localhost:8000/storage/mentors/photos/photo.png",
      "bio": "Laravel Expert",
      "work_experience": "WizTecBd",
      "type": "online",
      "availability": "available",
      "rating": 4.0,
      "total_reviews": 2,
      "lowest_session_rate": "0.96",
      "skills": ["E-Commerce & Online Stores", "Technical & AI Skills"],
      "created_at": "2025-08-03T13:14:55.000000Z",
      "updated_at": "2025-08-25T08:07:59.000000Z"
    }
  ],
  "count": 6
}
```

### Get Popular Courses
**GET** `/home/popular-courses`

Retrieve popular courses based on ratings and reviews, with preference matching for logged-in users and category filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (optional - for personalized results)

**Parameters:**
- `category_id` (query, optional) - Filter courses by category ID

**Response:**
```json
{
  "success": true,
  "message": "Popular courses retrieved successfully",
  "data": [
    {
      "id": 18,
      "title": "Machine Learning Basics",
      "description": "Comprehensive course on machine learning fundamentals",
      "price": "78.19",
      "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_689483c45bef6.jpg",
      "cover_photo": "http://localhost:8000/storage/courses/covers/689483c45bef6.jpg",
      "level": "intermediate",
      "duration": "21 days",
      "is_featured": true,
      "status": "approved",
      "rating": 4.7,
      "total_reviews": 3,
      "enrollment_count": 1,
      "mentor": {
        "id": 1,
        "name": "Mentor Name",
        "photo": "http://localhost:8000/storage/mentors/photos/photo.png"
      },
      "category": {
        "id": 5,
        "name": "Finance, Trading & Investment"
      },
      "sub_categories": [
        {
          "id": 12,
          "name": "Cryptocurrency Trading"
        }
      ],
      "created_at": "2025-08-03T14:32:08.000000Z",
      "updated_at": "2025-08-27T14:14:21.000000Z"
    }
  ],
  "count": 6
}
```

### Get New Courses
**GET** `/home/new-courses`

Retrieve newest courses ordered by creation date, with preference matching for logged-in users and category filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (optional - for personalized results)

**Parameters:**
- `category_id` (query, optional) - Filter courses by category ID

**Response:**
```json
{
  "success": true,
  "message": "New courses retrieved successfully",
  "data": [
    {
      "id": 50,
      "title": "New Course",
      "description": "Latest course content",
      "price": "120.00",
      "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_68aeb1d709eb4.png",
      "cover_photo": "http://localhost:8000/storage/courses/covers/68aeb1d709eb4.png",
      "level": "beginner",
      "duration": "7 days",
      "is_featured": false,
      "status": "approved",
      "rating": 0,
      "total_reviews": 0,
      "enrollment_count": 0,
      "mentor": {
        "id": 6,
        "name": "Maria Garcia",
        "photo": null
      },
      "category": {
        "id": 1,
        "name": "E-Commerce & Online Stores"
      },
      "sub_categories": [
        {
          "id": 2,
          "name": "Print on Demand"
        }
      ],
      "created_at": "2025-08-27T07:20:55.000000Z",
      "updated_at": "2025-08-27T07:22:45.000000Z"
    }
  ],
  "count": 6
}
```

### Get Featured Courses
**GET** `/home/featured-courses`

Retrieve manually selected featured courses with category filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Parameters:**
- `category_id` (query, optional) - Filter courses by category ID

**Response:**
```json
{
  "success": true,
  "message": "Featured courses retrieved successfully",
  "data": [
    {
      "id": 18,
      "title": "Machine Learning Basics",
      "description": "Comprehensive course on machine learning fundamentals",
      "price": "78.19",
      "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_689483c45bef6.jpg",
      "cover_photo": "http://localhost:8000/storage/courses/covers/689483c45bef6.jpg",
      "level": "intermediate",
      "duration": "21 days",
      "is_featured": true,
      "status": "approved",
      "rating": 4.7,
      "total_reviews": 3,
      "enrollment_count": 1,
      "mentor": {
        "id": 1,
        "name": "Mentor Name",
        "photo": "http://localhost:8000/storage/mentors/photos/photo.png"
      },
      "category": {
        "id": 5,
        "name": "Finance, Trading & Investment"
      },
      "sub_categories": [
        {
          "id": 12,
          "name": "Cryptocurrency Trading"
        }
      ],
      "created_at": "2025-08-03T14:32:08.000000Z",
      "updated_at": "2025-08-27T14:14:21.000000Z"
    }
  ],
  "count": 6
}
```

### Get Top Reviews
**GET** `/home/top-reviews`

Retrieve latest high-rated reviews.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Response:**
```json
{
  "success": true,
  "message": "Top reviews retrieved successfully",
  "data": [
    {
      "id": 60,
      "rating": 5,
      "comment": "Excellent course with great content",
      "user_name": "User Name",
      "user_photo": null,
      "course_title": "Machine Learning Basics",
      "created_at": "2025-08-03T14:39:18.000000Z",
      "updated_at": "2025-08-03T14:39:18.000000Z"
    }
  ],
  "count": 12
}
```

### Get All Home Data
**GET** `/home/all-data`

Retrieve all home page data in a single request for better performance with category filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (optional - for personalized results)

**Parameters:**
- `category_id` (query, optional) - Filter all data by category ID

**Response:**
```json
{
  "success": true,
  "message": "All home page data retrieved successfully",
  "data": {
    "top_mentors": [
      {
        "id": 1,
        "name": "Mentor Name",
        "photo": "http://localhost:8000/storage/mentors/photos/photo.png",
        "bio": "Laravel Expert",
        "work_experience": "WizTecBd",
        "rating": 4.0,
        "total_reviews": 2,
        "lowest_session_rate": "0.96",
        "skills": ["E-Commerce & Online Stores", "Technical & AI Skills"]
      }
    ],
    "popular_courses": [
      {
        "id": 18,
        "title": "Machine Learning Basics",
        "description": "Comprehensive course on machine learning fundamentals",
        "price": "78.19",
        "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_689483c45bef6.jpg",
        "cover_photo": "http://localhost:8000/storage/courses/covers/689483c45bef6.jpg",
        "rating": 4.7,
        "total_reviews": 3,
        "enrollment_count": 1,
        "mentor_name": "Mentor Name",
        "category_name": "Finance, Trading & Investment"
      }
    ],
    "new_courses": [
      {
        "id": 50,
        "title": "New Course",
        "description": "Latest course content",
        "price": "120.00",
        "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_68aeb1d709eb4.png",
        "cover_photo": "http://localhost:8000/storage/courses/covers/68aeb1d709eb4.png",
        "rating": 0,
        "total_reviews": 0,
        "enrollment_count": 0,
        "mentor_name": "Maria Garcia",
        "category_name": "E-Commerce & Online Stores"
      }
    ],
    "featured_courses": [],
    "top_reviews": [
      {
        "id": 60,
        "rating": 5,
        "comment": "Excellent course with great content",
        "user_name": "User Name",
        "user_photo": null,
        "course_title": "Machine Learning Basics"
      }
    ]
  }
}
```

---

## Course Details Endpoints

### Get Course Details
**GET** `/courses/{course}/details`

Retrieve comprehensive course information including statistics, mentor details, reviews, and enrollment status.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (optional - for enrollment status)

**Parameters:**
- `course` (path) - Course ID

**Response:**
```json
{
  "success": true,
  "message": "Course details retrieved successfully",
  "data": {
    "id": 18,
    "title": "Machine Learning Basics",
    "description": "Comprehensive course on machine learning fundamentals",
    "price": "78.19",
    "discount": "0.00",
    "discounted_price": null,
    "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_689483c45bef6.jpg",
    "cover_photo": "http://localhost:8000/storage/courses/covers/689483c45bef6.jpg",
    "level": "intermediate",
    "duration_days": 21,
    "start_date": "2025-08-07",
    "end_date": "2025-08-31",
    "status": "approved",
    "featured": true,
    "is_enrolled": false,
    "statistics": {
      "rating": 4.7,
      "total_reviews": 3,
      "enrollment_count": 1,
      "currently_enrolled": 1,
      "total_income": "78.19"
    },
    "mentor": {
      "id": 1,
      "name": "Mentor Name",
      "email": "mentor@gmail.com",
      "photo": "http://localhost:8000/storage/mentors/photos/photo.png",
      "bio": "Laravel Expert",
      "work_experience": "WizTecBd",
      "verified": true,
      "rating": 4.0,
      "total_reviews": 2
    },
    "category": {
      "id": 5,
      "name": "Finance, Trading & Investment"
    },
    "sub_categories": [
      {
        "id": 12,
        "name": "Cryptocurrency Trading"
      }
    ],
    "recent_reviews": [
      {
        "id": 60,
        "rating": 5,
        "comment": "Excellent course with great content",
        "user_name": "User Name",
        "user_photo": null,
        "created_at": "2025-08-03T14:39:18.000000Z"
      }
    ],
    "created_at": "2025-08-03T14:32:08.000000Z",
    "updated_at": "2025-08-27T14:14:21.000000Z"
  }
}
```

### Get Course Reviews
**GET** `/courses/{course}/reviews`

Retrieve paginated course reviews.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Parameters:**
- `course` (path) - Course ID
- `per_page` (query, optional) - Number of reviews per page (default: 10)
- `page` (query, optional) - Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "message": "Course reviews retrieved successfully",
  "data": [
    {
      "id": 60,
      "rating": 5,
      "comment": "Excellent course with great content",
      "user_name": "User Name",
      "user_photo": null,
      "created_at": "2025-08-03T14:39:18.000000Z",
      "updated_at": "2025-08-03T14:39:18.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 3,
    "has_more_pages": false
  }
}
```

### Get Enrolled Students (Protected)
**GET** `/courses/{course}/enrolled-students`

Retrieve paginated list of enrolled students. Requires authentication and mentor/admin permissions.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `course` (path) - Course ID
- `per_page` (query, optional) - Number of students per page (default: 10)
- `page` (query, optional) - Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "message": "Enrolled students retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Student Name",
      "email": "student@example.com",
      "photo": null,
      "enrolled_at": "2025-08-03T14:39:18.000000Z",
      "progress": 75
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1,
    "has_more_pages": false
  }
}
```

### Get Course Statistics (Protected)
**GET** `/courses/{course}/statistics`

Retrieve course statistics and analytics. Requires authentication and mentor/admin permissions.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `course` (path) - Course ID

**Response:**
```json
{
  "success": true,
  "message": "Course statistics retrieved successfully",
  "data": {
    "total_reviews": 3,
    "average_rating": 4.7,
    "enrollment_count": 1,
    "currently_enrolled": 1,
    "total_income": "78.19",
    "completion_rate": 75.0,
    "review_statistics": {
      "average_rating": 4.7,
      "total_reviews": 3,
      "rating_breakdown": {
        "5_star": 2,
        "4_star": 1,
        "3_star": 0,
        "2_star": 0,
        "1_star": 0
      },
      "rating_percentages": {
        "5_star": 66.7,
        "4_star": 33.3,
        "3_star": 0,
        "2_star": 0,
        "1_star": 0
      }
    }
  }
}
```

---

## Mentor Details Endpoints

### Get Mentor Details
**GET** `/mentors/{mentor}/details`

Retrieve comprehensive mentor information including statistics, reviews, courses, and sessions.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (optional - for review permissions)

**Parameters:**
- `mentor` (path) - Mentor ID

**Response:**
```json
{
  "success": true,
  "message": "Mentor details retrieved successfully",
  "data": {
    "id": 1,
    "name": "Mentor Name",
    "email": "mentor@gmail.com",
    "photo": "http://localhost:8000/storage/mentors/photos/photo.png",
    "bio": "Laravel Expert",
    "work_experience": "WizTecBd",
    "certifications": [],
    "availability": "available",
    "working_hours": [],
    "verified": true,
    "type": "online",
    "created_at": "2025-08-03T13:14:55.000000Z",
    "updated_at": "2025-08-25T08:07:59.000000Z",
    "statistics": {
      "average_rating": 4.0,
      "total_reviews": 2,
      "formatted_rating": "4.0",
      "star_rating": {
        "full_stars": 4,
        "half_star": 0,
        "empty_stars": 1,
        "rating": "4.0000"
      }
    },
    "top_category": "E-Commerce & Online Stores",
    "top_category_sub_categories": ["Shopify", "Dropshipping"],
    "courses_summary": {
      "total_courses": 29,
      "approved_courses": 29,
      "featured_courses": 0
    },
    "sessions_summary": {
      "total_sessions": 13,
      "active_sessions": 1,
      "booked_sessions": 12
    },
    "can_review": false,
    "existing_review": null,
    "review_statistics": {
      "average_rating": 4.0,
      "total_reviews": 2,
      "rating_breakdown": {
        "5_star": 0,
        "4_star": 2,
        "3_star": 0,
        "2_star": 0,
        "1_star": 0
      },
      "rating_percentages": {
        "5_star": 0,
        "4_star": 100,
        "3_star": 0,
        "2_star": 0,
        "1_star": 0
      }
    },
    "recent_reviews": [
      {
        "id": 36,
        "rating": 4,
        "comment": "Excellent mentor with great teaching skills",
        "user_name": "Marcia Frami",
        "user_photo": null,
        "created_at": "2025-08-03T14:39:18.000000Z",
        "updated_at": "2025-08-03T14:39:18.000000Z"
      }
    ]
  }
}
```

### Get Mentor Sessions
**GET** `/mentors/{mentor}/sessions`

Retrieve mentor's available sessions with pagination and date filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Parameters:**
- `mentor` (path) - Mentor ID
- `per_page` (query, optional) - Number of sessions per page (default: 9)
- `page` (query, optional) - Page number (default: 1)
- `start_date` (query, optional) - Filter sessions from this date (YYYY-MM-DD)
- `end_date` (query, optional) - Filter sessions to this date (YYYY-MM-DD)

**Response:**
```json
{
  "success": true,
  "message": "Mentor sessions retrieved successfully",
  "data": [
    {
      "id": 12,
      "title": "Laravel Development Session",
      "description": "Learn Laravel fundamentals",
      "date": "2025-08-26T18:00:00.000000Z",
      "start_time": "2025-09-11T12:16:00.000000Z",
      "end_time": "2025-09-11T13:16:00.000000Z",
      "duration": 60,
      "price": "50.00",
      "status": "active",
      "max_participants": 10,
      "current_participants": 3,
      "category": {
        "id": 1,
        "name": "E-Commerce & Online Stores"
      },
      "sub_categories": [
        {
          "id": 1,
          "name": "Dropshipping"
        }
      ],
      "mentor": {
        "id": 1,
        "name": "Mentor Name",
        "photo": "http://localhost:8000/storage/mentors/photos/photo.png"
      },
      "created_at": "2025-08-27T09:13:46.000000Z",
      "updated_at": "2025-08-27T09:33:06.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 9,
    "total": 1,
    "has_more_pages": false
  }
}
```

### Get Mentor Reviews
**GET** `/mentors/{mentor}/reviews`

Retrieve paginated mentor reviews.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Parameters:**
- `mentor` (path) - Mentor ID
- `per_page` (query, optional) - Number of reviews per page (default: 10)
- `page` (query, optional) - Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "message": "Mentor reviews retrieved successfully",
  "data": [
    {
      "id": 36,
      "rating": 4,
      "comment": "Excellent mentor with great teaching skills",
      "user_name": "Marcia Frami",
      "user_photo": null,
      "created_at": "2025-08-03T14:39:18.000000Z",
      "updated_at": "2025-08-03T14:39:18.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2,
    "has_more_pages": false
  }
}
```

### Get Mentor Courses
**GET** `/mentors/{mentor}/courses`

Retrieve mentor's courses with pagination and status filtering.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Parameters:**
- `mentor` (path) - Mentor ID
- `per_page` (query, optional) - Number of courses per page (default: 10)
- `page` (query, optional) - Page number (default: 1)
- `status` (query, optional) - Filter by status: `all`, `approved`, `pending`, `rejected` (default: all)

**Response:**
```json
{
  "success": true,
  "message": "Mentor courses retrieved successfully",
  "data": [
    {
      "id": 18,
      "title": "Machine Learning Basics",
      "description": "Comprehensive course on machine learning fundamentals",
      "price": "78.19",
      "discount": "0.00",
      "discounted_price": null,
      "thumbnail": "http://localhost:8000/storage/courses/thumbnails/thumb_689483c45bef6.jpg",
      "cover_photo": "http://localhost:8000/storage/courses/covers/689483c45bef6.jpg",
      "duration_days": 21,
      "start_date": "2025-08-07",
      "end_date": "2025-08-31",
      "status": "approved",
      "featured": true,
      "rating": 4.7,
      "total_reviews": 3,
      "enrollment_count": 1,
      "category": {
        "id": 5,
        "name": "Finance, Trading & Investment"
      },
      "sub_categories": [
        {
          "id": 12,
          "name": "Cryptocurrency Trading"
        }
      ],
      "created_at": "2025-08-03T14:32:08.000000Z",
      "updated_at": "2025-08-27T14:14:21.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 29,
    "has_more_pages": true
  }
}
```

### Get Mentor Statistics (Protected)
**GET** `/mentors/{mentor}/statistics`

Retrieve mentor statistics and analytics. Requires authentication and mentor/admin permissions.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `mentor` (path) - Mentor ID

**Response:**
```json
{
  "success": true,
  "message": "Mentor statistics retrieved successfully",
  "data": {
    "total_reviews": 2,
    "average_rating": 4.0,
    "total_courses": 29,
    "approved_courses": 29,
    "total_sessions": 13,
    "active_sessions": 1,
    "booked_sessions": 12,
    "review_statistics": {
      "average_rating": 4.0,
      "total_reviews": 2,
      "rating_breakdown": {
        "5_star": 0,
        "4_star": 2,
        "3_star": 0,
        "2_star": 0,
        "1_star": 0
      },
      "rating_percentages": {
        "5_star": 0,
        "4_star": 100,
        "3_star": 0,
        "2_star": 0,
        "1_star": 0
      }
    }
  }
}
```

---

## Review Endpoints

### Create Review
**POST** `/reviews/`

Create a new review for a course or mentor. Users can only review courses they are enrolled in or mentors they have booked sessions with.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Request Body:**
```json
{
  "rating": 5,
  "comment": "Excellent course with great content and clear explanations",
  "course_id": 18
}
```

**OR for mentor review:**
```json
{
  "rating": 4,
  "comment": "Great mentor with excellent teaching skills",
  "mentor_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Course review submitted successfully",
  "data": {
    "id": 61,
    "rating": 5,
    "comment": "Excellent course with great content and clear explanations",
    "user_name": "John Doe",
    "user_photo": null,
    "course_title": "Machine Learning Basics",
    "mentor_name": "Mentor Name",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

### Get User Reviews
**GET** `/reviews/`

Retrieve paginated list of user's reviews with optional filtering by type.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `per_page` (query, optional) - Number of reviews per page (default: 10)
- `page` (query, optional) - Page number (default: 1)
- `type` (query, optional) - Filter by type: `all`, `course`, `mentor` (default: all)

**Response:**
```json
{
  "success": true,
  "message": "User reviews retrieved successfully",
  "data": [
    {
      "id": 61,
      "rating": 5,
      "comment": "Excellent course with great content",
      "user_name": "John Doe",
      "user_photo": null,
      "type": "course",
      "course_title": "Machine Learning Basics",
      "mentor_name": "Mentor Name",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    {
      "id": 62,
      "rating": 4,
      "comment": "Great mentor with excellent teaching skills",
      "user_name": "John Doe",
      "user_photo": null,
      "type": "mentor",
      "mentor_name": "Mentor Name",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2,
    "has_more_pages": false
  }
}
```

### Get Review Details
**GET** `/reviews/{review}`

Retrieve details of a specific review.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `review` (path) - Review ID

**Response:**
```json
{
  "success": true,
  "message": "Review retrieved successfully",
  "data": {
    "id": 61,
    "rating": 5,
    "comment": "Excellent course with great content and clear explanations",
    "user_name": "John Doe",
    "user_photo": null,
    "type": "course",
    "course_title": "Machine Learning Basics",
    "mentor_name": "Mentor Name",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

### Update Review
**PUT** `/reviews/{review}`

Update an existing review. Users can only update their own reviews.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `review` (path) - Review ID

**Request Body:**
```json
{
  "rating": 4,
  "comment": "Updated review comment with more details"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Review updated successfully",
  "data": {
    "id": 61,
    "rating": 4,
    "comment": "Updated review comment with more details",
    "user_name": "John Doe",
    "user_photo": null,
    "type": "course",
    "course_title": "Machine Learning Basics",
    "mentor_name": "Mentor Name",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

### Delete Review
**DELETE** `/reviews/{review}`

Delete a review. Users can only delete their own reviews.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `review` (path) - Review ID

**Response:**
```json
{
  "success": true,
  "message": "Review deleted successfully"
}
```

### Check Review Eligibility
**GET** `/reviews/can-review`

Check if user can review a specific course or mentor before attempting to create a review.

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer {token}` (required)

**Parameters:**
- `course_id` (query, optional) - Course ID to check eligibility
- `mentor_id` (query, optional) - Mentor ID to check eligibility

**Response (Eligible):**
```json
{
  "success": true,
  "message": "Review eligibility checked",
  "data": {
    "can_review": true,
    "reason": "You are eligible to review this course",
    "course_title": "Machine Learning Basics"
  }
}
```

**Response (Not Eligible - Not Enrolled):**
```json
{
  "success": true,
  "message": "Review eligibility checked",
  "data": {
    "can_review": false,
    "reason": "You must be enrolled in this course to review it",
    "course_title": "Machine Learning Basics"
  }
}
```

**Response (Not Eligible - Already Reviewed):**
```json
{
  "success": true,
  "message": "Review eligibility checked",
  "data": {
    "can_review": false,
    "reason": "You have already reviewed this course",
    "course_title": "Machine Learning Basics",
    "existing_review": {
      "id": 61,
      "rating": 5,
      "comment": "Previous review comment",
      "created_at": "2025-01-01T00:00:00.000000Z"
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
  "message": "Unauthenticated",
  "error": "Token not provided or invalid"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error",
  "error": "An unexpected error occurred"
}
```

---

## Personalization Logic

For logged-in users, the API applies personalization based on their onboarding preferences:

1. **Category Preference**: Shows mentors and courses matching user's selected category
2. **Sub-category Preference**: Filters by user's selected sub-categories
3. **Education Type**: 
   - `online` - Shows only online mentors
   - `in-person` - Shows only in-person mentors
   - `both` - Shows all mentor types

**Priority Order:**
1. Preference-matched results (up to 6 items)
2. Regular top results to fill remaining slots
3. Combined with preference-matched items first

**For Non-logged-in Users:**
- Returns general top-rated results without personalization

**Category Filtering:**
- All home page endpoints support `category_id` parameter
- When provided, filters results to show only items from the specified category
- Works in combination with personalization for logged-in users

---

## Course Enrollment Endpoints

### 1. Get Course Enrollment Information

**Endpoint:** `GET /api/v1/enrollments/courses/{course}/info`

**Description:** Get course enrollment information and checkout details for a specific course.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `course` (path, required): Course ID

**Response:**
```json
{
  "success": true,
  "message": "Course enrollment information retrieved successfully",
  "data": {
    "is_enrolled": false,
    "course": {
      "id": 1,
      "title": "Advanced Web Development",
      "description": "Learn advanced web development techniques",
      "thumbnail": "https://example.com/storage/courses/thumbnails/course1.jpg",
      "cover_photo": "https://example.com/storage/courses/covers/course1.jpg",
      "mentor": {
        "id": 1,
        "name": "John Doe",
        "photo": "https://example.com/storage/mentors/photos/mentor1.jpg",
        "rating": 4.8,
        "total_reviews": 150
      },
      "category": {
        "id": 1,
        "name": "Technology"
      },
      "sub_categories": [
        {
          "id": 1,
          "name": "Web Development"
        }
      ],
      "duration_days": 30,
      "created_at": "2024-01-15T00:00:00.000000Z",
      "enrollment_count": 45
    },
    "pricing": {
      "original_price": 299.99,
      "discounted_price": 199.99,
      "discount_percentage": 33.00,
      "final_price": 199.99,
      "stripe_fee": 6.10,
      "net_amount": 193.89,
      "currency": "USD"
    },
    "eligibility": {
      "can_enroll": true,
      "reason": null
    }
  }
}
```

### 2. Enroll in Course

**Endpoint:** `POST /api/v1/enrollments/courses/{course}/enroll`

**Description:** Enroll in a course with payment processing using Stripe.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `course` (path, required): Course ID

**Request Body:**
```json
{
  "payment_method_id": "pm_1234567890",
  "cardholder_name": "John Doe"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully enrolled in course",
  "data": {
    "enrollment": {
      "id": 1,
      "status": "active",
      "payment_status": "paid",
      "enrolled_at": "2024-01-15T10:30:00.000000Z",
      "amount": 199.99,
      "currency": "USD"
    },
    "transaction": {
      "id": 1,
      "transaction_id": "TXN_1234567890",
      "status": "completed",
      "amount": 199.99,
      "currency": "USD"
    },
    "course": {
      "id": 1,
      "title": "Advanced Web Development",
      "thumbnail": "https://example.com/storage/courses/thumbnails/course1.jpg"
    }
  }
}
```

### 3. Get User Enrollments

**Endpoint:** `GET /api/v1/enrollments/my-enrollments`

**Description:** Get all course enrollments for the authenticated user.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)
- `status` (optional): Filter by enrollment status (`all`, `pending`, `active`, `completed`, `cancelled`, `refunded`)

**Response:**
```json
{
  "success": true,
  "message": "User enrollments retrieved successfully",
  "data": {
    "enrollments": [
      {
        "id": 1,
        "enrollment_status": "active",
        "payment_status": "paid",
        "enrolled_at": "2024-01-15T10:30:00.000000Z",
        "started_at": null,
        "completed_at": null,
        "progress_percentage": 0.00,
        "amount": 199.99,
        "currency": "USD",
        "course": {
          "id": 1,
          "title": "Advanced Web Development",
          "description": "Learn advanced web development techniques",
          "thumbnail": "https://example.com/storage/courses/thumbnails/course1.jpg",
          "cover_photo": "https://example.com/storage/courses/covers/course1.jpg",
          "duration_days": 30,
          "created_at": "2024-01-15T00:00:00.000000Z",
          "mentor": {
            "id": 1,
            "name": "John Doe",
            "photo": "https://example.com/storage/mentors/photos/mentor1.jpg"
          },
          "category": {
            "id": 1,
            "name": "Technology"
          }
        },
        "transaction": {
          "id": 1,
          "transaction_id": "TXN_1234567890",
          "status": "completed",
          "amount": 199.99,
          "currency": "USD"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1,
      "has_more_pages": false
    }
  }
}
```

### 4. Get Enrollment Details

**Endpoint:** `GET /api/v1/enrollments/{enrollment}`

**Description:** Get detailed information about a specific enrollment.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `enrollment` (path, required): Enrollment ID

**Response:**
```json
{
  "success": true,
  "message": "Enrollment details retrieved successfully",
  "data": {
    "enrollment": {
      "id": 1,
      "enrollment_status": "active",
      "payment_status": "paid",
      "enrolled_at": "2024-01-15T10:30:00.000000Z",
      "started_at": null,
      "completed_at": null,
      "cancelled_at": null,
      "progress_percentage": 0.00,
      "last_accessed_at": null,
      "amount": 199.99,
      "currency": "USD",
      "notes": null
    },
    "course": {
      "id": 1,
      "title": "Advanced Web Development",
      "description": "Learn advanced web development techniques",
      "thumbnail": "https://example.com/storage/courses/thumbnails/course1.jpg",
      "cover_photo": "https://example.com/storage/courses/covers/course1.jpg",
        "duration_days": 30,
        "created_at": "2024-01-15T00:00:00.000000Z",
      "mentor": {
        "id": 1,
        "name": "John Doe",
        "photo": "https://example.com/storage/mentors/photos/mentor1.jpg",
        "rating": 4.8,
        "total_reviews": 150
      },
      "category": {
        "id": 1,
        "name": "Technology"
      },
      "sub_categories": [
        {
          "id": 1,
          "name": "Web Development"
        }
      ]
    },
    "transaction": {
      "id": 1,
      "transaction_id": "TXN_1234567890",
      "status": "completed",
      "amount": 199.99,
      "currency": "USD",
      "payment_method_type": "card",
      "created_at": "2024-01-15T10:30:00.000000Z"
    },
    "conversation": {
      "id": 1,
      "status": "active"
    }
  }
}
```

### 5. Cancel Enrollment - DISABLED

**Endpoint:** `POST /api/v1/enrollments/{enrollment}/cancel`

**Description:** Course enrollments cannot be cancelled after payment. All enrollments are final once payment is completed.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `enrollment` (path, required): Enrollment ID

**Response:**
```json
{
  "success": false,
  "message": "Cancellation not allowed",
  "error": "Course enrollments cannot be cancelled after payment. No refunds are available.",
  "policy": "All course enrollments are final once payment is completed"
}
```

---

## Session Booking Endpoints

### 1. Get Available Sessions

**Endpoint:** `GET /api/v1/sessions/available`

**Description:** Get available session bookings with optional filtering by mentor, category, and date.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Query Parameters:**
- `mentor_id` (optional): Filter by specific mentor
- `category_id` (optional): Filter by category
- `date` (optional): Filter by specific date (YYYY-MM-DD format)
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "message": "Available sessions retrieved successfully",
  "data": {
    "sessions": [
      {
        "id": 14,
        "date": "2025-09-21",
        "start_time": "10:00",
        "end_time": "11:00",
        "duration_minutes": 60,
        "formatted_time_slot": "10:00 - 11:00",
        "fee": "50.00",
        "currency": "USD",
        "mentor": {
          "id": 1,
          "name": "John Doe",
          "photo": "https://example.com/storage/mentors/photos/mentor1.jpg",
          "rating": 4.8,
          "total_reviews": 150
        },
        "category": {
          "id": 1,
          "name": "Technology"
        },
        "sub_categories": [
          {
            "id": 1,
            "name": "Web Development"
          }
        ],
        "is_available": true,
        "has_not_started": true
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1,
      "has_more_pages": false
    }
  }
}
```

### 2. Get Session Booking Information

**Endpoint:** `GET /api/v1/sessions/{session}/info`

**Description:** Get session booking information and checkout details for a specific session.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `session` (path, required): Session ID

**Response:**
```json
{
  "success": true,
  "message": "Session booking information retrieved successfully",
  "data": {
    "is_booked": false,
    "session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00",
      "duration_minutes": 60,
      "formatted_time_slot": "10:00 - 11:00",
      "mentor": {
        "id": 1,
        "name": "John Doe",
        "photo": "https://example.com/storage/mentors/photos/mentor1.jpg",
        "rating": 4.8,
        "total_reviews": 150
      },
      "category": {
        "id": 1,
        "name": "Technology"
      },
      "sub_categories": [
        {
          "id": 1,
          "name": "Web Development"
        }
      ],
      "is_available": true,
      "has_not_started": true
    },
    "pricing": {
      "fee": "50.00",
      "stripe_fee": 1.75,
      "net_amount": 48.25,
      "currency": "USD"
    },
    "eligibility": {
      "can_book": true,
      "reason": null
    }
  }
}
```

### 3. Book Session

**Endpoint:** `POST /api/v1/sessions/{session}/book`

**Description:** Book a session with payment processing using Stripe.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `session` (path, required): Session ID

**Request Body:**
```json
{
  "payment_method_id": "pm_1234567890",
  "cardholder_name": "John Doe"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully booked session",
  "data": {
    "enrollment": {
      "id": 1,
      "status": "active",
      "payment_status": "paid",
      "enrolled_at": "2024-01-15T10:30:00.000000Z",
      "amount": 50.00,
      "currency": "USD"
    },
    "transaction": {
      "id": 1,
      "transaction_id": "TXN_1234567890",
      "status": "completed",
      "amount": 50.00,
      "currency": "USD"
    },
    "session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00",
      "mentor_name": "John Doe"
    }
  }
}
```

### 4. Get User Session Bookings

**Endpoint:** `GET /api/v1/sessions/my-bookings`

**Description:** Get all session bookings for the authenticated user.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)
- `status` (optional): Filter by enrollment status (`all`, `pending`, `active`, `completed`, `cancelled`, `refunded`)

**Response:**
```json
{
  "success": true,
  "message": "User session bookings retrieved successfully",
  "data": {
    "bookings": [
      {
        "id": 1,
        "enrollment_status": "active",
        "payment_status": "paid",
        "enrolled_at": "2024-01-15T10:30:00.000000Z",
        "started_at": null,
        "completed_at": null,
        "amount": 50.00,
        "currency": "USD",
        "session": {
          "id": 14,
          "date": "2025-09-21",
          "start_time": "10:00",
          "end_time": "11:00",
          "duration_minutes": 60,
          "formatted_time_slot": "10:00 - 11:00",
          "fee": "50.00",
          "status": "booked",
          "mentor": {
            "id": 1,
            "name": "John Doe",
            "photo": "https://example.com/storage/mentors/photos/mentor1.jpg"
          },
          "category": {
            "id": 1,
            "name": "Technology"
          }
        },
        "transaction": {
          "id": 1,
          "transaction_id": "TXN_1234567890",
          "status": "completed",
          "amount": 50.00,
          "currency": "USD"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1,
      "has_more_pages": false
    }
  }
}
```

### 5. Get Booking Details

**Endpoint:** `GET /api/v1/sessions/bookings/{enrollment}`

**Description:** Get detailed information about a specific session booking.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `enrollment` (path, required): Enrollment ID

**Response:**
```json
{
  "success": true,
  "message": "Booking details retrieved successfully",
  "data": {
    "enrollment": {
      "id": 1,
      "enrollment_status": "active",
      "payment_status": "paid",
      "enrolled_at": "2024-01-15T10:30:00.000000Z",
      "started_at": null,
      "completed_at": null,
      "cancelled_at": null,
      "amount": 50.00,
      "currency": "USD",
      "notes": null
    },
    "session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00",
      "duration_minutes": 60,
      "formatted_time_slot": "10:00 - 11:00",
      "fee": "50.00",
      "status": "booked",
      "has_not_started": true,
      "mentor": {
        "id": 1,
        "name": "John Doe",
        "photo": "https://example.com/storage/mentors/photos/mentor1.jpg",
        "rating": 4.8,
        "total_reviews": 150
      },
      "category": {
        "id": 1,
        "name": "Technology"
      },
      "sub_categories": [
        {
          "id": 1,
          "name": "Web Development"
        }
      ]
    },
    "transaction": {
      "id": 1,
      "transaction_id": "TXN_1234567890",
      "status": "completed",
      "amount": 50.00,
      "currency": "USD",
      "payment_method_type": "card",
      "created_at": "2024-01-15T10:30:00.000000Z"
    },
    "conversation": {
      "id": 1,
      "status": "active"
    }
  }
}
```

### 6. Cancel Session Booking - DISABLED

**Endpoint:** `POST /api/v1/sessions/bookings/{enrollment}/cancel`

**Description:** Session bookings cannot be cancelled after payment. Users can only switch to another available session with the same mentor and price.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `enrollment` (path, required): Enrollment ID

**Response:**
```json
{
  "success": false,
  "message": "Cancellation not allowed",
  "error": "Session bookings cannot be cancelled after payment. You can switch to another available session with the same mentor and price.",
  "suggestion": "Use the switch session endpoint to change your booking"
}
```

### 7. Get Switchable Sessions

**Endpoint:** `GET /api/v1/sessions/bookings/{enrollment}/switchable`

**Description:** Get available sessions for switching (same mentor, same price).

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Parameters:**
- `enrollment` (path, required): Enrollment ID
- `per_page` (query, optional): Number of sessions per page (default: 10)

**Response:**
```json
{
  "success": true,
  "message": "Switchable sessions retrieved successfully",
  "data": {
    "current_session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00"
    },
    "available_sessions": [
      {
        "id": 15,
        "date": "2025-09-22",
        "start_time": "14:00",
        "end_time": "15:00",
        "duration": 60,
        "type": "online",
        "fee": 50.00,
        "mentor": {
          "id": 1,
          "name": "John Doe",
          "photo": "https://example.com/storage/mentors/photos/mentor1.jpg"
        },
        "category": {
          "id": 1,
          "name": "Technology"
        },
        "sub_categories": [
          {
            "id": 1,
            "name": "Web Development"
          }
        ],
        "is_available": true
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1
    }
  }
}
```

### 8. Switch Session

**Endpoint:** `POST /api/v1/sessions/bookings/{enrollment}/switch`

**Description:** Switch to a different session with same mentor and price.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Parameters:**
- `enrollment` (path, required): Enrollment ID

**Request Body:**
```json
{
  "new_session_id": 15
}
```

**Response:**
```json
{
  "success": true,
  "message": "Session switched successfully",
  "data": {
    "enrollment_id": 1,
    "old_session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00"
    },
    "new_session": {
      "id": 15,
      "date": "2025-09-22",
      "start_time": "14:00",
      "end_time": "15:00"
    },
    "switched_at": "2024-01-16T14:30:00.000000Z"
  }
}
```

---

## Notes

- All timestamps are in ISO 8601 format
- Password must contain at least 1 uppercase letter and 1 number, minimum 8 characters
- GDPR consent is required for registration
- Custom categories can be created under the "Others" category
- Social login endpoints (Google/Apple) are placeholders and need implementation
- All image URLs are absolute URLs pointing to the storage directory
- Pagination is available for list endpoints with `per_page` and `page` parameters
- Protected endpoints require valid authentication token
- Category filtering is optional and works across all home page endpoints
- Course enrollment requires valid Stripe payment method
- Session booking requires valid Stripe payment method
- Enrollment statuses: `pending`, `active`, `completed`, `cancelled`, `refunded`
- Payment statuses: `pending`, `paid`, `failed`, `refunded`
- Stripe fees are calculated as 2.9% + $0.30 per transaction
- Session statuses: `active`, `booked`, `completed`, `cancelled`
- Session bookings are automatically marked as booked when payment succeeds
- **Cancellation Policy**: Course enrollments and session bookings cannot be cancelled after payment
- **Session Switching**: Users can switch session bookings to another available session with the same mentor and price
- **No Refunds**: All payments are final once completed