<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventInvite extends Model
{
    protected $fillable = [
        'id_invite',
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

    protected $table = 'event_invites';
    protected $primaryKey = 'id_invite';
    public $timestamps = false;

    // Invite ke event tertentu
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    // Invite untuk account tertentu
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_account', 'id_account');
    }
}
