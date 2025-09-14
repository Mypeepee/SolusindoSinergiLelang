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


public function showproperty(Request $request,
                             $property_type = 'semua',   // default jadi 'semua'
                             $province = 'semua',
                             $city = 'semua',
                             $district = 'semua',
                             $price_range = 'harga-max-0',
                             $land_size = 'luas-tanah-max-0',
                             $page = 1)
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

    // ============== Tipe properti (PERBAIKAN: default semua) ==============
    // Ambil dari query (?property_type=...) atau dari path param $property_type
    $resolvedType = strtolower((string) $request->input('property_type', $property_type));

    // Normalisasi ejaan/alias
    $aliases = [
        'apartement' => 'apartemen',
        'apartment'  => 'apartemen',
    ];
    if (isset($aliases[$resolvedType])) {
        $resolvedType = $aliases[$resolvedType];
    }

    // Jika 'semua' / 'all' / kosong => JANGAN filter tipe
    if ($resolvedType === '' || in_array($resolvedType, ['semua','all','properti','any'], true)) {
        $resolvedType = null;
    }

    // Jika ada tipe valid => filter case-insensitive
    if (!is_null($resolvedType)) {
        $query->whereRaw('LOWER(property.tipe) = ?', [$resolvedType]);
    }

    // Simpan untuk dikirim ke view
    $property_type = $resolvedType ?? 'semua';

    // ============== Ambil tag kota/kecamatan ==============
    $cities = [];
    $districts = [];
    if ($request->filled('selected_city_values')) {
        $selectedTagsFromCities = explode(',', $request->selected_city_values);
        foreach ($selectedTagsFromCities as $tag) {
            if (strpos($tag, ' - ') !== false) {
                [$cityName, $districtName] = explode(' - ', $tag);
                $districts[] = ['city' => trim($cityName), 'district' => trim($districtName)];
            } else {
                $cities[] = trim($tag);
            }
        }
        $selectedTags = array_merge($selectedTags, $selectedTagsFromCities);
    }

    // ============== Filter lokasi berdasarkan tag/provinsi ==============
    $urlFilters = [];
    $likeOp = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
    if (!empty($districts)) {
        $query->where(function ($q) use ($districts, $likeOp) {
            foreach ($districts as $d) {
                $q->orWhere(function ($sub) use ($d, $likeOp) {
                    $sub->where('property.kota', $likeOp, '%' . $d['city'] . '%')
                        ->where('property.kecamatan', $likeOp, '%' . $d['district'] . '%');
                });
            }
        });
        $urlFilters[] = strtolower(str_replace(' ', '-', implode('/', array_column($districts, 'city'))));
    } elseif (!empty($cities)) {
        $query->where(function ($q) use ($cities, $likeOp) {
            foreach ($cities as $cityName) {
                $q->orWhere('property.kota', $likeOp, '%' . $cityName . '%');
            }
        });
        $urlFilters[] = strtolower(str_replace(' ', '-', implode('/', $cities)));
    } elseif ($request->filled('province')) {
        $prov = '%' . $request->province . '%';
        $query->where(function ($q) use ($prov, $likeOp) {
            $q->where('property.provinsi', $likeOp, $prov)
              ->orWhere('property.lokasi',   $likeOp, $prov);
        });
        $urlFilters[] = strtolower(str_replace(' ', '-', $request->province));
    }

    // Ambil hasil properti
    $properties = $query->paginate(18)->appends($request->query());

    // ========================== Sorting ==========================
    // (biarkan sesuai logic awal)
    if ($request->sort === 'harga_asc') {
        $properties->setCollection($properties->getCollection()->sortBy('harga')->values());
    } elseif ($request->sort === 'harga_desc') {
        $properties->setCollection($properties->getCollection()->sortByDesc('harga')->values());
    } elseif ($request->sort === 'tanggal_terdekat') {
        $properties->setCollection($properties->getCollection()->sortBy('batas_akhir_penawaran')->values());
    } elseif ($request->sort === 'tanggal_terjauh') {
        $properties->setCollection($properties->getCollection()->sortByDesc('batas_akhir_penawaran')->values());
    } elseif ($request->sort === 'tanggal_sekarang') {
        $filtered = (clone $query)->where('property.batas_akhir_penawaran', '>=', now())->get()
                    ->sortBy(function ($p) {
                        return \Carbon\Carbon::parse($p->batas_akhir_penawaran)->timestamp;
                    })->values();

        $properties = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, 18),
            $filtered->count(),
            18,
            $page,
            ['path' => url()->current(), 'pageName' => 'page']
        );
        $properties->appends($request->query());
    } elseif ($request->sort === 'semua') {
        $properties->setCollection($properties->getCollection()->sortBy('batas_akhir_penawaran')->values());
    }

    // Generate URL based on filters
    $baseUrl = $property_type !== 'semua'
        ? 'https://solusindolelang.com/jual-' . strtolower($property_type)
        : 'https://solusindolelang.com/jual-properti';

    $url = rtrim($baseUrl . '/' . implode('/', array_filter($urlFilters)), '/');

    // Append pagination to URL
    $urlWithPagination = $url . '/page/' . $properties->currentPage();

    // Ambil salah satu properti untuk meta/structured data
    $property = $query->first();

    // Pass data ke view
    return view('property-list', compact(
        'properties',
        'selectedTags',
        'urlWithPagination',
        'property',
        'property_type',
        'province',
        'city',
        'price_range'
    ));
}





    public function showPropertyDetail($id)
    {
    // Fetch the property based on ID
    $property = Property::findOrFail($id);

    // Pass the property data to the view
    return view('property-detail', compact('property'));
    }

}
