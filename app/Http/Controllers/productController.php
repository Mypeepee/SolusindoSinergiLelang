<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Agent;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\ServiceAccount;
use App\Models\PropertyInterest;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Helpers\GoogleDriveUploader;
use Illuminate\Support\Facades\Http;


class productController extends Controller
{
    // public function showRandom()
    // {
    //     // Ambil 6 properti secara acak dengan status available
    //     $hotListings = Property::where('status', 'Tersedia')->inRandomOrder()->take(6)->get();

    //     $properties = Property::where('status', 'Tersedia')
    //                         ->select('tipe', DB::raw('count(*) as total'))
    //                         ->groupBy('tipe')
    //                         ->get();

    //                         $testimonials = DB::table('transaction')
    //                         ->join('account', 'transaction.id_klien', '=', 'account.id_account')
    //                         ->select('account.nama', 'transaction.rating', 'transaction.comment')
    //                         ->whereNotNull('transaction.rating')
    //                         ->orderByDesc('transaction.tanggal_transaksi')
    //                         ->limit(10)
    //                         ->get();

    //                     return view('index', compact('hotListings', 'properties', 'testimonials'));
    // }

    public function showPropertyTypeIndex()
    {
        // Ambil jumlah properti per tipe dengan status 'Tersedia'
        $propertyCountsRaw = DB::table('property')
            ->select('tipe', DB::raw('count(*) as total'))
            ->whereRaw('LOWER(status) = ?', [strtolower('Tersedia')])
            ->groupBy('tipe')
            ->get();

        $counts = [];
        foreach ($propertyCountsRaw as $row) {
            $counts[strtolower(trim($row->tipe))] = $row->total;
        }

        // Kirim kedua variabel ke view
        return view('index', compact('propertyTypes', 'counts'));
    }

    public function propertyList(Request $request)
    {
        // Ambil data dari input form dan bersihkan harga dari titik
        $minPrice = str_replace('.', '', $request->input('min_price'));
        $maxPrice = str_replace('.', '', $request->input('max_price'));
        $propertyType = $request->input('property_type');
        $city = $request->input('kota'); // Ambil input kota

        // Mulai query properti
        $query = Property::where('status', 'available')
        ->whereNotNull('gambar') // ✅ hanya properti yang punya gambar
        ->where('gambar', '!=', '');

        // Cek dan tambahkan filter harga jika ada
        if (!is_null($minPrice) && $minPrice !== '') {
            $query->where('harga', '>=', $minPrice);
        }

        if (!is_null($maxPrice) && $maxPrice !== '') {
            $query->where('harga', '<=', $maxPrice);
        }

        // Cek dan tambahkan filter tipe properti jika ada
        if (!is_null($propertyType) && $propertyType !== '' && $propertyType !== 'Tipe Property') {
            $query->where('tipe', $propertyType);
        }

        // Cek dan tambahkan filter kota jika ada
        if (!is_null($city) && $city !== '' && $city !== 'Pilih Kota/Kabupaten') {
            // Filter berdasarkan nama kota yang sesuai dengan data yang ada di database
            $query->where('kota', 'LIKE', '%' . $city . '%');
        }

        // Eksekusi query dengan pagination
        $properties = $query->paginate(18);

        // Kirim hasil pencarian ke view
        return view('property-list', ['properties' => $properties]);
    }

    public function index(Request $request)
    {
        // Filter properti dengan status available
        $query = Property::where('status', 'available')
        ->whereNotNull('gambar') // ✅ hanya properti yang punya gambar
        ->where('gambar', '!=', '');

        // Jika ada filter tipe, gunakan filter itu
        if ($request->has('tipe') && !empty($request->input('tipe'))) {
            // Asumsikan kolom kategori di db bernama 'tipe'
            $query->where('tipe', $request->input('tipe'));
        }

        // Ambil properti dengan pagination 18 per halaman
        $properties = $query->paginate(18);

        // Daftar tipe properti yang ingin ditampilkan
        $propertyTypes = ['Rumah', 'Gudang', 'Apartemen', 'Tanah', 'Pabrik', 'Hotel dan Villa', 'Ruko', 'Sewa'];

        // Ambil jumlah properti per tipe dengan status available
        $propertyCountsRaw = Property::select('tipe')
            ->selectRaw('COUNT(*) as total')
            ->where('status', 'available')
            ->groupBy('tipe')
            ->get();

        // Mapping hasil ke array dengan key lowercase
        $counts = [];
        foreach ($propertyCountsRaw as $row) {
            $counts[strtolower(trim($row->tipe))] = $row->total;
        }

        // Kirim semua data ke view
        return view('properties.index', compact('properties', 'propertyTypes', 'counts'));
    }



// public function store(Request $request)
// {
//     $request->merge([
//         'harga' => str_replace('.', '', $request->harga)
//     ]);
//     // Validasi data input
//     $request->validate([
//         'tipe' => 'required|string|max:15',
//         'deskripsi' => 'required|string|max:250',
//         'kamar_tidur' => 'required|integer|min:0',
//         'kamar_mandi' => 'required|integer|min:0',
//         'harga' => 'required|numeric|min:0',
//         'lokasi' => 'required|string|max:100',
//         'kota' => 'required|string|max:50',
//         'zipcode' => 'required|string|max:10',
//         'kelurahan' => 'required|string|max:50',
//         'kecamatan' => 'required|string|max:50',
//         'sertifikat' => 'required|string|max:50',
//         'lantai' => 'required|integer|min:0',
//         'orientation' => 'required|string|max:15',
//         'gambar.*' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Ubah menjadi array
//         'luas_tanah' => 'required|integer|min:0',
//         'luas_bangunan' => 'required|integer|min:0',
//         'payment' => 'array',
//     ]);

//     // Konfigurasi Firebase Storage
//     $firebaseFactory = (new Factory)->withServiceAccount(storage_path('app/solusindo-website-firebase-adminsdk-fbsvc-65184b738b.json'));
//     $storage = $firebaseFactory->createStorage();
//     $bucket = $storage->getBucket('solusindo-website.firebasestorage.app');

//     // Upload gambar-gambar ke Firebase Storage
//     $imageUrls = [];
//     foreach ($request->file('gambar') as $file) {
//         $fileName = time() . '_' . $file->getClientOriginalName();
//         $firebasePath = 'property-images/' . $fileName;
//         $bucket->upload(
//             file_get_contents($file->getRealPath()),
//             ['name' => $firebasePath]
//         );

//         // Simpan URL gambar
//         $imageUrls[] = "https://firebasestorage.googleapis.com/v0/b/solusindo-website.firebasestorage.app/o/" . urlencode($firebasePath) . "?alt=media";
//     }

//     // Konversi array gambar ke string untuk disimpan di database
//     $imageString = implode(',', $imageUrls);

//     // Konversi metode pembayaran menjadi string
//     $paymentMethods = implode(',', $request->input('payment', []));

//     // Dapatkan ID agent yang sedang login
//     $id_account = session('id_account');
//     $agent = Agent::where('id_account', $id_account)->first();

//     // Simpan property ke dalam database
//     $property = Property::create([
//         'tipe' => $request->tipe,
//         'deskripsi' => $request->deskripsi,
//         'kamar_tidur' => $request->kamar_tidur,
//         'kamar_mandi' => $request->kamar_mandi,
//         'harga' => $request->harga,
//         'lokasi' => $request->lokasi,
//         'kota' => $request->kota,
//         'zipcode' => $request->zipcode,
//         'kelurahan' => $request->kelurahan,
//         'kecamatan' => $request->kecamatan,
//         'sertifikat' => $request->sertifikat,
//         'lantai' => $request->lantai,
//         'orientation' => $request->orientation,
//         'status' => 'Masih Ada',
//         'gambar' => $imageString, // Simpan sebagai string
//         'luas_tanah' => $request->luas_tanah,
//         'luas_bangunan' => $request->luas_bangunan,
//         'payment' => $paymentMethods,
//         'id_agent' => $id_account,
//     ]);

//     if ($agent) {
//         $existingListings = $agent->id_listing ? explode(',', $agent->id_listing) : [];
//         $existingListings[] = $property->id_listing;
//         $agent->update([
//             'id_listing' => implode(',', array_unique($existingListings)),
//         ]);
//     }

//     return back()->with('success', 'Property added successfully!');
// }
private function getOrCreateFolder($folderName, $parentId, $accessToken)
{
    $search = Http::withToken($accessToken)->get('https://www.googleapis.com/drive/v3/files', [
        'q' => "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and '{$parentId}' in parents and trashed=false",
        'fields' => 'files(id, name)',
    ]);

    if ($search->successful() && count($search['files']) > 0) {
        return $search['files'][0]['id'];
    }

    $create = Http::withToken($accessToken)->post('https://www.googleapis.com/drive/v3/files', [
        'name' => $folderName,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentId],
    ]);

    return $create->json('id');
}

    //pake laravel storage
    public function store(Request $request)
{
    $request->merge([
        'harga' => str_replace('.', '', $request->harga)
    ]);

    $request->validate([
        'judul' => 'required|string|max:100',
        'tipe' => 'required|string|max:15',
        'deskripsi' => 'required|string|max:2200',
        'harga' => 'required|numeric|min:0',
        'lokasi' => 'required|string|max:100',
        'provinsi' => 'required|string|max:50',
        'kota' => 'required|string|max:50',
        'kelurahan' => 'required|string|max:50',
        'sertifikat' => 'required|string|max:50',
        'gambar' => 'required|array|min:1',
        'gambar.*' => 'image|mimes:jpeg,png,jpg|max:8192',
        'luas_tanah' => 'required|integer|min:0',
        'payment' => 'nullable|array',
        'cover_image_index' => 'nullable|string',
    ]);

    $id_account = session('id_account');
    $agent = Agent::where('id_account', $id_account)->first();
    if (!$agent) return back()->withErrors(['agent' => 'Data agent tidak ditemukan.']);

    $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
    $rootFolderId = '1yMtRi1DbiINlGSFzHzGj-MT8f7C-UANJ';
    $folderKota = $this->getOrCreateFolder($request->kota, $rootFolderId, $accessToken);
    $folderAlamat = $this->getOrCreateFolder($request->lokasi, $folderKota, $accessToken);

    $gambarUrls = [];
    $coverIndex = $request->input('cover_image_index');

    foreach ($request->file('gambar') as $index => $file) {
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $tempPath = $file->storeAs('temp', $fileName);
        $tempFullPath = storage_path('app/' . $tempPath);

        $response = Http::withToken($accessToken)
            ->attach('metadata', json_encode([
                'name' => $fileName,
                'parents' => [$folderAlamat],
            ]), 'metadata.json')
            ->attach('file', file_get_contents($tempFullPath), $fileName)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        Storage::delete($tempPath);

        if ($response->successful()) {
            $fileId = $response->json('id');

            Http::withToken($accessToken)->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                'role' => 'reader',
                'type' => 'anyone',
            ]);

            $thumbUrl = 'https://drive.google.com/thumbnail?id=' . $fileId;

            if ((string) $index === $coverIndex) {
                array_unshift($gambarUrls, $thumbUrl);
            } else {
                $gambarUrls[] = $thumbUrl;
            }
        }
    }

    $imageString = implode(',', $gambarUrls);
    $paymentString = implode(',', $request->input('payment', []));

    $uangJaminan = (int) ($request->harga * 0.2);
    $batasJaminan = now()->addDays(30);
    $batasPenawaran = now()->addDays(31);

    $property = Property::create([
        'judul' => $request->judul,
        'tipe' => $request->tipe,
        'vendor' => 'Balai Lelang Solusindo Surabaya',
        'deskripsi' => $request->deskripsi,
        'harga' => $request->harga,
        'lokasi' => $request->lokasi,
        'provinsi' => $request->provinsi,
        'kota' => $request->kota,
        'kelurahan' => $request->kelurahan,
        'sertifikat' => $request->sertifikat,
        'status' => 'Tersedia',
        'gambar' => $imageString,
        'luas' => $request->luas_tanah + $request->luas_bangunan,
        'payment' => $paymentString,
        'uang_jaminan' => $uangJaminan,
        'batas_akhir_jaminan' => $batasJaminan,
        'batas_akhir_penawaran' => $batasPenawaran,
        'vendor' => 'Balai Lelang Solusindo Surabaya',
        'id_agent' => $agent->id_agent,
    ]);
    return redirect()->route('agent.properties')->with('success', 'Properti berhasil ditambahkan!');
}



    public function create()
    {
        return view('addProperty');
    }

    public function showInterestForm($id_listing)
    {
        $id_account = Session::get('id_account') ?? $_COOKIE['id_account'] ?? null;

        $user = DB::table('account')->where('id_account', $id_account)->first();
        $property = DB::table('property')->where('id_listing', $id_listing)->first();
        $clientData = DB::table('informasi_klien')->where('id_account', $id_account)->first();

        return view('property_interest', compact('property', 'user', 'clientData'));
    }

    public function submitInterestForm(Request $request, $id_listing)
    {
        $id_account = Session::get('id_account');

        // Ambil data dari informasi_klien
        $clientData = DB::table('informasi_klien')->where('id_account', $id_account)->first();

        if (!$clientData || !$clientData->gambar_ktp || !$clientData->gambar_npwp || !$clientData->nomor_rekening) {
            return back()->with('error', 'Lengkapi informasi klien terlebih dahulu.');
        }

        // Cek apakah sudah pernah submit interest untuk listing yang sama
        $existingInterest = DB::table('property_interests')
            ->where('id_klien', $id_account)
            ->where('id_listing', $id_listing)
            ->first();

        if ($existingInterest) {
            return back()->with('error', 'Anda sudah pernah mengajukan ketertarikan untuk properti ini.');
        }

        // Insert ke property_interests
        DB::table('property_interests')->insert([
            'id_klien' => $id_account,
            'id_listing' => $id_listing,
            'tanggal_dibuat' => now(),
            'tanggal_diupdate' => now(),
        ]);


        return redirect()->route('cart.view')->with('success', 'Berhasil mengajukan ketertarikan.');
    }

    public function showInterestPage($id_listing)
    {
        $property = Property::where('id_listing', $id_listing)->firstOrFail();
        $user = Account::where('id_account', Auth::id())->first();

        return view('propertyinterest', compact('property', 'user'));
    }

    public function showPropertyInterest($id_listing)
    {
        // Ambil data properti berdasarkan id_listing
        $property = Property::where('id_listing', $id_listing)->first();

        if (!$property) {
            return redirect()->back()->with('error', 'Properti tidak ditemukan.');
        }

        // Ambil informasi pengguna dari session
        $id_account = session('id_account'); // Ambil ID pengguna dari session
        $user = $id_account ? Account::where('id_account', $id_account)->first() : null;

        return view('property_interest', compact('property', 'user'));
    }

    public function viewCart(Request $request)
    {
        // Ambil akun yang sedang login
        $id_account = session('id_account'); // atau Auth::user()->id_account jika pakai Auth

        $joinedProperties = DB::table('property_interests')
        ->join('property', 'property.id_listing', '=', 'property_interests.id_listing')
        ->where('property_interests.id_klien', $id_account)  // ganti id_account jadi id_klien
        ->select('property.*')
        ->get();


        // Pagination manual
        $perPage = 6;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $collection = collect($joinedProperties);
        $currentItems = $collection->slice(($currentPage - 1) * $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $wonStatuses = ['selesai'];

        $wonProperties = DB::table('property_interests')
        ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
        ->where('property_interests.id_klien', $id_account)  // Ganti id_account jadi id_klien
        ->whereIn('property_interests.status', $wonStatuses)
        ->select('property.*')
        ->get();

    $closingStatuses = ['closing', 'kutipan_risalah_lelang', 'akte_grosse', 'balik_nama'];

    $closingProperties = DB::table('property_interests')
        ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
        ->where('property_interests.id_klien', $id_account)  // Ganti id_account jadi id_klien
        ->whereIn('property_interests.status', $closingStatuses)
        ->select('property.*')
        ->get();


        return view('cart', [
            'properties' => $paginated,
            'menangProperties' => $wonProperties,
            'closingProperties' => $closingProperties
        ]);
    }

    public function removeFromCart($id_listing)
    {
        $id_account = session('id_account');

        DB::table('property_interests')
    ->where('id_klien', $id_account)
    ->where('id_listing', $id_listing)
    ->delete();

            return redirect()->route('cart.view')->with('success', 'Properti berhasil dihapus dari keranjang.');
    }

    public function deleteClient(Request $request)
    {
        $request->validate([
            'id_account' => 'required',
            'id_listing' => 'required',
        ]);

        DB::table('property_interests')
            ->where('id_account', $request->id_account)
            ->where('id_listing', $request->id_listing)
            ->update([
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Client marked as deleted (updated_at changed)');
    }

}
