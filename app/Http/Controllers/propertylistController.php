<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;

class propertylistController extends Controller
{
    public function PropertyList(Request $request)
{
    $query = \App\Models\Property::query();

    // Menambahkan kondisi status = 'Tersedia'
    $query->where('status', 'Tersedia');

    // Sorting berdasarkan query string
    if ($request->sort === 'harga_asc') {
        $query->orderBy('harga', 'asc');
    } elseif ($request->sort === 'harga_desc') {
        $query->orderBy('harga', 'desc');
    } else {
        $query->latest(); // default (unggulan)
    }

    $properties = $query->paginate(18);

    return view('property-list', compact('properties'));
}


public function showproperty(Request $request)
{
        // JOIN agent agar dapat nama & picture
        $query = Property::query()
        ->leftJoin('agent', 'agent.id_agent', '=', 'property.id_agent')
        ->select('property.*', 'agent.nama as agent_nama', 'agent.picture as agent_picture');

    $selectedTags = [];

    // Menambahkan kondisi status = 'Tersedia'
    $query->where('property.status', 'Tersedia');

    // ============== Keyword dari search bar (q) ==============
    $keyword = trim((string) $request->input('q', ''));
    if ($keyword !== '') {
        // Deteksi driver untuk LIKE case-insensitive (Postgres pakai ILIKE)
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        if (preg_match('/^\d+$/', $keyword)) {
            // Semua digit -> cari id_listing (exact)
            $query->where('id_listing', (int) $keyword);
        } else {
            // String -> cari di lokasi (kota/lokasi/provinsi/kecamatan)
            $kw = '%' . $keyword . '%';
            $query->where(function ($q) use ($kw, $likeOp) {
                $q->where('kota', $likeOp, $kw)
                  ->orWhere('lokasi', $likeOp, $kw)
                  ->orWhere('provinsi', $likeOp, $kw)
                  ->orWhere('kecamatan', $likeOp, $kw);
            });
        }

        // Menambahkan keyword ke selectedTags untuk ditampilkan di alert
        $selectedTags[] = $keyword;
    }

    // ============== Harga ==============
    if ($request->filled('min_price')) {
        $query->where('harga', '>=', str_replace('.', '', $request->min_price));
    }
    if ($request->filled('max_price')) {
        $query->where('harga', '<=', str_replace('.', '', $request->max_price));
    }

    // ============== Luas Tanah (Min–Max) ==============
    if ($request->filled('min_land_size')) {
        $query->where('luas', '>=', str_replace('.', '', $request->min_land_size));
    }
    if ($request->filled('max_land_size')) {
        $query->where('luas', '<=', str_replace('.', '', $request->max_land_size));
    }

    // ============== Tipe properti ==============
    if ($request->filled('property_type')) {
        $query->where('tipe', $request->property_type);
    }

    // ============== Ambil tag kota/kecamatan ==============
    $cities = [];
    $districts = [];
    if ($request->filled('selected_city_values')) {
        $selectedTagsFromCities = explode(',', $request->selected_city_values);
        foreach ($selectedTagsFromCities as $tag) {
            if (strpos($tag, ' - ') !== false) {
                [$city, $district] = explode(' - ', $tag);
                $districts[] = ['city' => trim($city), 'district' => trim($district)];
            } else {
                $cities[] = trim($tag);
            }
        }

        // Menambahkan tag kota/kecamatan ke selectedTags untuk ditampilkan di alert
        $selectedTags = array_merge($selectedTags, $selectedTagsFromCities);
    }

    // ============== Filter lokasi berdasarkan tag/provinsi ==============
    if (!empty($districts)) {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $query->where(function ($q) use ($districts, $likeOp) {
            foreach ($districts as $d) {
                $q->orWhere(function ($sub) use ($d, $likeOp) {
                    $sub->where('kota', $likeOp, '%' . $d['city'] . '%')
                        ->where('kecamatan', $likeOp, '%' . $d['district'] . '%');
                });
            }
        });
    } elseif (!empty($cities)) {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $query->where(function ($q) use ($cities, $likeOp) {
            foreach ($cities as $city) {
                $q->orWhere('kota', $likeOp, '%' . $city . '%');
            }
        });
    } elseif ($request->filled('province')) {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $prov = '%' . $request->province . '%';
        $query->where(function ($q) use ($prov, $likeOp) {
            $q->where('provinsi', $likeOp, $prov)
              ->orWhere('lokasi', $likeOp, $prov);
        });
    }

    // ============== Sorting ==============
    if ($request->sort === 'harga_asc') {
        $query->orderBy('harga', 'asc');
    } elseif ($request->sort === 'harga_desc') {
        $query->orderBy('harga', 'desc');
    } else {
        $query->orderBy('tanggal_dibuat', 'desc');
    }

    // Pagination + bawa semua query string (q, filter, sort, dst.)
    $properties = $query->paginate(18)->appends($request->query());

    return view('property-list', compact('properties', 'selectedTags'));
}




    public function showPropertyDetail($id)
    {
    // Fetch the property based on ID
    $property = Property::findOrFail($id);

    // Pass the property data to the view
    return view('property-detail', compact('property'));
    }

}
