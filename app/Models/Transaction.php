<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'id_transaction',
        'id_agent',
        'id_klien',
        'id_listing',
        'harga_deal',
        'harga_bidding',
        'selisih',
        'komisi_agent',
        'status_transaksi',
        'tanggal_transaksi',
        'tanggal_dibuat',
        'tanggal_diupdate',
        'rating',
        'comment',
    ];

    // Transaction dan Agent (Many to One)
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    // Transaction dan Account (Many to One sebagai Klien)
    public function client(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_klien', 'id_account');
    }

    // Transaction dan Property (One to One)
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'id_listing', 'id_listing');
    }

    // Transaction dan TransactionDetail (One to Many)
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'id_transaction', 'id_transaction');
    }

}
