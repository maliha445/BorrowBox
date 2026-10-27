<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_name',
        'description',
        'category',
        'condition',
        'user_id',
        'owner_name',
        'image',
        'is_available',
        'reward_coins',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'reward_coins' => 'integer',
    ];

    /**
     * Get the user who listed this item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
