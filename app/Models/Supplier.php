<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'pic',
    ];

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeSearch($query, ?string $keyword)
    {
        return $query->when($keyword, fn ($q) =>
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%")
              ->orWhere('pic', 'like', "%{$keyword}%")
        );
    }
}