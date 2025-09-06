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
    // >>> Hilangkan JOIN, pakai subselect agar bebas ambiguitas
    $query = Property::query()
        ->select('property.*')
        ->addSelect([
            'agent_nama' => \DB::table('agent')
                ->select('nama')
                ->whereColumn('agent.id_agent', 'property.id_agent')
                ->limit(1),
            'agent_picture' => \DB::table('agent')
                ->select('picture')
                ->whereColumn('agent.id_agent', 'property.id_agent')
                ->limit(1),
        ]);

    $selectedTags = [];

    // Hanya status Tersedia
    $query->where('property.status', 'Tersedia');

    // ============== Keyword dari search bar (q) ==============
    $keyword = trim((string) $request->input('q', ''));
    if ($keyword !== '') {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        if (preg_match('/^\d+$/', $keyword)) {
            // Semua digit -> cari id_listing (exact)
            $query->where('property.id_listing', (int) $keyword);
        } else {
            // String -> cari di lokasi (kota/lokasi/provinsi/kecamatan)
            $kw = '%' . $keyword . '%';
            $query->where(function ($q) use ($kw, $likeOp) {
                $q->where('property.kota', $likeOp, $kw)
                  ->orWhere('property.lokasi', $likeOp, $kw)
                  ->orWhere('property.provinsi', $likeOp, $kw)
                  ->orWhere('property.kecamatan', $likeOp, $kw);
            });
        }

        $selectedTags[] = $keyword;
    }

    // ============== Harga ==============
    if ($request->filled('min_price')) {
        $query->where('property.harga', '>=', str_replace('.', '', $request->min_price));
    }
    if ($request->filled('max_price')) {
        $query->where('property.harga', '<=', str_replace('.', '', $request->max_price));
    }

    // ============== Luas Tanah (Min–Max) ==============
    if ($request->filled('min_land_size')) {
        $query->where('property.luas', '>=', str_replace('.', '', $request->min_land_size));
    }
    if ($request->filled('max_land_size')) {
        $query->where('property.luas', '<=', str_replace('.', '', $request->max_land_size));
    }

    // ============== Tipe properti ==============
    if ($request->filled('property_type')) {
        $query->where('property.tipe', $request->property_type);
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
        $selectedTags = array_merge($selectedTags, $selectedTagsFromCities);
    }

    // ============== Filter lokasi berdasarkan tag/provinsi ==============
    if (!empty($districts)) {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $query->where(function ($q) use ($districts, $likeOp) {
            foreach ($districts as $d) {
                $q->orWhere(function ($sub) use ($d, $likeOp) {
                    $sub->where('property.kota', $likeOp, '%' . $d['city'] . '%')
                        ->where('property.kecamatan', $likeOp, '%' . $d['district'] . '%');
                });
            }
        });
    } elseif (!empty($cities)) {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $query->where(function ($q) use ($cities, $likeOp) {
            foreach ($cities as $city) {
                $q->orWhere('property.kota', $likeOp, '%' . $city . '%');
            }
        });
    } elseif ($request->filled('province')) {
        $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $prov = '%' . $request->province . '%';
        $query->where(function ($q) use ($prov, $likeOp) {
            $q->where('property.provinsi', $likeOp, $prov)
              ->orWhere('property.lokasi',   $likeOp, $prov);
        });
    }

    // ============== Sorting ==============
    if ($request->sort === 'harga_asc') {
        $query->orderBy('property.harga', 'asc');
    } elseif ($request->sort === 'harga_desc') {
        $query->orderBy('property.harga', 'desc');
    } else {
        $query->orderBy('property.tanggal_dibuat', 'desc');
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
