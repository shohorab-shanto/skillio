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
}
