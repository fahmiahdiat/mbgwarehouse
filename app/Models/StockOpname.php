<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'date',
        'status',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_COMPLETED = 'completed';

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function details(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeBetweenDate($query, ?string $from, ?string $to)
    {
        return $query->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
                     ->when($to,   fn ($q) => $q->whereDate('date', '<=', $to));
    }

    /* ============================================================
     |  ACCESSOR
     |============================================================ */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'Selesai',
            default                => 'Draft',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => '<span class="badge bg-success">Selesai</span>',
            default                => '<span class="badge bg-secondary">Draft</span>',
        };
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->details->count();
    }

    public function getTotalDifferenceAttribute(): int
    {
        return (int) $this->details->sum('difference');
    }

    public function getHasDifferenceAttribute(): bool
    {
        return $this->details->contains(fn ($d) => $d->difference != 0);
    }

    /* ============================================================
     |  STATIC HELPER
     |============================================================ */

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $pattern = "SO-{$date}-%";

        $last = static::where('code', 'like', $pattern)
                      ->orderByDesc('code')
                      ->value('code');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return sprintf('SO-%s-%03d', $date, $seq);
    }
}