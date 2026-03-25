<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'biography'
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
