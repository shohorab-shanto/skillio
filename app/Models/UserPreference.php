<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'education_type',
        'category_id',
        'sub_category_id',
        'country',
        'city',
        'wants_courses',
        'wants_mentoring',
    ];

    /**
     * Get the user that owns the preference.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category for this preference.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory for this preference.
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
