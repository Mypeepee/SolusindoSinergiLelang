<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Http;
use App\Models\Agent;
use Illuminate\Support\Str;


use App\Models\User;

class propertydetailController extends Controller
{
    public function show($id_listing)
    {
        // Ambil data property berdasarkan id_listing
        $property = Property::with(['agent.account'])->where('id_listing', $id_listing)->firstOrFail();

        return view('propertyDetail', compact('property'));
    }

    public function PropertyDetail(Request $request, $id, $agent = null)
{
    // ================================
    // 🔒 ADD: Deteksi crawler (bot OG)
    // ================================
    $ua = \Illuminate\Support\Str::lower($request->header('User-Agent', ''));
    $isCrawler = \Illuminate\Support\Str::contains($ua, [
        'facebookexternalhit', 'whatsapp', 'telegrambot', 'twitterbot', 'linkedinbot'
    ]);

    $property = Property::where('id_listing', $id)->first();

    // **Ambil kode referal dari account jika user login**
    // 🔒 ADD: Redirect referral HANYA untuk non-crawler (biar bot tidak diping-pong)
    if (!$isCrawler && session()->has('id_account')) {
        $userReferral = DB::table('account')
            ->where('id_account', session('id_account'))
            ->value('kode_referal');

        if ($userReferral) {
            // Kalau URL belum pakai kode referal user → redirect pakai kode itu
            if ($agent !== $userReferral) {
                return redirect()->to(url("/property-detail/{$id}/" . $userReferral));
            }
        }
    }

    // Kalau belum ada agent di URL, tapi ada di session → redirect
    // 🔒 ADD: Juga hanya untuk non-crawler
    if (!$isCrawler && !$agent && session()->has('id_agent')) {
        return redirect()->to(url("/property-detail/{$id}/" . session('id_agent')));
    }

    // Kalau ada agent di URL → cari datanya
    $sharedAgent = null;
    if ($agent) {
        $sharedAgent = \App\Models\Agent::where('id_agent', $agent)->first();

        // Kalau agent valid → catat klik ke referral_clicks
        // 🔒 ADD: Hindari insert saat crawler (biar ringan & tanpa side-effect)
        if (!$isCrawler && $sharedAgent && $property) {
            \DB::table('referral_clicks')->insert([
                'id_agent'   => $sharedAgent->id_agent,
                'id_listing' => $property->id_listing,
                'ip'         => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ✅ Hitung harga per m² properti ini
    $thisPricePerM2 = ($property->luas > 0) ? $property->harga / $property->luas : 0;

    // ✅ Hitung range harga per m² ±25%
    $lowerBound = (int) floor($thisPricePerM2 * 0.75);
    $upperBound = (int) ceil($thisPricePerM2 * 1.25);

    // ✅ Ambil properti serupa berdasarkan kecamatan & range harga
    $similarProperties = DB::table('property')
        ->where('id_listing', '!=', $property->id_listing)
        ->whereRaw('LOWER(kecamatan) = ?', [strtolower($property->kecamatan)])
        ->whereBetween(DB::raw('harga / luas'), [$lowerBound, $upperBound])
        ->limit(10)
        ->get();

    // ✅ Default: anggap serupa di kecamatan
    $similarLocation = "Kecamatan " . $property->kecamatan;

    // ✅ Fallback: kalau properti serupa di kecamatan kosong ➡️ ambil di kota
    if ($similarProperties->isEmpty()) {
        $similarProperties = DB::table('property')
            ->where('id_listing', '!=', $property->id_listing)
            ->whereRaw('LOWER(kota) = ?', [strtolower($property->kota)])
            ->whereBetween(DB::raw('harga / luas'), [$lowerBound, $upperBound])
            ->limit(10)
            ->get();

        $similarLocation = "Kota " . $property->kota; // ✅ update location fallback

        // ✅ Kalau di kota juga tidak ada ➡️ ambil semua di kota
        if ($similarProperties->isEmpty()) {
            $similarProperties = DB::table('property')
                ->where('id_listing', '!=', $property->id_listing)
                ->whereRaw('LOWER(kota) = ?', [strtolower($property->kota)])
                ->limit(10)
                ->get();

            $similarLocation = "Kota " . $property->kota; // tetap kota
        }
    }

    // ✅ Statistik harga
    $pricesPerM2 = $similarProperties->map(function ($p) {
        return ($p->luas > 0) ? $p->harga / $p->luas : null;
    })->filter();

    $avgPricePerM2 = $pricesPerM2->avg();
    $minPricePerM2 = $pricesPerM2->min();
    $maxPricePerM2 = $pricesPerM2->max();

    // ✅ Median harga per m²
    $sortedPrices = $pricesPerM2->sort()->values();
    $count = $sortedPrices->count();
    $medianPricePerM2 = null;
    if ($count > 0) {
        $medianPricePerM2 = ($count % 2 == 0)
            ? ($sortedPrices[$count / 2 - 1] + $sortedPrices[$count / 2]) / 2
            : $sortedPrices[floor($count / 2)];
    }

    // ✅ Selisih harga properti ini
    if ($avgPricePerM2 && $thisPricePerM2) {
        $selisihPersen = round((($avgPricePerM2 - $thisPricePerM2) / $avgPricePerM2) * 100, 2);
    } else {
        $selisihPersen = "Tidak ada data pembanding di kecamatan/kota ini.";
    }

    // =======================
    // 🔧 Perbaikan OG Image (kode asli kamu — DIPERTAHANKAN)
    // =======================
    $imgRaw = $property->gambar ?? null;

    // Jika berformat JSON / CSV → ambil gambar pertama
    if (is_string($imgRaw) && \Illuminate\Support\Str::startsWith(trim($imgRaw), ['[','{'])) {
        $decoded = json_decode($imgRaw, true);
        if (is_array($decoded)) {
            $imgRaw = $decoded[0]['url'] ?? $decoded[0] ?? $imgRaw;
        }
    } elseif (is_string($imgRaw) && \Illuminate\Support\Str::contains($imgRaw, ',')) {
        $imgRaw = trim(explode(',', $imgRaw)[0]);
    }

    // Buat URL absolut (HTTPS) yang bisa diakses crawler
    $ogImage = null;
    if ($imgRaw) {
        if (\Illuminate\Support\Str::startsWith($imgRaw, ['http://', 'https://'])) {
            $ogImage = $imgRaw;
        } elseif (\Illuminate\Support\Str::startsWith($imgRaw, ['storage/', '/storage/'])) {
            // file di storage symlink
            $ogImage = url(\Illuminate\Support\Str::start($imgRaw, '/'));
        } elseif (\Illuminate\Support\Str::startsWith($imgRaw, ['public/', '/public/'])) {
            // convert public/foo.jpg -> /storage/foo.jpg (butuh storage:link)
            $path = ltrim(preg_replace('#^/?public/#', '', $imgRaw), '/');
            $ogImage = url('/storage/'.$path);
        } else {
            // anggap relative di public/
            $ogImage = url('/'.ltrim($imgRaw, '/'));
        }
    }
    // Fallback jika kosong / gagal
    if (!$ogImage) {
        $ogImage = asset('img/og-default.jpg');
    }

    // Tambahkan cache-buster agar scraper ambil versi terbaru
    $ogImage .= (str_contains($ogImage, '?') ? '&' : '?')
              . 'v=' . ($property->updated_at?->timestamp ?? time());

    // =========================================
    // 🔒 ADD: Generate DERIVATIVE 1200×630 JPG
    // =========================================
    // Path derivative lokal yang stabil untuk semua listing
    $derivativeRel      = "og/property_{$property->id_listing}.jpg";
    $derivativeDiskPath = storage_path('app/public/'.$derivativeRel);
    $derivativeUrl      = secure_url('/storage/'.$derivativeRel); // HTTPS selalu

    // Bangun kalau belum ada
    if (!file_exists($derivativeDiskPath)) {
        try {
            if (!is_dir(dirname($derivativeDiskPath))) {
                @mkdir(dirname($derivativeDiskPath), 0775, true);
            }

            if (class_exists(\Intervention\Image\ImageManagerStatic::class)) {
                // Sumber: utamakan URL langsung kalau eksternal
                if ($imgRaw && \Illuminate\Support\Str::startsWith($imgRaw, ['http://','https://'])) {
                    $binary = @file_get_contents($imgRaw);
                    if ($binary === false) throw new \RuntimeException('Gagal unduh gambar eksternal');
                    $img = \Intervention\Image\ImageManagerStatic::make($binary);
                } else {
                    // Local fallback (public/ atau default)
                    $local = public_path('/'.ltrim($imgRaw ?: 'img/og-default.jpg', '/'));
                    if (!file_exists($local)) {
                        $local = public_path('img/og-default.jpg');
                    }
                    $img = \Intervention\Image\ImageManagerStatic::make($local);
                }

                // Fit 1200x630 (upsize true)
                $img->fit(1200, 630, function($c){ $c->upsize(); })
                    ->encode('jpg', 85)
                    ->save($derivativeDiskPath);
            } else {
                // Tanpa Intervention: copy saja sumber lokal / default
                $local = public_path('/'.ltrim($imgRaw ?: 'img/og-default.jpg', '/'));
                if (!file_exists($local)) {
                    $local = public_path('img/og-default.jpg');
                }
                @copy($local, $derivativeDiskPath);
            }
        } catch (\Throwable $e) {
            // Kalau gagal total → pakai default
            @copy(public_path('img/og-default.jpg'), $derivativeDiskPath);
        }
    }

    // Pakai derivative sebagai OG utama (lebih konsisten)
    $derivativeCacheV = file_exists($derivativeDiskPath) ? filemtime($derivativeDiskPath) : time();
    $ogImageFinal = $derivativeUrl . '?v=' . $derivativeCacheV;

    // 🔒 ADD: Pastikan og:url juga HTTPS & lengkap
    $ogUrlFinal = $request->fullUrl();
    if (\Illuminate\Support\Str::startsWith($ogUrlFinal, 'http://')) {
        $ogUrlFinal = preg_replace('#^http://#', 'https://', $ogUrlFinal);
    }

    // 🔒 ADD: Build ogTags final (mempertahankan milikmu + override yg penting)
    $ogTags = [
        'og_title'       => $property->judul,
        'og_description' => \Illuminate\Support\Str::limit(($property->lokasi ?? '') . ' - ' . strip_tags($property->deskripsi ?? ''), 150),
        // gunakan derivative konsisten
        'og_image'       => $ogImageFinal,
        'og_url'         => $ogUrlFinal,
    ];

    return view("property-detail", compact(
        'property',
        'similarProperties',
        'similarLocation',
        'thisPricePerM2',
        'avgPricePerM2',
        'minPricePerM2',
        'maxPricePerM2',
        'medianPricePerM2',
        'selisihPersen',
        'ogTags',
        'sharedAgent'
    ));
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
private function getOrCreateFolder($name, $parentId, $token)
{
    $query = Http::withToken($token)->get('https://www.googleapis.com/drive/v3/files', [
        'q' => "name='{$name}' and mimeType='application/vnd.google-apps.folder' and '{$parentId}' in parents and trashed=false",
        'fields' => 'files(id, name)',
    ]);

    if ($query->successful() && count($query['files']) > 0) {
        return $query['files'][0]['id'];
    }

    $create = Http::withToken($token)->post('https://www.googleapis.com/drive/v3/files', [
        'name' => $name,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentId],
    ]);

    return $create->json('id');
}

    public function update(Request $request, $id_listing)
{
    $request->merge([
        'harga' => str_replace('.', '', $request->harga)
    ]);

    $request->validate([
        'judul' => 'required|string|max:100',
        'tipe' => 'required|string|max:50',
        'deskripsi' => 'required|string|max:2200',
        'harga' => 'required|numeric|min:0',
        'lokasi' => 'required|string|max:500',
        'provinsi' => 'required|string|max:100',
        'kota' => 'required|string|max:70',
        'kelurahan' => 'required|string|max:70',
        'sertifikat' => 'required|string|max:100',
        'luas_tanah' => 'required|integer|min:0',
        'payment' => 'nullable|array',
        'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg|max:8192',
        'cover_image_index' => 'nullable|string',
    ]);

    $property = Property::findOrFail($id_listing);

    $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
    $rootFolderId = '1yMtRi1DbiINlGSFzHzGj-MT8f7C-UANJ';

    // Hapus gambar lama dari Google Drive
    if ($property->gambar) {
        $oldThumbs = explode(',', $property->gambar);
        foreach ($oldThumbs as $thumbUrl) {
            if (preg_match('/id=([^&]+)/', $thumbUrl, $matches)) {
                $fileId = $matches[1];
                Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$fileId}");
            }
        }
    }

    // Upload gambar baru (jika ada)
    $gambarUrls = [];
    if ($request->hasFile('gambar')) {
        $coverIndex = $request->input('cover_image_index');

        $folderKota = $this->getOrCreateFolder($request->kota, $rootFolderId, $accessToken);
        $folderAlamat = $this->getOrCreateFolder($request->lokasi, $folderKota, $accessToken);

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

        $property->gambar = implode(',', $gambarUrls);
    }

    // Update field lainnya
    $property->judul = $request->judul;
    $property->tipe = $request->tipe;
    $property->deskripsi = $request->deskripsi;
    $property->harga = $request->harga;
    $property->lokasi = $request->lokasi;
    $property->provinsi = $request->provinsi;
    $property->kota = $request->kota;
    $property->kelurahan = $request->kelurahan;
    $property->sertifikat = $request->sertifikat;
    $property->luas = $request->luas_tanah; // asumsi satuan meter persegi
    $property->payment = implode(',', $request->input('payment', []));

    $property->save();

// === Ambil id_agent dari session ===
    $idAgent = session('id_agent') ?? 'DEFAULT';

    // Redirect ke detail
    return redirect()->route('property-detail', [
        'id' => $property->id_listing,
        'agent' => $idAgent
    ])->with('success', 'Properti berhasil diperbarui!');
}

    public function edit($id)
    {
        $property = Property::findOrFail($id);

        // Fetch data from a JSON file for provinces, cities, and districts
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
