<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformasiKlien extends Model
{
    protected $fillable = [
        'id_account',
        'alamat_ktp',
        'alamat_domisili',
        'pekerjaan',
        'penghasilan',
        'npwp',
        'ktp',
        'buku_tabungan',
        'status_perkawinan',
        'jumlah_tanggungan',
        'tanggal_dibuat',
        'tanggal_diupdate',
    ];

    protected $table = 'informasi_klien'; // 👈 force pakai tabel singular

    // InformasiKlien dan Account (One to One)
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_account');
    }
}
