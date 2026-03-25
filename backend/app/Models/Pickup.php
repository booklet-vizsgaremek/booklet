<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pickup extends Model
{
    /** @use HasFactory<\Database\Factories\PickupFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'receipt_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }
}
