<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class aboutController extends Controller
{
    public function About()
    {
        $testimonials = DB::table('transaction')
                          ->join('account', 'transaction.id_klien', '=', 'account.id_account')
                          ->select('account.nama', 'transaction.rating', 'transaction.comment')
                          ->whereNotNull('transaction.rating')
                          ->orderByDesc('transaction.tanggal_transaksi')
                          ->limit(10)
                          ->get();

        return view("about", compact('testimonials'));
    }
}
