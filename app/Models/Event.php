<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'id_event',
        'title',
        'description',
        'start',
        'end',
        'all_day',
        'location',
        'created_by',
        'tanggal_dibuat',
        'tanggal_diupdate',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end'   => 'datetime',
    ];
    
    protected $table = 'events';
    protected $primaryKey = 'id_event';
    public $timestamps = false;

    // Event dibuat oleh 1 account (creator)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'created_by', 'id_account');
    }

    // Event punya banyak undangan
    public function invites(): HasMany
    {
        return $this->hasMany(EventInvite::class, 'id_event', 'id_event');
    }
}
