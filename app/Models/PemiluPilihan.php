<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemiluPilihan extends Model
{
    protected $table = 'pemilu_pilihan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_event',
        'id_agent',
        'id_listing',
    ];

    /** Relasi ke Event */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    /** Relasi ke Agent */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    /** Relasi ke Property */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'id_listing', 'id_listing');
    }
}
