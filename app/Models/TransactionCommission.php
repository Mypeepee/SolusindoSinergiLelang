<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCommission extends Model
{
    protected $table = 'transaction_commissions';

    protected $fillable = [
        'id_transaction',
        'role',
        'id_agent',
        'pendapatan',
    ];

    public $timestamps = false; // tabel ini nggak punya created_at/updated_at
}
