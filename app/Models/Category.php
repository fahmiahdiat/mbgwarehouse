<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_monitored',
    ];

    protected function casts(): array
    {
        return [
            'is_monitored' => 'boolean',
        ];
    }

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeSearch($query, ?string $keyword)
    {
        return $query->when($keyword, fn ($q) =>
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
        );
    }

    public function scopeMonitored($query)
    {
        return $query->where('is_monitored', true);
    }
}