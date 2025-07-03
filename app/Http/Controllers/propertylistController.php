<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;

class propertylistController extends Controller
{
    public function PropertyList()
    {
        return view("property-list");
    }

    public function showproperty(Request $request)
    {
        $query = Property::query();
        $selectedCities = [];

        if ($request->filled('min_price')) {
            $minPrice = str_replace('.', '', $request->min_price);
            $query->where('harga', '>=', $minPrice);
        }

        if ($request->filled('max_price')) {
            $maxPrice = str_replace('.', '', $request->max_price);
            $query->where('harga', '<=', $maxPrice);
        }

        if ($request->filled('property_type')) {
            $query->where('tipe', $request->property_type);
        }

        if ($request->filled('selected_city_values')) {
            $selectedCities = explode(',', $request->selected_city_values);
            $query->whereIn('kota', $selectedCities);
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
