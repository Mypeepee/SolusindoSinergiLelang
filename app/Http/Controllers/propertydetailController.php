<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;

use App\Models\User;

class propertydetailController extends Controller
{
    public function show($id_listing)
    {
        // Ambil data property berdasarkan id_listing
        $property = Property::with(['agent.account'])->where('id_listing', $id_listing)->firstOrFail();

        return view('propertyDetail', compact('property'));
    }
    public function PropertyDetail($id)
{
    $property = Property::where('id_listing', $id)->first();

    if (!$property) {
        abort(404);
    }

    // Ambil properti serupa berdasarkan kota, kelurahan, dan rentang harga
    $similarProperties = DB::table('property')
        ->where('id_listing', '!=', $property->id_listing)
        ->where(function ($query) use ($property) {
            $query->where('kota', $property->kota)
                  ->orWhere('kelurahan', $property->kelurahan);
        })
        ->whereBetween('harga', [$property->harga - 100000000, $property->harga + 100000000])
        ->limit(10)
        ->get();

    // Hitung rentang harga untuk properti serupa
    $minPrice = $similarProperties->min('harga');
    $maxPrice = $similarProperties->max('harga');

    // Hitung median harga untuk properti serupa
    $prices = $similarProperties->pluck('harga')->sort();
    $median = $prices->count() % 2 == 0
        ? ($prices->get($prices->count() / 2 - 1) + $prices->get($prices->count() / 2)) / 2
        : $prices->get(floor($prices->count() / 2));

    // Hitung harga rata-rata properti di wilayah tersebut
    // Menghitung harga rata-rata properti di wilayah tersebut
$avgPrice = $similarProperties->avg('harga');

// Menghitung harga rata-rata properti di wilayah tersebut
$avgPrice = $similarProperties->avg('harga');

// Jika hanya ada 1 properti di wilayah ini
if ($avgPrice == 0 || count($similarProperties) == 0) {
    // Tidak ada perbandingan harga
    $discountPercentage = "Properti ini adalah satu-satunya properti di wilayah ini, tidak ada properti sebanding untuk perbandingan harga.";
} else {
    // Hitung diskon dari harga rata-rata
    $discountPercentage = round((($avgPrice - $property->harga) / $avgPrice) * 100, 2);
}


    // Kirim data ke view
    return view("property-detail", compact('property', 'similarProperties', 'minPrice', 'maxPrice', 'median', 'avgPrice', 'discountPercentage'));
}



//     public function update(Request $request, $id_listing)
// {
//     $request->validate([
//         'gambar.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
//         'gambar' => 'nullable|array|max:4',
//         'harga' => 'required|string', // Karena kita mau formatting dari input 'Rp 1.000.000'
//     ]);

//     try {
//         // Ambil properti dari database
//         $property = DB::table('property')->where('id_listing', $id_listing)->first();
//         if (!$property) {
//             return back()->withErrors(['error' => 'Property tidak ditemukan']);
//         }

//         // Ambil gambar lama dari database
//         $oldImagePaths = $property->gambar ? explode(',', $property->gambar) : [];
//         $newImagePaths = $oldImagePaths;

//         // Setup Firebase Storage
//         $firebaseFactory = (new Factory)->withServiceAccount(storage_path('app/solusindo-website-firebase-adminsdk-fbsvc-65184b738b.json'));
//         $storage = $firebaseFactory->createStorage();
//         $bucket = $storage->getBucket('solusindo-website.firebasestorage.app');

//         if ($request->hasFile('gambar')) {
//             $newImagePaths = [];
//             foreach ($request->file('gambar') as $image) {
//                 $fileName = 'property-images/' . time() . '_' . $image->getClientOriginalName();
//                 $bucket->upload(
//                     file_get_contents($image->getRealPath()),
//                     ['name' => $fileName]
//                 );

//                 $newImagePaths[] = "https://firebasestorage.googleapis.com/v0/b/solusindo-website.firebasestorage.app/o/" . urlencode($fileName) . "?alt=media";
//             }

//             // Hapus gambar lama di Firebase setelah upload baru sukses
//             foreach ($oldImagePaths as $oldImage) {
//                 if (!empty($oldImage)) {
//                     $parsedUrl = parse_url($oldImage, PHP_URL_PATH);
//                     $fileName = urldecode(basename($parsedUrl));
//                     $object = $bucket->object('property-images/' . $fileName);
//                     if ($object->exists()) {
//                         $object->delete();
//                     }
//                 }
//             }
//         }

//         // Format harga input ke angka (hilangkan Rp, titik, spasi)
//         $cleanHarga = (int) str_replace(['Rp', '.', ' '], '', $request->harga);

//         // Update database
//         $updateData = [
//             'tipe' => $request->tipe,
//             'deskripsi' => $request->deskripsi,
//             'kamar_tidur' => $request->kamar_tidur,
//             'kamar_mandi' => $request->kamar_mandi,
//             'harga' => $cleanHarga,
//             'lokasi' => $request->lokasi,
//             'kota' => $request->kota,
//             'zipcode' => $request->zipcode,
//             'kelurahan' => $request->kelurahan,
//             'kecamatan' => $request->kecamatan,
//             'luas_tanah' => $request->luas_tanah,
//             'luas_bangunan' => $request->luas_bangunan,
//             'lantai' => $request->lantai,
//             'orientation' => $request->orientation,
//             'sertifikat' => $request->sertifikat,
//             'payment' => implode(',', $request->input('payment', [])),
//             'gambar' => implode(',', $newImagePaths),
//         ];

//         // Jangan update 'status'
//         DB::table('property')->where('id_listing', $id_listing)->update($updateData);

//         return redirect()->route('property.index')->with('success', 'Property updated successfully');
//     } catch (\Exception $e) {
//         return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
//     }
// }




public function update(Request $request, $id_listing)
{
    // Hilangkan titik pada input harga
    $request->merge([
        'harga' => str_replace('.', '', $request->harga)
    ]);

    // Validasi data
    $request->validate([
        'judul' => 'required|string|max:100',
        'tipe' => 'required|string|max:15',
        'deskripsi' => 'required|string|max:2200',
        'kamar_tidur' => 'required|integer|min:0',
        'kamar_mandi' => 'required|integer|min:0',
        'status' => 'required|string|in:Tersedia,Terjual',
        'harga' => 'required|numeric|min:0',
        'lokasi' => 'required|string|max:100',
        'provinsi' => 'required|string|max:50',
        'kota' => 'required|string|max:50',
        'kelurahan' => 'required|string|max:50',
        'sertifikat' => 'required|string|max:50',
        'lantai' => 'required|integer|min:0',
        'orientation' => 'required|string|max:15',
        'luas_tanah' => 'required|integer|min:0',
        'luas_bangunan' => 'required|integer|min:0',
        'payment' => 'nullable|array',
        'cover_image_index' => 'nullable|string',
        'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg|max:8192',
    ]);

    // Ambil data properti
    $property = Property::findOrFail($id_listing);

    // Proses gambar baru (jika ada)
    if ($request->hasFile('gambar')) {
        $imageUrls = [];
        $coverIndex = $request->input('cover_image_index');

        foreach ($request->file('gambar') as $index => $file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('property-images', $fileName, 'public');
            $url = '/storage/' . $filePath;

            if ((string) $index === $coverIndex) {
                array_unshift($imageUrls, $url); // jadikan cover
            } else {
                $imageUrls[] = $url;
            }
        }

        $property->gambar = implode(',', $imageUrls); // simpan gambar baru
    }

    // Isi data baru
    $property->judul = $request->judul;
    $property->tipe = $request->tipe;
    $property->deskripsi = $request->deskripsi;
    $property->kamar_tidur = $request->kamar_tidur;
    $property->kamar_mandi = $request->kamar_mandi;
    $property->status = $request->status;
    $property->harga = $request->harga;
    $property->lokasi = $request->lokasi;
    $property->provinsi = $request->provinsi;
    $property->kota = $request->kota;
    $property->kelurahan = $request->kelurahan;
    $property->sertifikat = $request->sertifikat;
    $property->lantai = $request->lantai;
    $property->orientation = $request->orientation;
    $property->luas_tanah = $request->luas_tanah;
    $property->luas_bangunan = $request->luas_bangunan;
    $property->payment = implode(',', $request->input('payment', []));

    // Jika status berubah menjadi Terjual, pindah ke sold_property
    if ($request->status === 'Terjual') {
        \DB::table('sold_property')->insert([
            'judul' => $property->judul,
            'deskripsi' => $property->deskripsi,
            'tipe' => $property->tipe,
            'kamar_tidur' => $property->kamar_tidur,
            'kamar_mandi' => $property->kamar_mandi,
            'harga' => $property->harga,
            'lokasi' => $property->lokasi,
            'lantai' => $property->lantai,
            'id_agent' => null,
            'luas_tanah' => $property->luas_tanah,
            'luas_bangunan' => $property->luas_bangunan,
            'kota' => $property->kota,
            'kelurahan' => $property->kelurahan,
            'sertifikat' => $property->sertifikat,
            'orientation' => $property->orientation,
            'status' => 'Terjual',
            'gambar' => $property->gambar,
            'payment' => $property->payment,
            'tanggal_dibuat' => $property->created_at,
            'tanggal_diupdate' => now(),
        ]);

        $property->delete();

        return redirect()->route('agent.properties')->with('success', 'Properti telah dipindah ke daftar terjual.');
    }

    // Simpan perubahan biasa
    $property->save();

    return redirect()->route('agent.properties')->with('success', 'Properti berhasil diperbarui!');
}



    public function edit($id)
{
    $property = Property::findOrFail($id);

    // Fetch data from a JSON file for provinces, cities, and districts
    $lokasiData = json_decode(file_get_contents(public_path('data/indonesia.json')), true);
    // Extract provinces (keys of the JSON data)
    $data = json_decode(file_get_contents(public_path('data/indonesia.json')), true);

// Ambil semua provinsi unik
$provinces = collect($data)->pluck('province')->unique()->sort()->values();
$lokasiJson = json_encode($data);
// Ambil kota berdasarkan provinsi yang disimpan di property
$cities = collect($data)
    ->where('province', $property->provinsi)
    ->pluck('regency')
    ->unique()
    ->sort()
    ->values();

$districts = collect($data)
    ->where('regency', $property->kota)
    ->pluck('district')
    ->unique()
    ->sort()
    ->values();


    return view('editproperty', compact('property', 'provinces', 'cities', 'districts', 'lokasiJson'));
}

}
