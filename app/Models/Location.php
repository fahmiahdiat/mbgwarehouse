<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /* ============================================================
     |  ACCESSOR
     |============================================================ */

    /** "A1 — Rak A1" */
    public function getFullNameAttribute(): string
    {
        return $this->code
            ? "{$this->code} — {$this->name}"
            : $this->name;
    }
}