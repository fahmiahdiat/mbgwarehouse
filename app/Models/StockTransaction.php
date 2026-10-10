<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'date',
        'supplier_id',
        'delivery_note',
        'recipient',
        'purpose',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /* ============================================================
     |  KONSTANTA
     |============================================================ */

    public const TYPE_IN              = 'in';
    public const TYPE_OUT             = 'out';
    public const TYPE_RETURN_SUPPLIER = 'return_supplier';
    public const TYPE_RETURN_CUSTOMER = 'return_customer';

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function details(): HasMany
    {
        return $this->hasMany(StockTransactionDetail::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDate($query, ?string $from, ?string $to)
    {
        return $query->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
                     ->when($to,   fn ($q) => $q->whereDate('date', '<=', $to));
    }

    public function scopeIncoming($query)
    {
        return $query->whereIn('type', [self::TYPE_IN, self::TYPE_RETURN_CUSTOMER]);
    }

    public function scopeOutgoing($query)
    {
        return $query->whereIn('type', [self::TYPE_OUT, self::TYPE_RETURN_SUPPLIER]);
    }

    /* ============================================================
     |  ACCESSOR
     |============================================================ */

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_IN              => 'Barang Masuk',
            self::TYPE_OUT             => 'Barang Keluar',
            self::TYPE_RETURN_SUPPLIER => 'Retur ke Supplier',
            self::TYPE_RETURN_CUSTOMER => 'Retur ke Gudang',
            default                    => 'Tidak Diketahui',
        };
    }

    public function getIsStockInAttribute(): bool
    {
        return in_array($this->type, [self::TYPE_IN, self::TYPE_RETURN_CUSTOMER], true);
    }

    /** Total qty dari semua detail */
    public function getTotalQtyAttribute(): int
    {
        return (int) $this->details->sum('qty');
    }

    /** Total baris item */
    public function getTotalItemsAttribute(): int
    {
        return $this->details->count();
    }

    /* ============================================================
     |  STATIC HELPER
     |============================================================ */

    /**
     * Generate kode transaksi otomatis.
     * Format: {PREFIX}-{YYYYMMDD}-{XXX}
     */
    public static function generateCode(string $type): string
    {
        $prefix = match ($type) {
            self::TYPE_IN              => 'BM',
            self::TYPE_OUT             => 'BK',
            self::TYPE_RETURN_SUPPLIER => 'RS',
            self::TYPE_RETURN_CUSTOMER => 'RG',
            default                    => 'TRX',
        };

        $date = now()->format('Ymd');
        $pattern = "{$prefix}-{$date}-%";

        $last = static::where('code', 'like', $pattern)
                      ->orderByDesc('code')
                      ->value('code');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return sprintf('%s-%s-%03d', $prefix, $date, $seq);
    }

    /** Delta untuk type tertentu: +1 tambah stok, -1 kurangi */
    public static function stockDelta(string $type): int
    {
        return match ($type) {
            self::TYPE_IN, self::TYPE_RETURN_CUSTOMER =>  1,
            self::TYPE_OUT, self::TYPE_RETURN_SUPPLIER => -1,
            default                                    =>  0,
        };
    }
}