<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function subCategories()
    {
        return $this->hasMany(\App\Models\SubCategory::class);
    }

    public function courses()
    {
        return $this->hasMany(\App\Models\Course::class);
    }

    public function sessionBookings()
    {
        return $this->hasMany(\App\Models\SessionBooking::class);
    }

    /**
     * Get the category image URL or return a default icon
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        
        return null; // Return null to indicate no image
    }

    /**
     * Check if category has a valid image
     */
    public function hasValidImage()
    {
        return $this->image && file_exists(public_path('storage/' . $this->image));
    }
}
