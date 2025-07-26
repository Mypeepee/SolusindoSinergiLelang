<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;


class CartDetailController extends Controller
{
    public function show($id)
    {
        // Ambil data properti berdasarkan ID
        $property = Property::findOrFail($id);
        $idAccount = session('id_account') ?? Cookie::get('id_account');


        // Cek apakah ada transaction untuk user & listing ini
        $transaction = DB::table('transaction')
            ->where('id_listing', $id)
            ->where('id_klien', $idAccount)
            ->first();

        if ($transaction) {
            // Jika ada transaksi, ambil data terakhir dari transaction_details
            $lastDetail = DB::table('transaction_details')
                ->where('id_transaction', $transaction->id_transaction)
                ->orderByDesc('tanggal_dibuat')
                ->first();

            $status = optional($lastDetail)->status_transaksi ?? 'tunggu_verifikasi';
            $catatan = optional($lastDetail)->catatan ?? '';
        } else {
            // Jika tidak ada, fallback ke property_interests
            $interest = DB::table('property_interests')
                ->where('id_listing', $id)
                ->where('id_klien', $idAccount)
                ->first();

            $status = optional($interest)->status ?? 'tunggu_verifikasi';
            $catatan = optional($interest)->catatan ?? '';
        }

        // Ambil rating dan deskripsi dari company_earnings berdasarkan id_listing
        $transactionReview = DB::table('transaction')
            ->where('id_listing', $id)
            ->select('rating', 'comment')
            ->first();

        // Kirim semua data ke view
        return view('cartdetail', compact('property', 'status', 'catatan', 'transactionReview'));
    }

    public function storeRating(Request $request)
    {
        $validated = $request->validate([
            'id_account' => 'required',
            'id_listing' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'deskripsi' => 'nullable|string',
        ]);

        // Update langsung ke tabel company_earnings atau listing
        DB::table('transaction')
            ->where('id_listing', $validated['id_listing'])
            ->update([
                'rating' => $validated['rating'],
                'comment' => $validated['deskripsi'],
                'tanggal_diupdate' => now(),
            ]);

        return response()->json(['success' => true]);
    }

}
