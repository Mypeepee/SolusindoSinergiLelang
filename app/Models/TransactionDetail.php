<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Database\Transaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    protected $fillable = [
        'id_account',
        'id_transaction',
        'status_transaksi',
        'catatan',
        'tanggal_dibuat',
        'tanggal_diupdate',
    ];

    // TransactionDetail dan Account (Many to One)
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'id_account', 'id_account');
    }

    // TransactionDetail dan Transaction (Many to One)
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'id_transaction', 'id_transaction');
    }

}
