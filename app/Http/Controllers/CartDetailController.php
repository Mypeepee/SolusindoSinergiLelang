<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\DB;


class CartDetailController extends Controller
{
    //
    public function show($id)
{
    // Ambil data properti berdasarkan ID
    $property = Property::findOrFail($id);

    // Ambil data ketertarikan user terhadap properti ini (jika ada)
    $interest = DB::table('property_interests')
        ->where('id_listing', $id)
        ->where('id_klien', session('id_account')) // bisa diganti dengan auth()->user()->id_account jika pakai Auth
        ->first();

    // Ambil status dan catatan jika ada
    $status = optional($interest)->status ?? 'tunggu_verifikasi';
    $catatan = optional($interest)->catatan ?? '';

    // Ambil rating dan deskripsi dari company_earnings berdasarkan id_listing
    $transactionReview = DB::table('transaction')
    ->where('id_listing', $id)
    ->select('rating', 'comment')
    ->first();


    // Kirim semua data ke view
    return view('cartdetail', compact('property', 'status', 'catatan', 'interest', 'transactionReview'));
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
    DB::table('company_earnings')
        ->where('id_listing', $validated['id_listing'])
        ->update([
            'rating' => $validated['rating'],
            'comment' => $validated['deskripsi'],
        ]);

    return response()->json(['success' => true]);
}

}
