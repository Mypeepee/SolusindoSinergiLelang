<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class EventInvite extends Model
{
    protected $table = 'event_invites';
    protected $primaryKey = 'id_invite';
    public $timestamps = false; // kita pakai kolom custom: tanggal_dibuat/diupdate

    // Jangan mass-assign primary key
    protected $guarded = ['id_invite'];

    protected $fillable = [
        'id_event',
        'id_account',
        'status',
        'urutan',
        'mulai_giliran',
        'selesai_giliran',
        'status_giliran',
        'tanggal_dibuat',
        'tanggal_diupdate',
    ];

    /** Casts modern (Laravel >=8/9) */
    protected $casts = [
        'urutan'           => 'integer',
        'mulai_giliran'    => 'datetime',
        'selesai_giliran'  => 'datetime',
        'tanggal_dibuat'   => 'datetime',
        'tanggal_diupdate' => 'datetime',
        // enum kolom disimpan sebagai string; kalau nanti pakai PHP 8.1 Enum bisa diubah
        'status'           => 'string',        // 'Diundang' | 'Hadir' | 'Tidak Hadir'
        'status_giliran'   => 'string',        // 'Menunggu' | 'Berjalan' | 'Selesai'
    ];

    // ==== RELATIONSHIPS ====

    /** Invite ke event tertentu */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    /** Invite untuk account tertentu */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_account', 'id_account');
    }

    // ==== SCOPES YANG SERING DIPAKAI ====

    /** Filter per event */
    public function scopeForEvent($q, $idEvent)
    {
        return $q->where('id_event', $idEvent);
    }

    /** Yang sedang “window”-nya berjalan sekarang (berdasar waktu) */
    public function scopeRunningNow($q)
    {
        $now = now();
        return $q->whereNotNull('mulai_giliran')
                 ->whereNotNull('selesai_giliran')
                 ->where('mulai_giliran', '<=', $now)
                 ->where('selesai_giliran', '>=', $now);
    }

    /** Menunggu (belum mulai) */
    public function scopeQueued($q)
    {
        return $q->where(function ($qq) {
            $qq->whereNull('mulai_giliran')
               ->orWhere('mulai_giliran', '>', now());
        });
    }

    /** Selesai (window sudah lewat) */
    public function scopeFinished($q)
    {
        return $q->whereNotNull('selesai_giliran')
                 ->where('selesai_giliran', '<', now());
    }

    // ==== HELPERS ====

    /**
     * Hitung status giliran “on-the-fly” tanpa mengubah kolom status_giliran di DB.
     * Mengembalikan: 'Berjalan' | 'Menunggu' | 'Selesai'
     */
    public function computedTurnStatus(?Carbon $ref = null): string
    {
        $ref ??= now();

        if ($this->mulai_giliran && $this->selesai_giliran) {
            if ($ref->between($this->mulai_giliran, $this->selesai_giliran)) {
                return 'Berjalan';
            }
            if ($ref->lessThan($this->mulai_giliran)) {
                return 'Menunggu';
            }
            return 'Selesai';
        }

        // fallback ke kolom status_giliran jika waktu belum diset
        return $this->status_giliran ?: 'Menunggu';
    }

    /**
     * Convenience accessor: $invite->status_giliran_calc
     */
    public function getStatusGiliranCalcAttribute(): string
    {
        return $this->computedTurnStatus();
    }
}
