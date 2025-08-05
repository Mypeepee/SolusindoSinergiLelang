<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

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

        $idaccount = session('id_account') ?? Cookie::get('id_account');
        $hotListings = collect(); // default kosong
        $hotListingNote = null;

        if (!$idaccount) {
            // ❌ Tidak login
            $hotListings = Property::where('status', 'Tersedia')
                ->whereNotNull('gambar')
                ->where('gambar', '!=', '')
                ->inRandomOrder()
                ->take(6)
                ->get();
            $hotListingNote = "Silakan login untuk melihat listing properti yang sesuai dengan lokasi Anda.";
        } else {
            $account = Account::find($idaccount);

            $kecamatan = $account->kecamatan ?? null;
            $kota = $account->kota ?? null;
            $provinsi = $account->provinsi ?? null;

            if (!$kecamatan && !$kota && !$provinsi) {
                // ⚠️ Semua lokasi kosong
                $hotListings = Property::where('status', 'Tersedia')
                    ->whereNotNull('gambar')
                    ->where('gambar', '!=', '')
                    ->inRandomOrder()
                    ->take(6)
                    ->get();
                $hotListingNote = "Silakan lengkapi data lokasi Anda (provinsi, kota, kecamatan) di profil untuk melihat listing properti yang relevan.";
            } else {
                $query = Property::where('status', 'Tersedia')
                    ->whereNotNull('gambar')
                    ->where('gambar', '!=', '');

                $results = collect();

                // 1. Kecamatan + kota
                if ($kecamatan && $kota) {
                    $results = (clone $query)
                        ->where('kecamatan', $kecamatan)
                        ->where('kota', $kota)
                        ->take(6)
                        ->get();
                }

                // 2. Kota (hindari duplikat kecamatan jika sudah diambil)
                if ($results->count() < 6 && $kota) {
                    $remaining = 6 - $results->count();
                    $more = (clone $query)
                        ->where('kota', $kota)
                        ->when($kecamatan, fn($q) => $q->where('kecamatan', '!=', $kecamatan))
                        ->take($remaining)
                        ->get();
                    $results = $results->concat($more);
                }

                // 3. Provinsi (hindari duplikat kota)
                if ($results->count() < 6 && $provinsi) {
                    $remaining = 6 - $results->count();
                    $more = (clone $query)
                        ->where('provinsi', $provinsi)
                        ->when($kota, fn($q) => $q->where('kota', '!=', $kota))
                        ->take($remaining)
                        ->get();
                    $results = $results->concat($more);
                }

                // 4. Fallback: Random
                if ($results->count() < 6) {
                    $remaining = 6 - $results->count();
                    $more = Property::where('status', 'Tersedia')
                        ->whereNotNull('gambar')
                        ->where('gambar', '!=', '')
                        ->take($remaining)
                        ->get();
                    $results = $results->concat($more);
                }

                $hotListings = $results;

            }
        }


        // Ambil testimonial terakhir dengan rating
        $testimonials = DB::table('transaction')
            ->join('account', 'transaction.id_klien', '=', 'account.id_account')
            ->select('account.nama', 'transaction.rating', 'transaction.comment')
            ->whereNotNull('transaction.rating')
            ->orderByDesc('transaction.tanggal_transaksi')
            ->limit(10)
            ->get();

        // Kirim semua ke view
        return view('index', compact('properties', 'hotListings', 'hotListingNote', 'testimonials'));
    }

}
