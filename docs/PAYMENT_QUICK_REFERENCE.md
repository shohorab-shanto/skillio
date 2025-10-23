# Payment API Quick Reference

## 🚀 Quick Start

### For Mobile Developers

**You need:**
1. Stripe Publishable Key: `pk_test_...` (ask backend team)
2. User's auth token from login
3. Stripe SDK installed in your app

**Payment Flow:**
```
Create Intent → Confirm Payment → Confirm Booking/Enrollment
(Backend)       (Mobile SDK)      (Backend)
```

---

## 📱 Session Booking

### Step 1: Create Payment Intent
```http
POST /api/v1/sessions/{id}/create-payment-intent
Authorization: Bearer {token}
```

**Response:** Get `client_secret`

### Step 2: Confirm Payment (Your Code)
```dart
// Flutter example
await Stripe.instance.confirmPayment(
  paymentIntentClientSecret: clientSecret,
  data: PaymentMethodParams.card(...)
);
```

### Step 3: Confirm Booking
```http
POST /api/v1/sessions/{id}/confirm-booking
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_intent_id": "pi_xxx"
}
```

---

## 📚 Course Enrollment

### Step 1: Create Payment Intent
```http
POST /api/v1/enrollments/courses/{id}/create-payment-intent
Authorization: Bearer {token}
```

**Response:** Get `client_secret`

### Step 2: Confirm Payment (Your Code)
```dart
// Flutter example
await Stripe.instance.confirmPayment(
  paymentIntentClientSecret: clientSecret,
  data: PaymentMethodParams.card(...)
);
```

### Step 3: Confirm Enrollment
```http
POST /api/v1/enrollments/courses/{id}/confirm-enrollment
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_intent_id": "pi_xxx"
}
```

---

## 🧪 Test Cards

| Card Number | Result |
|-------------|--------|
| 4242 4242 4242 4242 | ✅ Success |
| 4000 0027 6000 3184 | ✅ Success (with 3DS) |
| 4000 0000 0000 0002 | ❌ Declined |

---

## 🔑 Stripe Keys Location

**Backend:** `.env` file
```
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
```

**Mobile App:** Ask backend team for publishable key

---

## ⚠️ Common Errors

| Code | Message | Fix |
|------|---------|-----|
| 400 | Already enrolled | User already paid for this |
| 400 | Not available | Session/course sold out |
| 400 | Payment not completed | Payment failed, retry |
| 403 | Unauthorized | Wrong payment_intent_id |

---

## 📖 Full Documentation

- **Mobile Flow:** `docs/MOBILE_PAYMENT_FLOW.md`
- **Changes:** `docs/PAYMENT_API_CHANGES.md`
- **Postman:** `docs/Skillo_API_Collection.postman_collection.json`

---

## 💡 Tips

1. **Always handle 3D Secure** - Stripe SDK does this automatically
2. **Check payment status** - Must be "succeeded" before Step 3
3. **Don't retry on success** - Check if already enrolled first
4. **Test with test cards** - Use test mode until ready
5. **Keep payment_intent_id** - Needed for Step 3

---

## 🆘 Need Help?

1. Check error message
2. Verify auth token is valid
3. Test with Stripe test cards
4. Check Postman collection for examples
5. Contact backend team

---

**Last Updated:** October 23, 2025

