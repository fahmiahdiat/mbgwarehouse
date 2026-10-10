<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'batch_id', 
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'system_stock'   => 'integer',
            'physical_stock' => 'integer',
            'difference'     => 'integer',
        ];
    }

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function opname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
{
    return $this->belongsTo(ProductBatch::class, 'batch_id');
}

    /* ============================================================
     |  BOOT — otomatis hitung selisih
     |============================================================ */

    protected static function booted(): void
    {
        static::saving(function (self $detail) {
            if ($detail->physical_stock !== null) {
                $detail->difference = $detail->physical_stock - $detail->system_stock;
            }
        });
    }

    /* ============================================================
     |  ACCESSOR
     |============================================================ */

    public function getDifferenceLabelAttribute(): string
    {
        if ($this->difference === 0) return 'Sesuai';
        return $this->difference > 0 ? 'Lebih' : 'Kurang';
    }

    public function getDifferenceBadgeAttribute(): string
    {
        return match (true) {
            $this->difference === 0 => '<span class="badge bg-secondary">Sesuai</span>',
            $this->difference > 0   => '<span class="badge bg-success">Lebih</span>',
            default                 => '<span class="badge bg-danger">Kurang</span>',
        };
    }
}