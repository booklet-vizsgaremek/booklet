<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Receipt extends Model
{
    /** @use HasFactory<\Database\Factories\ReceiptFactory> */
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'books_receipts')
            ->withPivot('quantity', 'price_at_purchase');
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupons_receipts');
    }

    public function pickup(): HasOne
    {
        return $this->hasOne(Pickup::class);
    }
}
