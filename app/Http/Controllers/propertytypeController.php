<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;

class propertytypeController extends Controller
{
    public function PropertyType()
    {
        return view("property-type");
    }

    public function tipeproperty()
    {
        // Ambil data properti dan hitung jumlah tiap tipe
        $properties = Property::where('status', 'available')
                            ->select('tipe', \DB::raw('count(*) as total'))
                            ->groupBy('tipe')
                            ->get();

        return view('property-type', compact('properties'));
    }

    public function showPropertyTypeIndex()
    {
        $allTypes = [
            'rumah', 'gudang', 'apartemen', 'tanah',
            'pabrik', 'villa', 'ruko', 'sewa'
        ];

        // Ambil total property untuk masing-masing tipe yang tersedia
        $propertyCounts = \DB::table('property')
            ->where('status', 'available')
            ->select('tipe', \DB::raw('count(*) as total'))
            ->groupBy('tipe')
            ->pluck('total', 'tipe') // hasilnya: ['Rumah' => 4, 'Ruko' => 2, ...]
            ->toArray();

        // Gabungkan semua tipe dengan count default 0 jika belum ada di database
        $properties = collect($allTypes)->map(function ($type) use ($propertyCounts) {
            return (object)[
                'tipe' => $type,
                'total' => $propertyCounts[$type] ?? 0
            ];
        });
        return view('property-type', compact('properties'));
    }

}
