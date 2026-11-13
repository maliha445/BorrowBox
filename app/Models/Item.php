<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Item extends Model
{
    protected $fillable = [
        'item_name',
        'description',
        'category',
        'category_id',
        'condition',
        'user_id',
        'owner_name',
        'image',
        'is_available',
        'status',
        'reward_coins',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'reward_coins' => 'integer',
    ];

    /**
     * Get the user who listed this item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category relation
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function borrowRequests(): HasMany
    {
        return $this->hasMany(BorrowRequest::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, BorrowRequest::class, 'item_id', 'borrow_request_id');
    }

    public function averageRating(): float
    {
        return (float) ($this->reviews()->avg('rating') ?? 0);
    }

    public function isAvailable(): bool
    {
        return (bool) $this->is_available && $this->status === 'available';
    }

    public function getCategoryDisplayNameAttribute(): string
    {
        return $this->categoryRelation?->name ?? $this->category ?? 'Other';
    }
}
