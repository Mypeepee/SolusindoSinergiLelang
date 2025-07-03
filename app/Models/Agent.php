<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'agent';
    protected $primaryKey = 'id_agent';
    public $incrementing = false;
    protected $fillable = [
        'id_account',
        'id_agent',
        'nama',
        'nomor_telepon',
        'email',
        'instagram',
        'facebook',
        'id_listing',
        'jadwal',
        'picture',
        'kota',
        'status',
        'rating',
        'jumlah_penjualan',
        'lokasi_kerja',
        'tanggal_dibuat',
        'tanggal_diupdate',
        'gambar_ktp',
        'gambar_npwp',
    ];
    
    // Agent dan Account (One to One)
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_account', 'id_account');
    }

    // Agent dan Property (One to Many)
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'id_agent');
    }

    // Agent dan Transaction (One to Many)
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id_agent', 'id_agent');
    }


}
