<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Account extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id_account';
    public $incrementing = false;
    protected $table = 'account';

    // Sesuaikan $fillable agar match dengan kolom baru
    protected $fillable = [
        'nama',
        'kota',
        'kecamatan',
        'tanggal_lahir',
        'nomor_telepon',
        'email',
        'username',
        'password'
    ];

    // Method untuk ambil nomor telepon dari session
    public static function getPhoneNumberFromSession()
    {
        $id_account = Session::get('id_account');
        return self::where('id_account', $id_account)->value('nomor_telepon');
    }

    // Relasi: satu akun bisa punya banyak properti
    public function properties()
    {
        return $this->hasMany(Property::class, 'id_agent', 'id_account');
    }
}
