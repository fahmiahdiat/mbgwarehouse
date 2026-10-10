<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_code',
        'entry_date',
        'expired_date',
        'qty_initial',
        'qty_remaining',
        'price',
        'source_type',
        'source_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'entry_date'     => 'date',
            'expired_date'   => 'date',
            'qty_initial'    => 'integer',
            'qty_remaining'  => 'integer',
            'price'          => 'decimal:2',
        ];
    }

    // ============================================================
    // RELASI
    // ============================================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(StockTransactionDetail::class, 'batch_id');
    }

    // ============================================================
    // SCOPE
    // ============================================================

    /** Batch yang masih ada sisa */
    public function scopeAvailable($query)
    {
        return $query->where('qty_remaining', '>', 0);
    }

    /** Batch yang sudah expired */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expired_date')
                     ->whereDate('expired_date', '<', now());
    }

    /** Batch yang akan expired dalam N hari */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expired_date')
                     ->whereDate('expired_date', '>=', now())
                     ->whereDate('expired_date', '<=', now()->addDays($days));
    }

    /** FEFO ordering: expired dulu, baru entry_date */
    public function scopeFefo($query)
    {
        return $query->available()
                     ->orderByRaw('expired_date IS NULL, expired_date ASC')
                     ->orderBy('entry_date')
                     ->orderBy('id');
    }

    // ============================================================
    // ACCESSOR
    // ============================================================

    /** Status expiry: 'aman', 'segera', 'expired', 'tanpa_expired' */
    public function getExpiryStatusAttribute(): string
    {
        if (! $this->expired_date) return 'tanpa_expired';

        $days = now()->diffInDays($this->expired_date, false);

        if ($days < 0)  return 'expired';
        if ($days <= 30) return 'segera';
        return 'aman';
    }

    public function getExpiryStatusLabelAttribute(): string
    {
        return match ($this->expiry_status) {
            'expired'       => 'Sudah Expired',
            'segera'        => 'Segera Expired',
            'aman'          => 'Aman',
            'tanpa_expired' => 'Tanpa Expired',
        };
    }

    public function getExpiryBadgeAttribute(): string
    {
        return match ($this->expiry_status) {
            'expired'       => '<span class="badge bg-danger">🔴 Expired</span>',
            'segera'        => '<span class="badge bg-warning text-dark">🟡 Segera</span>',
            'aman'          => '<span class="badge bg-success">🟢 Aman</span>',
            'tanpa_expired' => '<span class="badge bg-secondary">—</span>',
        };
    }

    public function getDaysUntilExpiredAttribute(): ?int
    {
        if (! $this->expired_date) return null;
        return (int) now()->diffInDays($this->expired_date, false);
    }

    // ============================================================
    // STATIC HELPER
    // ============================================================

    /**
     * Generate kode batch unik per produk + tanggal.
     * Format: BRG001-20261008-001
     */
    public static function generateCode(string $productCode, Carbon $date): string
    {
        $dateStr = $date->format('Ymd');
        $prefix  = "{$productCode}-{$dateStr}-";

        $last = static::where('batch_code', 'like', "{$prefix}%")
                      ->orderByDesc('batch_code')
                      ->value('batch_code');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return sprintf('%s%03d', $prefix, $seq);
    }
}