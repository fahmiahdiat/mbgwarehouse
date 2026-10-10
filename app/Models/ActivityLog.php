<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'reference',
    ];

    /* ============================================================
     |  RELASI
     |============================================================ */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ============================================================
     |  SCOPE
     |============================================================ */

    public function scopeSearch($query, ?string $keyword)
    {
        return $query->when($keyword, fn ($q) =>
            $q->where('action', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('reference', 'like', "%{$keyword}%")
        );
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /* ============================================================
     |  STATIC HELPER
     |============================================================ */

    /**
     * Catat aktivitas user.
     *
     * Contoh:
     *   ActivityLog::record('Tambah Barang', 'Menambah barang Beras', 'BRG001');
     */
    public static function record(
        string $action,
        string $description = '',
        ?string $reference = null
    ): self {
        return static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'reference'   => $reference,
        ]);
    }
}