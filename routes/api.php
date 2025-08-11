<?php

use Illuminate\Support\Facades\Route;
use App\Models\Property;
use Carbon\Carbon;

Route::get('/ping', fn() => ['ok' => true]);

Route::get('/property/{id}', function ($id) {
    $p = Property::where('id_listing', $id)->first();

    if (!$p) {
        return response()->json(['message' => 'Property tidak ditemukan'], 404);
    }

    return response()->json([
        'id_listing' => $p->id_listing,
        'vendor' => $p->vendor ?? 'Vendor tidak diketahui',
        'lokasi' => $p->lokasi,
        'batas_akhir_penawaran' => $p->batas_akhir_penawaran
            ? Carbon::parse($p->batas_akhir_penawaran)->translatedFormat('d F Y')
            : null,
    ]);
});
