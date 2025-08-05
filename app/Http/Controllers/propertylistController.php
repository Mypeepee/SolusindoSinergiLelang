<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;

class propertylistController extends Controller
{
    public function PropertyList(Request $request)
    {
        $query = \App\Models\Property::query();

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
        $query = Property::query();
        $selectedTags = [];

        // Harga
        if ($request->filled('min_price')) {
            $query->where('harga', '>=', str_replace('.', '', $request->min_price));
        }
        if ($request->filled('max_price')) {
            $query->where('harga', '<=', str_replace('.', '', $request->max_price));
        }

        // Tipe properti
        if ($request->filled('property_type')) {
            $query->where('tipe', $request->property_type);
        }

        // Ambil tag kota/kecamatan
        $cities = [];
        $districts = [];
        if ($request->filled('selected_city_values')) {
            $selectedTags = explode(',', $request->selected_city_values);
            foreach ($selectedTags as $tag) {
                if (strpos($tag, ' - ') !== false) {
                    [$city, $district] = explode(' - ', $tag);
                    $districts[] = ['city' => trim($city), 'district' => trim($district)];
                } else {
                    $cities[] = trim($tag);
                }
            }
        }

        // Filter lokasi:
        if (!empty($districts)) {
            // Kalau ada kecamatan → fokus kota+kecamatan
            $query->where(function ($q) use ($districts) {
                foreach ($districts as $d) {
                    $q->orWhere(function ($sub) use ($d) {
                        $sub->where('kota', 'ILIKE', '%' . $d['city'] . '%')
                            ->where('kecamatan', 'ILIKE', '%' . $d['district'] . '%');
                    });
                }
            });
        } elseif (!empty($cities)) {
            // Kalau cuma kota
            $query->where(function ($q) use ($cities) {
                foreach ($cities as $city) {
                    $q->orWhere('kota', 'ILIKE', '%' . $city . '%');
                }
            });
        } elseif ($request->filled('province')) {
            // Kalau cuma provinsi
            $query->where(function ($q) use ($request) {
                $q->where('provinsi', 'ILIKE', '%' . $request->province . '%')
                  ->orWhere('lokasi', 'ILIKE', '%' . $request->province . '%');
            });
        }

        // Sorting
        if ($request->sort === 'harga_asc') {
            $query->orderBy('harga', 'asc');
        } elseif ($request->sort === 'harga_desc') {
            $query->orderBy('harga', 'desc');
        } else {
            $query->orderBy('tanggal_dibuat', 'desc');
        }

        $properties = $query->paginate(18);

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
