<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'property';
    protected $primaryKey = 'id_listing';
    protected $fillable = [
        'tipe',
        'judul',
        'deskripsi',
        'kamar_tidur',
        'kamar_mandi',
        'harga',
        'lokasi',
        'provinsi',
        'kota',
        'kelurahan',
        'sertifikat',
        'orientation',
        'status',
        'gambar',
        'luas_tanah',
        'luas_bangunan',
        'payment',
        'lantai',
        'id_agent',
    ];


    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_account');
    }

    public function getAgentPhone()
{
    return DB::table('account')
        ->where('id_account', $this->id_agent) // Cocokkan id_agent dengan id_account
        ->value('nomor_telepon'); // Ambil nomor telepon
}

//     public function agents()
// {
//     return $this->belongsToMany(Agent::class, 'agent_listing', 'id_listing', 'id_account');
// }
}
