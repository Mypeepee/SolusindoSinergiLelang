<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;

class propertylistController extends Controller
{
    private function slugHarga($min, $max) {
        $format = function($val) {
            $val = (int) $val;
            if ($val >= 1000000000) {
                return ($val / 1000000000) . '-milyar';
            } elseif ($val >= 1000000) {
                return ($val / 1000000) . '-juta';
            }
            return $val;
        };

        if ($min && $max) return 'antara-' . $format($min) . '-' . $format($max);
        if ($min) return 'di-atas-' . $format($min);
        if ($max) return 'di-bawah-' . $format($max);

        return null;
    }

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
                             $property_type = 'property',
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

    // Sort terbaru (id terbesar dulu)
    $query->orderBy('property.id_listing', 'desc');

    // ============== Keyword dari search bar (q) ==============
    $keyword = trim((string) $request->input('q', ''));
    if ($keyword !== '') {
        if (preg_match('/^\d+$/', $keyword)) {
            // ✅ Kalau keyword angka → langsung cari id_listing
            $query->where('property.id_listing', (int) $keyword);
        } else {
            // ✅ Kalau keyword teks → cari di lokasi, provinsi, kota, kecamatan
            $kw = '%' . strtolower(trim($keyword)) . '%';

            $query->where(function ($q) use ($kw) {
                $q->orWhereRaw('LOWER(COALESCE(TRIM(property.lokasi), \'\')) LIKE ?', [$kw])
                  ->orWhereRaw('LOWER(TRIM(property.provinsi)) LIKE ?', [$kw])
                  ->orWhereRaw('LOWER(TRIM(property.kota)) LIKE ?', [$kw])
                  ->orWhereRaw('LOWER(TRIM(property.kecamatan)) LIKE ?', [$kw]);
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
    $resolvedType = str_replace('-', ' ', strtolower($request->input('property_type', $property_type)));
    $aliases = [
        'apartement' => 'apartemen',
        'apartment'  => 'apartemen',
    ];
    if (isset($aliases[$resolvedType])) {
        $resolvedType = $aliases[$resolvedType];
    }
    if ($resolvedType === '' || in_array($resolvedType, ['all','any'], true)) {
        $resolvedType = null;
    }
    if (!is_null($resolvedType) && $resolvedType !== 'property') {
        $query->whereRaw('LOWER(property.tipe) = ?', [$resolvedType]);
    }
    $property_type = $resolvedType ?? 'property'; // ✅ fallback default "property"

// ============== Ambil tag kota/kecamatan ==============
$cities = [];
$districts = [];
if ($request->filled('selected_city_values')) {
    $selectedTagsFromCities = explode(',', $request->selected_city_values);
    foreach ($selectedTagsFromCities as $tag) {
        if (strpos($tag, ' - ') !== false) {
            [$districtName, $cityName] = explode(' - ', $tag);
            $districts[] = ['city' => trim($cityName), 'district' => trim($districtName)];
        } else {
            // ✅ Jangan masukin ke $cities kalau sama dengan province
            if (strtolower(trim($tag)) !== strtolower(trim($request->province))) {
                $cities[] = trim($tag);
            }
        }
    }
    $selectedTags = array_merge($selectedTags, $selectedTagsFromCities);
}

// ============== Filter lokasi ==============
$urlFilters = [];

if (!empty($districts)) {
    // filter kecamatan + kota (EXACT match)
    $query->where(function ($q) use ($districts) {
        foreach ($districts as $d) {
            $q->orWhere(function ($sub) use ($d) {
                $sub->whereRaw('LOWER(TRIM(property.kota)) = ?', [strtolower(trim($d['city']))])
                    ->whereRaw('LOWER(TRIM(property.kecamatan)) = ?', [strtolower(trim($d['district']))]);
            });
        }
    });

    foreach ($districts as $d) {
        // ✅ gabung kecamatan + kota → rapikan, tanpa spasi aneh
        $urlFilters[] = \Str::slug(trim($d['district']).' '.trim($d['city']));
    }

} elseif (!empty($cities)) {
    // filter kota (EXACT match)
    $query->where(function ($q) use ($cities) {
        foreach ($cities as $cityName) {
            $q->orWhereRaw('LOWER(TRIM(property.kota)) = ?', [strtolower(trim($cityName))]);
        }
    });

    foreach ($cities as $c) {
        $urlFilters[] = \Str::slug(trim($c));
    }

} elseif (
    $request->filled('province')
    && strtolower(trim($request->province)) !== 'di-indonesia'
    && strtolower(trim($request->province)) !== 'semua'
) {
    // filter provinsi (EXACT match)
    $provName = strtolower(trim($request->province));
    $query->whereRaw('LOWER(TRIM(property.provinsi)) = ?', [$provName]);

    $urlFilters[] = \Str::slug(trim($request->province));
}

// fallback SEO: kalau semua kosong, taruh "di-indonesia"
if (empty($urlFilters)) {
    $urlFilters[] = 'di-indonesia';
}



    // Ambil hasil properti
    $properties = $query->paginate(18)->appends($request->query());

    // ========================== Sorting ==========================
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

    // ========================== URL SEO ==========================
    $baseUrl = 'https://solusindolelang.com/jual/' . \Str::slug($property_type);

    // Tambah lokasi
    $url = rtrim($baseUrl . '/' . implode('/', array_filter($urlFilters)), '/');

    // Tambah harga
    $priceSlug = $this->slugHarga($request->min_price, $request->max_price);
    if ($priceSlug) {
        $url .= '/' . $priceSlug;
    }

    // Pagination
    $urlWithPagination = $url . '/page/' . $properties->currentPage();

    // Ambil salah satu properti untuk meta/structured data
    $property = $query->first();

    return view('property-list', compact(
        'properties',
        'selectedTags',
        'urlWithPagination',
        'property',
        'property_type',
        'province',
        'city',
        'district',
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
