<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'unit_id',
        'supplier_id',
        'location_id',
        'stock',
        'min_stock',
        'status',
        'has_batch',    
    ];

    protected function casts(): array
    {
        return [
            'stock'     => 'integer',
            'min_stock' => 'integer',
             'has_batch' => 'boolean',
        ];
    }

    /* ============================================================
    |  RELASI
     |============================================================ */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(StockTransactionDetail::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function opnameDetails(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function batches(): HasMany
{
    return $this->hasMany(ProductBatch::class);
}

public function activeBatches(): HasMany
{
    return $this->hasMany(ProductBatch::class)->where('qty_remaining', '>', 0);
}

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, ?string $keyword)
    {
        return $query->when($keyword, fn ($q) =>
            $q->where(fn ($x) =>
                $x->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%")
            )
        );
    }

    /** Stok menipis: stok <= min_stock DAN stok > 0 */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock')
                     ->where('stock', '>', 0);
    }

    /** Stok habis: stok <= 0 */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    /** Stok aman: stok > min_stock */
    public function scopeSafeStock($query)
    {
        return $query->whereColumn('stock', '>', 'min_stock');
    }

    /* ============================================================
     |  ACCESSOR
     |============================================================ */

    /** Nilai status: 'aman' | 'menipis' | 'habis' */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) return 'habis';
        if ($this->stock <= $this->min_stock) return 'menipis';
        return 'aman';
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'habis'   => 'Habis',
            'menipis' => 'Menipis',
            default   => 'Aman',
        };
    }

    /** Badge HTML (untuk view cepat, opsional) */
    public function getStockStatusBadgeAttribute(): string
    {
        return match ($this->stock_status) {
            'habis'   => '<span class="badge bg-danger">🔴 Habis</span>',
            'menipis' => '<span class="badge bg-warning text-dark">🟡 Menipis</span>',
            default   => '<span class="badge bg-success">🟢 Aman</span>',
        };
    }

    /* ============================================================
     |  BUSINESS LOGIC
     |============================================================ */

    /**
     * Terapkan perubahan stok.
     * Positif = tambah, Negatif = kurangi.
     */
    public function applyStockChange(int $delta): void
    {
        $this->stock = max(0, $this->stock + $delta);
        $this->save();
    }

    /**
     * Hitung semua pergerakan stok untuk kartu stok.
     * Return: Collection of ['date', 'reference', 'type', 'in', 'out', 'notes']
     */
    public function movements(): Collection
    {
        $rows = collect();

        // Mutasi dari transaksi
        foreach ($this->transactionDetails()->with('transaction')->get() as $detail) {
            $trx = $detail->transaction;
            if (! $trx) continue;

            $isStockIn = in_array($trx->type, ['in', 'return_customer'], true);

            $rows->push([
                'date'      => $trx->date,
                'reference' => $trx->code,
                'type'      => $trx->type,
                'in'        => $isStockIn ? $detail->qty : 0,
                'out'       => $isStockIn ? 0 : $detail->qty,
                'notes'     => $detail->notes ?? '',
            ]);
        }

        // Mutasi dari penyesuaian
        foreach ($this->adjustments as $adj) {
            $rows->push([
                'date'      => $adj->date,
                'reference' => $adj->code,
                'type'      => 'adjustment',
                'in'        => $adj->adjustment > 0 ? $adj->adjustment : 0,
                'out'       => $adj->adjustment < 0 ? abs($adj->adjustment) : 0,
                'notes'     => $adj->reason,
            ]);
        }

        return $rows->sortBy(fn ($r) => $r['date']->timestamp ?? strtotime($r['date']))->values();
    }
}