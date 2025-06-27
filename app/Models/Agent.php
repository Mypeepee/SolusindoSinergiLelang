<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

public function agent()
{
    return $this->belongsTo(Account::class, 'id_agent', 'id_account');
}
public function account()
    {
        return $this->belongsTo(Account::class, 'id_account', 'id_account');
    }

}
