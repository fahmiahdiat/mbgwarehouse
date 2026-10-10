<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'date',
        'product_id',
        'batch_id',
        'adjustment',
        'reason',
        'reference',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date'       => 'date',
            'adjustment' => 'integer',
        ];
    }

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
{
    return $this->belongsTo(ProductBatch::class, 'batch_id');
}

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeBetweenDate($query, ?string $from, ?string $to)
    {
        return $query->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
                     ->when($to,   fn ($q) => $q->whereDate('date', '<=', $to));
    }

    public function scopeIncreases($query)
    {
        return $query->where('adjustment', '>', 0);
    }

    public function scopeDecreases($query)
    {
        return $query->where('adjustment', '<', 0);
    }

    /* ============================================================
     |  ACCESSOR
     |============================================================ */

    /** Format: "+5" atau "-3" */
    public function getFormattedAdjustmentAttribute(): string
    {
        return ($this->adjustment > 0 ? '+' : '') . $this->adjustment;
    }

    public function getDirectionAttribute(): string
    {
        return $this->adjustment >= 0 ? 'in' : 'out';
    }

    /* ============================================================
     |  STATIC HELPER
     |============================================================ */

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $pattern = "ADJ-{$date}-%";

        $last = static::where('code', 'like', $pattern)
                      ->orderByDesc('code')
                      ->value('code');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return sprintf('ADJ-%s-%03d', $date, $seq);
    }
}