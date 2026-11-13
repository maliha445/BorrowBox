<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BorrowRequest extends Model
{
    protected $fillable = [
        'item_id',
        'borrower_id',
        'owner_id',
        'start_date',
        'due_date',
        'message',
        'status',
        'approved_at',
        'returned_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function hasReviewBy(int $userId): bool
    {
        return $this->reviews()->where('reviewer_id', $userId)->exists();
    }
}
