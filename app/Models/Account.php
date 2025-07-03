<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Database\Transaction;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    // Account ke InformasiKlien (One to One)
    public function informasiKlien(): HasOne
    {
        return $this->hasOne(InformasiKlien::class, 'id_account');
    }

    // Account ke Agent (One to One, jika ada)
    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class, 'id_account');
    }

    // Account dan PropertyInterest (One to Many)
    public function propertyInterests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class, 'id_klien', 'id_account');
    }

    // Account (Klien) dan Transaction (One to Many)
    public function transactionsAsClient(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id_klien', 'id_account');
    }

    // Account dan TransactionDetail (One to Many)
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'id_account', 'id_account');
    }


}
