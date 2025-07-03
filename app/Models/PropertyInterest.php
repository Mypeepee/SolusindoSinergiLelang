<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyInterest extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'property_interests';
    protected $primaryKey = 'id_interest';

    protected $fillable = [
        'id_listing',
        'id_account',
        'ktp',
        'npwp',
        'buku_tabungan',
    ];

    // Fungsi untuk mendapatkan semua data property interest
    public static function getAllPropertyInterests()
    {
        return DB::table('property_interests')->get();
    }

    // Fungsi untuk menambahkan data interest baru
    public static function addPropertyInterest($data)
    {
        return DB::table('property_interests')->insert($data);
    }

    // PropertyInterest dan Account (Many to One sebagai Klien)
    public function client(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_klien', 'id_account');
    }

    // PropertyInterest dan Property (Many to One)
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'id_listing', 'id_listing');
    }

}
