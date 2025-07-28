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
    $selectedCities = [];

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

    // Filter kota
    if ($request->filled('selected_city_values')) {
        $selectedCities = explode(',', $request->selected_city_values);
        $query->whereIn('kota', $selectedCities);
    }

    // ✅ Sorting berdasarkan parameter
    if ($request->sort === 'harga_asc') {
        $query->orderBy('harga', 'asc');
    } elseif ($request->sort === 'harga_desc') {
        $query->orderBy('harga', 'desc');
    } else {
        $query->orderBy('tanggal_dibuat', 'desc'); // pakai kolom dari skema kamu
    }

    $properties = $query->paginate(18);

    return view('property-list', compact('properties', 'selectedCities'));
}




    public function showPropertyDetail($id)
    {
    // Fetch the property based on ID
    $property = Property::findOrFail($id);

    // Pass the property data to the view
    return view('property-detail', compact('property'));
    }

}
