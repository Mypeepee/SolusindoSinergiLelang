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
        'id_account', 'nama', 'email', 'tanggal_lahir',
        'nomor_telepon', 'username', 'password',
        'provinsi', 'kota', 'kecamatan', 'kode_referal'
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            // Ambil id_account terakhir
            $lastAccount = Account::orderBy('id_account', 'desc')->first();

            if ($lastAccount && preg_match('/^AC(\d+)$/i', $lastAccount->id_account, $matches)) {
                $lastNumber = (int)$matches[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1; // Kalau belum ada data
            }

            // Format id_account: AC001, AC002, dst
            $account->id_account = 'AC' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}
