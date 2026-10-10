<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transaction_id',
        'product_id',
        'batch_id',
        'qty',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
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
     |  ACCESSOR
     |============================================================ */

    /** Tanda + / - sesuai arah mutasi */
    public function getDirectionAttribute(): string
    {
        $type = $this->transaction?->type;
        return in_array($type, [StockTransaction::TYPE_IN, StockTransaction::TYPE_RETURN_CUSTOMER], true)
            ? 'in'
            : 'out';
    }
}