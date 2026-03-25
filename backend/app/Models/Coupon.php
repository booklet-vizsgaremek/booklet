<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'book_id',
        'genre_id',
        'user_id',
        'discount',
        'starts_at',
        'ends_at',
        'code'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receipts(): BelongsToMany
    {
        return $this->belongsToMany(Receipt::class, 'coupons_receipts');
    }
}
