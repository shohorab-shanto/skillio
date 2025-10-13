# Postman Collection Update - Sliders Endpoints

## ✅ What Was Added

The Postman collection has been updated to include the new Sliders API endpoints.

---

## 📦 Updated File

**File:** `docs/Skillo_API_Collection.postman_collection.json`

---

## 🆕 New Folder Added

### **Sliders** Folder

**Location in Collection:**
```
Skillo Mobile API
├── Authentication
├── Sliders ← NEW!
│   ├── Get All Sliders
│   └── Get Single Slider
├── Onboarding - Public
└── ... (other endpoints)
```

---

## 📋 New Requests

### 1. **Get All Sliders**

**Method:** `GET`  
**Endpoint:** `/api/v1/sliders`  
**Authentication:** Not required (public endpoint)

**Description:**
> Retrieve all active sliders ordered by position for display in the mobile app carousel/banner. No authentication required. Returns only active sliders with full image URLs ready to use.

**Sample Responses Included:**
- ✅ Success Response (200 OK) - With sliders data
- ✅ Empty Response (200 OK) - When no sliders exist

**Success Response Example:**
```json
{
  "success": true,
  "message": "Sliders retrieved successfully",
  "data": {
    "sliders": [
      {
        "id": 1,
        "title": null,
        "image_url": "http://localhost:8000/storage/sliders/banner1.jpg",
        "link": null,
        "description": null,
        "order": 0
      },
      {
        "id": 2,
        "title": null,
        "image_url": "http://localhost:8000/storage/sliders/banner2.jpg",
        "link": null,
        "description": null,
        "order": 1
      }
    ],
    "total": 2
  }
}
```

---

### 2. **Get Single Slider**

**Method:** `GET`  
**Endpoint:** `/api/v1/sliders/{id}`  
**Authentication:** Not required (public endpoint)

**URL Parameters:**
- `id` (required): The slider ID (e.g., `1`)

**Description:**
> Get details of a specific slider by ID. Returns full slider information including status.

**Sample Responses Included:**
- ✅ Success Response (200 OK)
- ✅ Not Found (404) - When slider doesn't exist

**Success Response Example:**
```json
{
  "success": true,
  "message": "Slider retrieved successfully",
  "data": {
    "slider": {
      "id": 1,
      "title": null,
      "image_url": "http://localhost:8000/storage/sliders/banner1.jpg",
      "link": null,
      "description": null,
      "order": 0,
      "status": "active"
    }
  }
}
```

**Error Response Example (404):**
```json
{
  "success": false,
  "message": "Slider not found"
}
```

---

## 🧪 How to Use in Postman

### Step 1: Import Updated Collection

1. Open Postman
2. Click **Import**
3. Select `docs/Skillo_API_Collection.postman_collection.json`
4. Click **Import** (if already imported, it will update)

### Step 2: Find Sliders Endpoints

Navigate to:
```
Skillo Mobile API → Sliders
```

You'll see:
- Get All Sliders
- Get Single Slider

### Step 3: Test the Endpoints

**Test "Get All Sliders":**
1. Select **Get All Sliders** request
2. Click **Send**
3. View response (should return all active sliders)

**Test "Get Single Slider":**
1. Select **Get Single Slider** request
2. The URL is set to `/api/v1/sliders/1`
3. Change the ID in the URL if needed
4. Click **Send**
5. View response

---

## 📊 Collection Structure

```
Skillo Mobile API Collection
├── Authentication (9 requests)
├── Sliders (2 requests) ← NEW!
│   ├── Get All Sliders
│   │   ├── Success Response
│   │   └── Empty Response
│   └── Get Single Slider
│       ├── Success Response
│       └── Not Found
├── Onboarding - Public
├── Home Page Data
├── Course Details
├── Mentor Details
└── ... (other endpoints)
```

---

## 🔑 Environment Variables

The Sliders endpoints use the existing environment variables:

| Variable | Usage | Example |
|----------|-------|---------|
| `{{base_url}}` | API base URL | `http://localhost:8000` |

No authentication token required for Sliders endpoints.

---

## 📝 Request Details

### Headers Used

Both requests include:
```json
{
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```

### Query Parameters

None required. The endpoints return all active sliders in the correct order.

---

## 💡 Testing Tips

### Tip 1: Verify Response Data

Check that the response includes:
- ✅ `success: true`
- ✅ `sliders` array
- ✅ Full `image_url` (not relative path)
- ✅ Sliders ordered by `order` field (0, 1, 2...)
- ✅ `total` count matches array length

### Tip 2: Test Edge Cases

**Empty State:**
- Test when no sliders exist
- Should return empty array with `total: 0`

**Invalid ID:**
- Test with `/api/v1/sliders/999`
- Should return 404 error

### Tip 3: Mobile App Integration

Use the response directly in your mobile app:
```javascript
const sliders = response.data.sliders;
sliders.forEach(slider => {
  // slider.image_url is ready to use
  // slider.order determines display position
});
```

---

## 🎯 What's Different

### Compared to Other Endpoints:

| Feature | Sliders | Most Other Endpoints |
|---------|---------|----------------------|
| **Authentication** | ❌ Not required | ✅ Required |
| **User-specific** | ❌ Same for all | ✅ User-specific |
| **Caching** | ✅ Recommended | Varies |
| **Update Frequency** | Low (admin updates) | High (user actions) |

---

## 📚 Related Documentation

- **API Documentation:** `docs/api-documentation.md` (Sliders section)
- **Feature Guide:** `docs/SLIDER_FEATURE_DOCUMENTATION.md`
- **Implementation Summary:** `SLIDER_IMPLEMENTATION_COMPLETE.md`

---

## ✅ Validation

The updated Postman collection has been validated:

```bash
✅ JSON is valid
✅ All endpoints included
✅ Sample responses added
✅ Descriptions complete
```

---

## 🔄 Changelog

**Version:** Updated on 2025-10-13

**Changes:**
- ✅ Added "Sliders" folder
- ✅ Added "Get All Sliders" request with 2 sample responses
- ✅ Added "Get Single Slider" request with 2 sample responses
- ✅ Added folder description
- ✅ Positioned after Authentication, before Onboarding

---

## 🚀 Ready to Use

The Postman collection is now complete with:
- ✅ All slider endpoints documented
- ✅ Sample requests ready to test
- ✅ Multiple response examples
- ✅ Clear descriptions

**Import and start testing!** 🎉


