<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    // Relasi ke Property
    public function property()
    {
        return $this->belongsTo(Property::class, 'id_listing', 'id_listing');
    }

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
}
