<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'property';
    protected $primaryKey = 'id_listing';
    protected $fillable = [
        'id_agent',
        'vendor',
        'judul',
        'deskripsi',
        'tipe',
        'harga',
        'lokasi',
        'luas',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'sertifikat',
        'status',
        'gambar',
        'payment',
        'uang_jaminan',
        'batas_akhir_jaminan',
        'batas_akhir_penawaran',
        'tanggal_buyer_meeting',
    ];


    public function getAgentPhone()
    {
        return DB::table('account')
            ->where('id_account', $this->id_agent) // Cocokkan id_agent dengan id_account
            ->value('nomor_telepon'); // Ambil nomor telepon
    }

    // Property dan Agent (Many to One)
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    // Property dan PropertyInterest (One to Many)
    public function propertyInterests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class, 'id_listing', 'id_listing');
    }

    // Property dan Transaction (One to One)
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'id_listing', 'id_listing');
    }

//     public function agents()
// {
//     return $this->belongsToMany(Agent::class, 'agent_listing', 'id_listing', 'id_account');
// }
}
