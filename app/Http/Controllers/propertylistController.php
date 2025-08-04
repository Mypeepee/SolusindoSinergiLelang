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

    // Filter harga minimum
    if ($request->filled('min_price')) {
        $minPrice = str_replace('.', '', $request->min_price);
        $query->where('harga', '>=', $minPrice);
    }

    // Filter harga maksimum
    if ($request->filled('max_price')) {
        $maxPrice = str_replace('.', '', $request->max_price);
        $query->where('harga', '<=', $maxPrice);
    }

    // Filter tipe properti
    if ($request->filled('property_type')) {
        $query->where('tipe', $request->property_type);
    }

    // Filter provinsi
    if ($request->filled('province')) {
        $query->where('provinsi', $request->province);
    }

    // Filter kota & kecamatan
    if ($request->filled('selected_city_values')) {
        $selectedTags = explode(',', $request->selected_city_values);

        $cities = [];
        $districts = [];

        foreach ($selectedTags as $tag) {
            if (strpos($tag, ' - ') !== false) {
                [$city, $district] = explode(' - ', $tag);
                $districts[] = ['city' => trim($city), 'district' => trim($district)];
            } else {
                $cities[] = trim($tag);
            }
        }

        $query->where(function ($q) use ($cities, $districts) {
            // Jika ada kota
            if (!empty($cities)) {
                $q->orWhereIn('kota', $cities);
            }

            // Jika ada kecamatan
            if (!empty($districts)) {
                foreach ($districts as $d) {
                    $q->orWhere(function ($sub) use ($d) {
                        $sub->where('kota', $d['city'])
                            ->where('kecamatan', $d['district']);
                    });
                }
            }
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
