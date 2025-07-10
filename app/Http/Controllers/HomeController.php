<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function home()
    {
        $propertyTypes = ['Rumah', 'Gudang', 'Apartemen', 'Tanah', 'Pabrik', 'Hotel dan Villa', 'Ruko', 'Sewa'];

        // Ambil count dari DB per tipe
        $propertyCounts = DB::table('property')
            ->selectRaw("LOWER(tipe) as tipe, SUM(CASE WHEN status = 'Tersedia' THEN 1 ELSE 0 END) as total")
            ->groupBy('tipe')
            ->pluck('total', 'tipe') // contoh: ['rumah' => 5, 'villa' => 0, ...]
            ->toArray();

        // Map semua tipe supaya tetap muncul walau total = 0
        $properties = collect($propertyTypes)->map(function ($type) use ($propertyCounts) {
            $lowerType = strtolower($type);
            return (object)[
                'tipe' => $type,
                'total' => $propertyCounts[$lowerType] ?? 0
            ];
        });

        // Ambil 6 properti random dengan status "Tersedia"
        $hotListings = Property::where('status', 'Tersedia')
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Ambil testimonial terakhir dengan rating
        $testimonials = DB::table('transaction')
            ->join('account', 'transaction.id_klien', '=', 'account.id_account')
            ->select('account.nama', 'transaction.rating', 'transaction.comment')
            ->whereNotNull('transaction.rating')
            ->orderByDesc('transaction.tanggal_transaksi')
            ->limit(10)
            ->get();

        // Kirim semua ke view
        return view('index', compact('properties', 'hotListings', 'testimonials'));
    }

}
