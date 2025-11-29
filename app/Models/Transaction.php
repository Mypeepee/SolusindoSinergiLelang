<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    // Nama tabel
    protected $table = 'transaction';

    // Primary key BUKAN "id"
    protected $primaryKey = 'id_transaction';

    // Primary key string manual (bukan auto increment)
    public $incrementing = false;
    protected $keyType   = 'string';

    // Mapping timestamp Laravel ke kolom custom di DB
    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = 'tanggal_diupdate';

    // Kolom yang boleh di-mass assign
    protected $fillable = [
        'id_transaction',
        'skema_komisi',
        'id_agent',
        'id_klien',
        'id_listing',
        'harga_limit',
        'harga_bidding',
        'selisih',
        'persentase_komisi',
        'basis_pendapatan',
        'status_transaksi',
        'tanggal_transaksi',
        'tanggal_dibuat',
        'tanggal_diupdate',
        'rating',
        'comment',
        'catatan',
        'kenaikan_dari_limit',
    ];

    // ================== RELATIONSHIPS ==================

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_klien', 'id_account');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'id_listing', 'id_listing');
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'id_transaction', 'id_transaction');
    }
}
