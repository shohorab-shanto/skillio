<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'category_id', 
        'title',
        'description',
        'thumbnail',
        'cover_photo',
        'price',
        'discount',
        'duration_days',
        'start_date',
        'end_date',
        'status',
        'rejection_reason',
        'needs_reapproval',
        'featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'duration_days' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'needs_reapproval' => 'boolean',
        'featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'courses_sub_categories');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->whereHas('subCategories', function ($q) use ($subCategoryId) {
            $q->where('sub_category_id', $subCategoryId);
        });
    }

    public function scopeBySubCategories($query, array $subCategoryIds)
    {
        return $query->whereHas('subCategories', function ($q) use ($subCategoryIds) {
            $q->whereIn('sub_category_id', $subCategoryIds);
        });
    }

    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeNeedsReapproval($query)
    {
        return $query->where('needs_reapproval', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function isApproved(): bool
    {
        return $this->status == 'approved';
    }

    public function isPending(): bool
    {
        return $this->status == 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status == 'rejected';
    }

    public function isFeatured(): bool
    {
        return $this->featured == true;
    }

    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount > 0) {
            return $this->price - ($this->price * ($this->discount / 100));
        }
        return $this->price;
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function totalReviews()
    {
        return $this->reviews()->count();
    }

    /**
     * Get the total number of enrolled students for this course
     */
    public function enrolledStudentsCount()
    {
        return UserEnrollment::where('enrollable_type', Course::class)
            ->where('enrollable_id', $this->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->count();
    }

    /**
     * Get the paginated list of enrolled students for this course
     */
    public function enrolledStudents($perPage = 10)
    {
        return UserEnrollment::where('enrollable_type', Course::class)
            ->where('enrollable_id', $this->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get the count of currently active enrolled students
     */
    public function currentlyEnrolledCount()
    {
        return UserEnrollment::where('enrollable_type', Course::class)
            ->where('enrollable_id', $this->id)
            ->where('enrollment_status', 'active')
            ->count();
    }

    /**
     * Get the total income from this course
     */
    public function totalIncome()
    {
        return PaymentTransaction::whereHas('enrollments', function($query) {
            $query->where('enrollable_type', Course::class)
                  ->where('enrollable_id', $this->id);
        })->sum('mentor_amount');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        return $this->cover_photo ? asset('storage/' . $this->cover_photo) : null;
    }

    public function markForReapproval(): void
    {
        $this->update(['needs_reapproval' => true]);
    }

    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'needs_reapproval' => false,
            'rejection_reason' => null,
        ]);
    }

    public function toggleFeatured(): void
    {
        $this->update(['featured' => !$this->featured]);
    }

    public function markAsFeatured(): void
    {
        $this->update(['featured' => true]);
    }

    public function removeFeatured(): void
    {
        $this->update(['featured' => false]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'needs_reapproval' => false,
        ]);
    }
}
