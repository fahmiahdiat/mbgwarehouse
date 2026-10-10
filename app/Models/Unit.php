<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
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

    /** Nama + simbol, contoh: "Kilogram (Kg)" */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->symbol})";
    }
}