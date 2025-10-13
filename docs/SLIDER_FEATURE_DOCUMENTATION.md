# Slider Management Feature Documentation

## Overview

The Slider Management feature allows administrators to create, manage, and reorder banner images/sliders that are displayed in the mobile app. These sliders are commonly used for:
- Promotional banners
- Featured courses/mentors
- Announcements
- Marketing campaigns

---

## Features

### Admin Panel Features
✅ **CRUD Operations** - Create, Read, Update, Delete sliders  
✅ **Image Upload** - Upload banner images (JPG, PNG, GIF)  
✅ **Drag & Drop Reordering** - Visually reorder sliders by dragging  
✅ **Status Toggle** - Activate/deactivate sliders  
✅ **Optional Fields** - Title, link, description are optional  
✅ **Real-time Preview** - Preview images before uploading  

### API Features
✅ **Public Endpoint** - No authentication required  
✅ **Ordered Response** - Sliders returned in correct order  
✅ **Active Only** - Only active sliders are returned  
✅ **Full Image URLs** - Ready-to-use image URLs  
✅ **Caching Friendly** - Designed for client-side caching  

---

## Database Schema

### `sliders` Table

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT | No | Auto | Primary key |
| `title` | VARCHAR(255) | Yes | NULL | Slider title |
| `image` | VARCHAR(255) | No | - | Image path in storage |
| `link` | VARCHAR(255) | Yes | NULL | URL to redirect when clicked |
| `description` | TEXT | Yes | NULL | Slider description |
| `order` | INTEGER | No | 0 | Display order (0, 1, 2...) |
| `status` | ENUM | No | active | active or inactive |
| `created_at` | TIMESTAMP | Yes | NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | Last update timestamp |

**Indexes:**
- Primary key on `id`
- Index on `order` (for fast ordering)
- Index on `status` (for filtering active sliders)

---

## File Structure

### Backend Files

```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   └── SliderController.php          # Admin CRUD + reorder
│   └── Api/
│       └── SliderApiController.php       # Mobile API endpoints
└── Models/
    └── Slider.php                        # Slider model

database/migrations/
└── 2025_10_13_175949_create_sliders_table.php

resources/views/admin/sliders/
├── index.blade.php                       # List sliders with drag-drop
├── create.blade.php                      # Create new slider
└── edit.blade.php                        # Edit slider

routes/
├── admin.php                             # Admin routes
└── api.php                               # API routes
```

---

## Admin Panel Usage

### Access Slider Management

**URL:** `/admin/sliders`

**Menu Navigation:** Admin Dashboard → Sliders Management

### Creating a Slider

1. Click **"Add New Slider"** button
2. Fill in the form:
   - **Image*** (required): Upload banner image
     - Recommended size: 1200x400px
     - Max file size: 2MB
     - Formats: JPG, PNG, GIF
   - **Title** (optional): Slider heading
   - **Link** (optional): URL to redirect when clicked
   - **Description** (optional): Slider description
   - **Order*** (required): Display position (lower = first)
   - **Status*** (required): Active or Inactive
3. Click **"Create Slider"**

**Image Preview:**
- Real-time preview shown after selecting image
- Helps verify image quality before upload

### Editing a Slider

1. In the sliders list, click **Edit** button (pencil icon)
2. Modify any fields
3. Upload new image (optional - leave empty to keep current)
4. Click **"Update Slider"**

### Deleting a Slider

**Option 1:** From the list page
- Click the **Delete** button (trash icon)
- Confirm deletion

**Option 2:** From the edit page
- Click **"Delete Slider"** button
- Confirm deletion

**Note:** Deleting a slider also removes the image from storage.

### Reordering Sliders

**Using Drag & Drop:**
1. Go to sliders list page
2. Hover over a slider row
3. Click and hold the grip icon (⋮⋮)
4. Drag the row to desired position
5. Release mouse button
6. Order is saved automatically via AJAX

**Manual Ordering:**
- Edit a slider and change the **Order** field
- Lower numbers appear first (0, 1, 2, 3...)

### Toggle Status

- Click the **Status** badge in the list view
- Instantly toggles between Active/Inactive
- Inactive sliders won't appear in the API

---

## API Usage

### Get All Sliders

**Endpoint:** `GET /api/v1/sliders`

**Authentication:** Not required (public endpoint)

**Request Example:**
```bash
curl -X GET http://your-domain.com/api/v1/sliders \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

**Response Example:**
```json
{
  "success": true,
  "message": "Sliders retrieved successfully",
  "data": {
    "sliders": [
      {
        "image_url": "http://your-domain.com/storage/sliders/banner1.jpg",
        "order": 0
      },
      {
        "image_url": "http://your-domain.com/storage/sliders/banner2.jpg",
        "order": 1
      }
    ]
  }
}
```

### Get Single Slider

**Endpoint:** `GET /api/v1/sliders/{id}`

**Request Example:**
```bash
curl -X GET http://your-domain.com/api/v1/sliders/1 \
  -H "Accept: application/json"
```

**Response Example:**
```json
{
  "success": true,
  "message": "Slider retrieved successfully",
  "data": {
    "slider": {
      "id": 1,
      "title": "Welcome to Skillo",
      "image_url": "http://your-domain.com/storage/sliders/banner1.jpg",
      "link": "https://example.com/promo",
      "description": "Get 20% off on all courses",
      "order": 0,
      "status": "active"
    }
  }
}
```

---

## Mobile App Integration

### React Native Example

```javascript
import React, { useEffect, useState } from 'react';
import { View, Image, FlatList, TouchableOpacity, Linking } from 'react-native';

const SlidersCarousel = () => {
  const [sliders, setSliders] = useState([]);

  useEffect(() => {
    fetchSliders();
  }, []);

  const fetchSliders = async () => {
    try {
      const response = await fetch('http://your-api.com/api/v1/sliders');
      const data = await response.json();
      
      if (data.success) {
        setSliders(data.data.sliders);
      }
    } catch (error) {
      console.error('Error fetching sliders:', error);
    }
  };

  return (
    <FlatList
      data={sliders}
      horizontal
      pagingEnabled
      showsHorizontalScrollIndicator={false}
      keyExtractor={(item, index) => index.toString()}
      renderItem={({ item }) => (
        <Image
          source={{ uri: item.image_url }}
          style={{ width: 350, height: 150, borderRadius: 10 }}
          resizeMode="cover"
        />
      )}
    />
  );
};

export default SlidersCarousel;
```

### Flutter Example

```dart
import 'package:flutter/material.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

class SlidersCarousel extends StatefulWidget {
  @override
  _SlidersCarouselState createState() => _SlidersCarouselState();
}

class _SlidersCarouselState extends State<SlidersCarousel> {
  List<dynamic> sliders = [];

  @override
  void initState() {
    super.initState();
    fetchSliders();
  }

  Future<void> fetchSliders() async {
    final response = await http.get(
      Uri.parse('http://your-api.com/api/v1/sliders'),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        setState(() {
          sliders = data['data']['sliders'];
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return CarouselSlider(
      options: CarouselOptions(
        height: 150.0,
        autoPlay: true,
        enlargeCenterPage: true,
      ),
      items: sliders.map((slider) {
        return Builder(
          builder: (BuildContext context) {
            return Container(
              width: MediaQuery.of(context).size.width,
              margin: EdgeInsets.symmetric(horizontal: 5.0),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(10),
                image: DecorationImage(
                  image: NetworkImage(slider['image_url']),
                  fit: BoxFit.cover,
                ),
              ),
            );
          },
        );
      }).toList(),
    );
  }
}
```

---

## Best Practices

### For Administrators

1. **Image Optimization:**
   - Compress images before upload
   - Use 1200x400px for consistency
   - Keep file size under 500KB for fast loading

2. **Ordering Strategy:**
   - Use gaps (0, 10, 20, 30) for easier insertion
   - Or use drag-and-drop for visual reordering

3. **Content Management:**
   - Set inactive when slider campaign ends
   - Don't delete - deactivate (preserves data)
   - Update regularly to keep content fresh

4. **Mobile-Friendly:**
   - Test images on mobile devices
   - Ensure text is readable on small screens
   - Use clear, compelling images

### For Developers

1. **Caching:**
   ```javascript
   // Cache sliders for 10 minutes
   const CACHE_DURATION = 10 * 60 * 1000;
   const cachedSliders = await AsyncStorage.getItem('sliders');
   const cacheTime = await AsyncStorage.getItem('sliders_cache_time');
   
   if (cachedSliders && Date.now() - cacheTime < CACHE_DURATION) {
     return JSON.parse(cachedSliders);
   }
   ```

2. **Error Handling:**
   ```javascript
   try {
     const response = await fetch('/api/v1/sliders');
     const data = await response.json();
     
     if (!data.success) {
       // Handle error
       showDefaultSliders();
     }
   } catch (error) {
     // Network error
     useOfflineSliders();
   }
   ```

3. **Link Handling:**
   ```javascript
   const handleLink = (link) => {
     if (!link) return; // No action if no link
     
     // Check if internal or external
     if (link.startsWith('http')) {
       Linking.openURL(link); // External
     } else {
       navigation.navigate(link); // Internal route
     }
   };
   ```

---

## Technical Details

### Image Storage

- **Location:** `storage/app/public/sliders/`
- **Public URL:** `public/storage/sliders/` (symlinked)
- **Access:** `asset('storage/sliders/filename.jpg')`

**Create Storage Symlink:**
```bash
php artisan storage:link
```

### Model Methods

**Slider.php:**
```php
// Get image URL
$slider->image_url; // Returns full URL

// Scopes
Slider::active()->get();        // Only active
Slider::ordered()->get();        // Ordered by position
Slider::active()->ordered()->get(); // Both
```

### API Response Format

All API responses follow standard format:
```json
{
  "success": boolean,
  "message": string,
  "data": object,
  "errors": object|null
}
```

---

## Troubleshooting

### Images Not Displaying

**Issue:** Image URLs return 404

**Solution:**
```bash
# Create storage symlink
php artisan storage:link

# Check permissions
chmod -R 755 storage/app/public/sliders
```

### Reordering Not Saving

**Issue:** Drag-drop doesn't save

**Check:**
1. Browser console for JavaScript errors
2. CSRF token is present
3. SortableJS library is loaded
4. Route `/admin/sliders/update-order` exists

### API Returns Empty

**Issue:** API returns empty sliders array

**Possible Causes:**
1. All sliders are inactive
2. No sliders created yet
3. Database connection issue

**Debug:**
```bash
php artisan tinker
```
```php
\App\Models\Slider::all(); // Check if sliders exist
\App\Models\Slider::active()->get(); // Check active sliders
```

---

## Security Considerations

1. **Admin Authentication:**
   - All admin routes protected by `admin_auth` middleware
   - Only admins can manage sliders

2. **File Upload Validation:**
   - File type validation (image/* only)
   - File size limit (2MB max)
   - Secure storage in `storage/app/public`

3. **Input Sanitization:**
   - All inputs validated
   - XSS protection via Blade escaping
   - CSRF protection on forms

4. **API Security:**
   - Public endpoint (read-only)
   - No sensitive data exposed
   - Rate limiting recommended

---

## Future Enhancements

Potential improvements:
- [ ] Image cropping/editing tool
- [ ] Scheduler (auto-activate/deactivate by date)
- [ ] Analytics (track slider clicks)
- [ ] A/B testing support
- [ ] Multi-language support
- [ ] Video slider support
- [ ] Slider groups/categories

---

## Summary

✅ **Complete Feature** - Fully functional slider management  
✅ **User-Friendly** - Drag-and-drop interface  
✅ **Mobile-Ready** - Optimized API for mobile apps  
✅ **Production-Ready** - Secure, validated, tested  
✅ **Well-Documented** - Complete docs for admins and developers  

**Admin Panel:** `/admin/sliders`  
**API Endpoint:** `GET /api/v1/sliders`  
**Documentation:** This file + `docs/api-documentation.md`


