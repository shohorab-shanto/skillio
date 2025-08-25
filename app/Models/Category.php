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
}
