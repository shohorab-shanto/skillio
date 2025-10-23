# Mobile App Payment Flow - Two-Step Payment Intent Method

## Overview

This document explains the **recommended payment flow** for mobile applications using Stripe Payment Intents API. This two-step approach properly handles 3D Secure authentication and is optimized for mobile apps.

---

## 🔑 Required Stripe Keys

### Backend (Server-Side)
- **Secret Key**: `sk_test_xxxxx` or `sk_live_xxxxx`
- **Stored in**: `.env` file as `STRIPE_SECRET_KEY`
- **Usage**: Creating Payment Intents, verifying payments, transfers

### Mobile App (Client-Side)
- **Publishable Key**: `pk_test_xxxxx` or `pk_live_xxxxx`
- **Stored in**: Mobile app configuration
- **Usage**: Initializing Stripe SDK, confirming payments

---

## 📱 Two-Step Payment Flow

### For Session Booking

```
┌─────────────────────────────────────────────────────────────┐
│ STEP 1: Create Payment Intent                              │
│ POST /api/v1/sessions/{session_id}/create-payment-intent   │
└─────────────────────────────────────────────────────────────┘
                            ↓
    Backend creates unconfirmed Payment Intent
                            ↓
    Returns: { client_secret, payment_intent_id, amount }
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 2: Confirm Payment (Client-Side)                      │
│ Mobile app uses Stripe SDK                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
    Stripe.confirmPayment(clientSecret, cardDetails)
                            ↓
    Handles 3D Secure automatically if needed
                            ↓
    Returns: { paymentIntent.id, status: "succeeded" }
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 3: Confirm Booking                                    │
│ POST /api/v1/sessions/{session_id}/confirm-booking         │
└─────────────────────────────────────────────────────────────┘
                            ↓
    Backend verifies payment and creates booking
                            ↓
    Returns: { enrollment, transaction, conversation_code }
```

### For Course Enrollment

```
┌─────────────────────────────────────────────────────────────┐
│ STEP 1: Create Payment Intent                              │
│ POST /api/v1/enrollments/courses/{course_id}/create-payment-intent │
└─────────────────────────────────────────────────────────────┘
                            ↓
    Backend creates unconfirmed Payment Intent
                            ↓
    Returns: { client_secret, payment_intent_id, amount }
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 2: Confirm Payment (Client-Side)                      │
│ Mobile app uses Stripe SDK                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
    Stripe.confirmPayment(clientSecret, cardDetails)
                            ↓
    Handles 3D Secure automatically if needed
                            ↓
    Returns: { paymentIntent.id, status: "succeeded" }
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 3: Confirm Enrollment                                 │
│ POST /api/v1/enrollments/courses/{course_id}/confirm-enrollment │
└─────────────────────────────────────────────────────────────┐
                            ↓
    Backend verifies payment and creates enrollment
                            ↓
    Returns: { enrollment, transaction, conversation_code }
```

---

## 🔧 API Endpoints

### Session Booking Endpoints

#### 1. Create Payment Intent (Step 1)
```http
POST /api/v1/sessions/{session_id}/create-payment-intent
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment Intent created successfully",
  "data": {
    "client_secret": "pi_xxx_secret_yyy",
    "payment_intent_id": "pi_xxx",
    "amount": 50.00,
    "currency": "USD",
    "session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00",
      "fee": 50.00,
      "mentor": {
        "id": 5,
        "name": "John Mentor"
      }
    },
    "pricing_breakdown": {
      "gross_amount": 50.00,
      "stripe_fee": 1.75,
      "net_amount": 48.25,
      "mentor_amount": 38.60,
      "admin_amount": 9.65
    }
  }
}
```

#### 2. Confirm Booking (Step 3)
```http
POST /api/v1/sessions/{session_id}/confirm-booking
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_intent_id": "pi_xxx"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Session booked successfully",
  "data": {
    "enrollment": {
      "id": 123,
      "enrollment_status": "active",
      "payment_status": "paid",
      "enrolled_at": "2025-10-23T10:30:00.000Z",
      "amount": 50.00,
      "currency": "USD"
    },
    "session": {
      "id": 14,
      "date": "2025-09-21",
      "start_time": "10:00",
      "end_time": "11:00",
      "status": "booked"
    },
    "transaction": {
      "id": 456,
      "transaction_id": "TXN_67890",
      "status": "completed",
      "amount": 50.00,
      "currency": "USD"
    },
    "conversation_code": "CONV_ABC123"
  }
}
```

### Course Enrollment Endpoints

#### 1. Create Payment Intent (Step 1)
```http
POST /api/v1/enrollments/courses/{course_id}/create-payment-intent
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment Intent created successfully",
  "data": {
    "client_secret": "pi_xxx_secret_yyy",
    "payment_intent_id": "pi_xxx",
    "amount": 99.00,
    "currency": "USD",
    "course": {
      "id": 18,
      "title": "Advanced JavaScript",
      "description": "Master JavaScript...",
      "thumbnail": "https://...",
      "price": 149.00,
      "discounted_price": 99.00,
      "discount": 33.56,
      "mentor": {
        "id": 5,
        "name": "John Mentor"
      }
    },
    "pricing_breakdown": {
      "gross_amount": 99.00,
      "stripe_fee": 3.17,
      "net_amount": 95.83,
      "mentor_amount": 76.66,
      "admin_amount": 19.17
    }
  }
}
```

#### 2. Confirm Enrollment (Step 3)
```http
POST /api/v1/enrollments/courses/{course_id}/confirm-enrollment
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_intent_id": "pi_xxx"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully enrolled in course",
  "data": {
    "enrollment": {
      "id": 789,
      "enrollment_status": "active",
      "payment_status": "paid",
      "enrolled_at": "2025-10-23T10:30:00.000Z",
      "amount": 99.00,
      "currency": "USD"
    },
    "course": {
      "id": 18,
      "title": "Advanced JavaScript",
      "thumbnail": "https://..."
    },
    "transaction": {
      "id": 890,
      "transaction_id": "TXN_12345",
      "status": "completed",
      "amount": 99.00,
      "currency": "USD"
    },
    "conversation_code": "CONV_XYZ789"
  }
}
```

---

## 💻 Mobile App Implementation Examples

### Flutter (Dart)

```dart
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class PaymentService {
  final String baseUrl = 'https://yourapi.com';
  final String token; // User's auth token

  PaymentService(this.token);

  // Initialize Stripe (call once on app start)
  static void initStripe() {
    Stripe.publishableKey = "pk_test_YOUR_PUBLISHABLE_KEY";
  }

  // Book a session with two-step payment flow
  Future<Map<String, dynamic>> bookSession(int sessionId, Map<String, String> cardDetails) async {
    try {
      // STEP 1: Create Payment Intent
      final response1 = await http.post(
        Uri.parse('$baseUrl/api/v1/sessions/$sessionId/create-payment-intent'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response1.statusCode != 200) {
        throw Exception('Failed to create payment intent');
      }

      final data = jsonDecode(response1.body);
      final clientSecret = data['data']['client_secret'];
      final paymentIntentId = data['data']['payment_intent_id'];

      // STEP 2: Confirm Payment using Stripe SDK (handles 3DS automatically)
      final paymentIntent = await Stripe.instance.confirmPayment(
        paymentIntentClientSecret: clientSecret,
        data: PaymentMethodParams.card(
          paymentMethodData: PaymentMethodData(
            billingDetails: BillingDetails(
              name: cardDetails['cardholder_name'],
            ),
          ),
        ),
      );

      if (paymentIntent.status != PaymentIntentsStatus.Succeeded) {
        throw Exception('Payment failed: ${paymentIntent.status}');
      }

      // STEP 3: Confirm Booking on Backend
      final response2 = await http.post(
        Uri.parse('$baseUrl/api/v1/sessions/$sessionId/confirm-booking'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'payment_intent_id': paymentIntentId,
        }),
      );

      if (response2.statusCode != 200) {
        throw Exception('Failed to confirm booking');
      }

      return jsonDecode(response2.body);

    } catch (e) {
      throw Exception('Payment error: $e');
    }
  }

  // Enroll in a course with two-step payment flow
  Future<Map<String, dynamic>> enrollInCourse(int courseId, Map<String, String> cardDetails) async {
    try {
      // STEP 1: Create Payment Intent
      final response1 = await http.post(
        Uri.parse('$baseUrl/api/v1/enrollments/courses/$courseId/create-payment-intent'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response1.statusCode != 200) {
        throw Exception('Failed to create payment intent');
      }

      final data = jsonDecode(response1.body);
      final clientSecret = data['data']['client_secret'];
      final paymentIntentId = data['data']['payment_intent_id'];

      // STEP 2: Confirm Payment using Stripe SDK
      final paymentIntent = await Stripe.instance.confirmPayment(
        paymentIntentClientSecret: clientSecret,
        data: PaymentMethodParams.card(
          paymentMethodData: PaymentMethodData(
            billingDetails: BillingDetails(
              name: cardDetails['cardholder_name'],
            ),
          ),
        ),
      );

      if (paymentIntent.status != PaymentIntentsStatus.Succeeded) {
        throw Exception('Payment failed: ${paymentIntent.status}');
      }

      // STEP 3: Confirm Enrollment on Backend
      final response2 = await http.post(
        Uri.parse('$baseUrl/api/v1/enrollments/courses/$courseId/confirm-enrollment'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'payment_intent_id': paymentIntentId,
        }),
      );

      if (response2.statusCode != 200) {
        throw Exception('Failed to confirm enrollment');
      }

      return jsonDecode(response2.body);

    } catch (e) {
      throw Exception('Payment error: $e');
    }
  }
}
```

### React Native (JavaScript)

```javascript
import { useStripe } from '@stripe/stripe-react-native';
import axios from 'axios';

const BASE_URL = 'https://yourapi.com';
const STRIPE_PUBLISHABLE_KEY = 'pk_test_YOUR_PUBLISHABLE_KEY';

// Initialize Stripe (in App.tsx)
// <StripeProvider publishableKey={STRIPE_PUBLISHABLE_KEY}>

export const usePayment = (authToken) => {
  const { confirmPayment } = useStripe();

  const bookSession = async (sessionId, cardDetails) => {
    try {
      // STEP 1: Create Payment Intent
      const response1 = await axios.post(
        `${BASE_URL}/api/v1/sessions/${sessionId}/create-payment-intent`,
        {},
        {
          headers: {
            'Authorization': `Bearer ${authToken}`,
          },
        }
      );

      const { client_secret, payment_intent_id } = response1.data.data;

      // STEP 2: Confirm Payment
      const { paymentIntent, error } = await confirmPayment(client_secret, {
        paymentMethodType: 'Card',
        paymentMethodData: {
          billingDetails: {
            name: cardDetails.cardholderName,
          },
        },
      });

      if (error) {
        throw new Error(error.message);
      }

      if (paymentIntent.status !== 'Succeeded') {
        throw new Error(`Payment failed: ${paymentIntent.status}`);
      }

      // STEP 3: Confirm Booking
      const response2 = await axios.post(
        `${BASE_URL}/api/v1/sessions/${sessionId}/confirm-booking`,
        {
          payment_intent_id: payment_intent_id,
        },
        {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json',
          },
        }
      );

      return response2.data;

    } catch (error) {
      throw error;
    }
  };

  const enrollInCourse = async (courseId, cardDetails) => {
    try {
      // STEP 1: Create Payment Intent
      const response1 = await axios.post(
        `${BASE_URL}/api/v1/enrollments/courses/${courseId}/create-payment-intent`,
        {},
        {
          headers: {
            'Authorization': `Bearer ${authToken}`,
          },
        }
      );

      const { client_secret, payment_intent_id } = response1.data.data;

      // STEP 2: Confirm Payment
      const { paymentIntent, error } = await confirmPayment(client_secret, {
        paymentMethodType: 'Card',
        paymentMethodData: {
          billingDetails: {
            name: cardDetails.cardholderName,
          },
        },
      });

      if (error) {
        throw new Error(error.message);
      }

      if (paymentIntent.status !== 'Succeeded') {
        throw new Error(`Payment failed: ${paymentIntent.status}`);
      }

      // STEP 3: Confirm Enrollment
      const response2 = await axios.post(
        `${BASE_URL}/api/v1/enrollments/courses/${courseId}/confirm-enrollment`,
        {
          payment_intent_id: payment_intent_id,
        },
        {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json',
          },
        }
      );

      return response2.data;

    } catch (error) {
      throw error;
    }
  };

  return { bookSession, enrollInCourse };
};
```

---

## ✅ Benefits of Two-Step Flow

1. **✅ Proper 3D Secure Handling**: Stripe SDK handles authentication automatically
2. **✅ Better UX**: Native authentication modals on mobile
3. **✅ Separation of Concerns**: Payment logic separate from booking logic
4. **✅ Error Recovery**: Can retry payment without recreating intent
5. **✅ PCI Compliance**: Card details never touch your server
6. **✅ Stripe Recommended**: This is Stripe's recommended approach for mobile

---

## 🔒 Security Features

- Card details collected client-side only
- Payment Intent verification on backend
- Customer ownership validation
- Duplicate payment prevention
- Idempotency protection

---

## 🚨 Error Handling

### Common Errors

**Step 1 - Create Payment Intent:**
- `400` - Session/course not available
- `400` - Already enrolled/booked
- `401` - Unauthorized (invalid token)
- `500` - Server error

**Step 2 - Confirm Payment (Client-Side):**
- Card declined
- 3D Secure failed
- Network error

**Step 3 - Confirm Booking/Enrollment:**
- `400` - Payment not completed
- `400` - Payment already processed
- `400` - Session/course no longer available
- `403` - Payment Intent doesn't belong to user

---

## 📝 Notes

- Old single-step endpoints (`/book` and `/enroll`) still work for backward compatibility
- Two-step flow is recommended for all new mobile implementations
- Revenue sharing (80/20) handled automatically
- Stripe Connect transfers to mentors happen automatically if configured
- Conversations created automatically for enrolled students

---

## 📞 Support

For questions or issues, contact the backend team.

**Last Updated:** October 23, 2025
**API Version:** v1

