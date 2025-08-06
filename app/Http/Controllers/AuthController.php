<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agent;
use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Validation\Rule;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Google\Service\Drive\Permission; // pastikan ini ditambahkan
use Google\Client;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    public function showJoinAgentForm()
{

    $id_account = trim(Session::get('id_account') ?? $_COOKIE['id_account'] ?? null);

    if (!$id_account) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $user = DB::table('account')->where('id_account', $id_account)->first();
    $informasi_klien = DB::table('informasi_klien')->where('id_account', $id_account)->first();

    // ✅ Cari agent berdasarkan id_account
    $agent = DB::table('agent')
        ->whereRaw('LOWER(TRIM(id_account)) = ?', [strtolower($id_account)])
        ->first();

    $isPending = false;
    $isRejected = false;

    if ($agent) {
        if (strtolower($agent->status) === 'pending') {
            $isPending = true;
        } elseif (strtolower($agent->status) === 'approved') {
            // ✅ Kalau sudah disetujui, suruh login ulang
            return redirect()->route('login')->with('success', 'Akun Anda sudah disetujui sebagai agen. Silakan login ulang.');
        } elseif (strtolower($agent->status) === 'rejected') {
            $isRejected = true;
        }
    }

    return view('register-agent', compact('user', 'informasi_klien', 'isPending', 'isRejected'));
}

public function getOrCreateFolder($folderName, $parentFolderId, $accessToken)
{
    $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and '{$parentFolderId}' in parents and trashed=false";
    $searchResponse = Http::withToken($accessToken)
        ->get('https://www.googleapis.com/drive/v3/files', [
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

    $files = $searchResponse->json('files');

    if (!empty($files)) {
        return $files[0]['id'];
    }

    $createResponse = Http::withToken($accessToken)
        ->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentFolderId],
        ]);

    return $createResponse->json('id');
}


public function registerAgent(Request $request)
{
    $request->validate([
        'id_account' => 'required|string|exists:account,id_account',
        'nama' => 'required|string|max:100',
        'deskripsi' => 'nullable|string|max:2200',
        'nomor_telepon' => 'required|string|max:15',
        'email' => 'nullable|email|max:100',
        'instagram' => 'nullable|string|max:50',
        'facebook' => 'nullable|string|max:50',
        'lokasi_kerja' => 'required|string|max:100',
        'kota' => 'nullable|string|max:100',
        'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'cropped_image_ktp' => 'nullable|string',
        'cropped_image_npwp' => 'nullable|string',
        'cropped_profile_image' => 'nullable|string',

        'kode_referal' => [
            'nullable',
            'regex:/^[0-9]{3}$/',
            function ($attribute, $value, $fail) {
                $fullCode = 'AG' . str_pad($value, 3, '0', STR_PAD_LEFT);
                $exists = DB::table('agent')->where('id_agent', $fullCode)->exists();
                if (!$exists) {
                    $fail('Kode referral tidak valid atau tidak terdaftar.');
                }
            }
        ],
    ]);

    $user = Account::where('id_account', $request->id_account)->first();

    // === CARI UPLINE DARI KODE REFERAL ===
    $uplineId = null;
    if ($request->filled('kode_referal')) {
        $finalKode = 'AG' . str_pad($request->kode_referal, 3, '0', STR_PAD_LEFT);
        $uplineId = $finalKode;
    }

    // === Siapkan Google Drive ===
    $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
    $parentFolderId = '1u8faFug3GV3lB6y0L2TbwEX48IPAUtiQ';
    $folderName = \Str::slug($request->nama, '_');
    $targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);

    $uploadToDrive = function ($base64Data, $prefix) use ($targetFolderId, $accessToken) {
        $filename = $prefix . '_' . \Str::uuid() . '.jpg';
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));

        $tempPath = storage_path("app/temp/{$filename}");
        file_put_contents($tempPath, $imageData);

        $response = Http::withToken($accessToken)
            ->attach('metadata', json_encode([
                'name' => $filename,
                'parents' => [$targetFolderId],
            ]), 'metadata.json')
            ->attach('file', file_get_contents($tempPath), $filename)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        unlink($tempPath);

        if ($response->successful()) {
            $fileId = $response->json('id');

            Http::withToken($accessToken)
                ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                    'role' => 'reader',
                    'type' => 'anyone',
                ]);

            return $fileId;
        }

        return null;
    };

    $ktpFileId = $request->filled('cropped_image_ktp')
        ? $uploadToDrive($request->cropped_image_ktp, 'ktp')
        : DB::table('informasi_klien')->where('id_account', $user->id_account)->value('gambar_ktp');

    if (!$ktpFileId) {
        return back()->withErrors(['cropped_image_ktp' => 'Gambar KTP wajib diunggah.']);
    }

    $npwpFileId = $request->filled('cropped_image_npwp')
        ? $uploadToDrive($request->cropped_image_npwp, 'npwp')
        : DB::table('informasi_klien')->where('id_account', $user->id_account)->value('gambar_npwp');

    $profileFileId = null;
    if ($request->hasFile('picture')) {
        $path = $request->file('picture')->store('temp');
        $base64 = base64_encode(Storage::get($path));
        $profileFileId = $uploadToDrive("data:image/jpeg;base64," . $base64, 'agent');
        Storage::delete($path);
    } elseif ($request->filled('cropped_profile_image')) {
        $profileFileId = $uploadToDrive($request->cropped_profile_image, 'agent');
    }

    // === SIMPAN AGENT ===
    Agent::updateOrCreate(
        ['id_account' => $user->id_account],
        [
            'id_account' => $user->id_account,
            'upline_id' => $uplineId,
            'nama' => $request->nama,
            'email' => $request->email ?? $user->email,
            'deskripsi' => $request->deskripsi,
            'nomor_telepon' => $request->nomor_telepon,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'kota' => $request->lokasi_kerja,
            'status' => 'Pending',
            'picture' => $profileFileId,
            'gambar_ktp' => $ktpFileId,
            'gambar_npwp' => $npwpFileId,
            'tanggal_dibuat' => now(),
            'tanggal_diupdate' => now(),
        ]
    );

    return redirect('/join-agent')->with('success', 'Pendaftaran agen berhasil. Menunggu verifikasi dari Owner.');
}






public function updateProfilePicture(Request $request)
{
    $request->validate([
        'cropped_profile_image' => 'nullable|string',
    ]);

    $id_account = session('id_account');
    $agent = Agent::where('id_account', $id_account)->first();

    if (!$agent) {
        return back()->with('error', 'Data agent tidak ditemukan.');
    }

    if ($request->filled('cropped_profile_image')) {
        $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
        $parentFolderId = '1u8faFug3GV3lB6y0L2TbwEX48IPAUtiQ'; // ID Data_Agent
        $folderName = \Str::slug($agent->nama, '_');
        $targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);

        // Hapus gambar lama jika berupa file ID (panjang pendek jadi indikator)
        if (!empty($agent->picture) && strlen($agent->picture) < 100) {
            Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$agent->picture}");
        }

        // Siapkan data gambar dari base64 (langsung di-memory, tanpa file temp)
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_profile_image));
        $filename = 'agent_profile_' . \Str::uuid() . '.jpg';

        // Upload ke Google Drive
        $upload = Http::withToken($accessToken)
            ->attach('metadata', json_encode([
                'name' => $filename,
                'parents' => [$targetFolderId],
            ]), 'metadata.json')
            ->attach('file', $imageData, $filename)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if ($upload->successful()) {
            $fileId = $upload->json('id');

            // Set file agar bisa diakses publik
            Http::withToken($accessToken)
                ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                    'role' => 'reader',
                    'type' => 'anyone',
                ]);

            $agent->picture = $fileId;
        } else {
            return back()->with('error', 'Gagal mengunggah foto ke Google Drive.');
        }
    }

    $agent->save();

    return redirect()->route('profile', ['id_account' => $id_account])
        ->with('success', 'Foto profil berhasil diperbarui.');
}


    // public function registerAgent(Request $request)
    // {
    //     $request->validate([
    //         'id_account' => 'required|string|exists:account,id_account',
    //         'deskripsi' => 'nullable|string',
    //         'nomor_telepon' => 'required|string|max:15',
    //         'instagram' => 'nullable|string|max:100',
    //         'facebook' => 'nullable|string|max:100',
    //         'id_listing' => 'nullable|integer|exists:property,id_listing',
    //         'jadwal' => 'nullable|date',
    //         'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     // Ambil akun berdasarkan ID
    //     $user = Account::where('id_account', $request->id_account)->first();

    //     if (!$user) {
    //         return redirect()->route('register')->with('error', 'Akun tidak ditemukan.');
    //     }

    //     // **Pastikan perubahan peran langsung tersimpan**
    //     $user->roles = 'Pending';
    //     $user->save();  // **Pastikan save() berhasil**

    //     // Jika ada gambar yang diupload, simpan
    //     $firebaseFactory = (new Factory)->withServiceAccount(storage_path('app/solusindo-website-firebase-adminsdk-fbsvc-65184b738b.json'));
    //     $storage = $firebaseFactory->createStorage();
    //     $bucket = $storage->getBucket('solusindo-website.firebasestorage.app'); // FIX: Cara ambil storage

    //     $pictureUrl = null;
    //     if ($request->hasFile('picture')) {
    //         $file = $request->file('picture');
    //         $fileName = time() . '_' . $file->getClientOriginalName();
    //         $firebasePath = 'Foto-Agent/' . $fileName;

    //         $bucket->upload(
    //             file_get_contents($file->getRealPath()),
    //             [
    //                 'name' => $firebasePath
    //                  // Membuat file dapat diakses tanpa token
    //             ]
    //     );

    //     // URL gambar dari Firebase
    //     $pictureUrl = "https://firebasestorage.googleapis.com/v0/b/solusindo-website.firebasestorage.app/o/" . urlencode($firebasePath) . "?alt=media";
    //     }

    //     // **Simpan data ke tabel Agent**
    //     Agent::updateOrCreate(
    //         ['id_account' => $user->id_account],
    //         [
    //             'id_account' => $user->id_account,
    //             'deskripsi' => $request->deskripsi,
    //             'nomor_telepon' => $request->nomor_telepon,
    //             'instagram' => $request->instagram,
    //             'facebook' => $request->facebook,
    //             'id_listing' => $request->id_listing,
    //             'jadwal' => $request->jadwal,
    //             'picture' => $pictureUrl,
    //         ]
    //     );

    //     // **Hapus semua session agar user harus login ulang**
    //     session()->flush();

    //     // **Redirect langsung ke /login**
    //     return redirect()->back()->with('success', 'Pendaftaran agen berhasil. Menunggu verifikasi dari Owner.');
    // }

    public function updateProfile(Request $request)
    {
        // Validasi inputan
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string|max:15',
            'kode_referal' => 'nullable|string|regex:/^[0-9]{3}$/',
            'provinsi' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:70',
            'kecamatan' => 'nullable|string|max:100', // 3 digit angka
        ]);

        // Ambil id_account dari session
        $id_account = session('id_account');
        if (!$id_account) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cari user di tabel account
        $user = Account::where('id_account', $id_account)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Jika kode_referal masih kosong dan user mengisi kode baru
        if (empty($user->kode_referal) && $request->filled('kode_referal')) {
            $kodeReferal = 'AG' . str_pad($request->kode_referal, 3, '0', STR_PAD_LEFT);

            // Validasi: pastikan kode ada di tabel agent
            if (!DB::table('agent')->where('id_agent', $kodeReferal)->exists()) {
                return back()->withErrors(['kode_referal' => 'Kode referal tidak ditemukan.'])->withInput();
            }

            $user->kode_referal = $kodeReferal;
        }

        // Update field di tabel account
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->nomor_telepon = $request->nomor_telepon;
        $user->provinsi = $request->provinsi;
        $user->kota = $request->kota;
        $user->kecamatan = $request->kecamatan;

        $user->save();

        // Update field di tabel agent (kalau ada)
        $agent = \App\Models\Agent::where('id_account', $id_account)->first();
        if ($agent) {
            $agent->nomor_telepon = $request->nomor_telepon;
            $agent->save();
        }

        return redirect()->route('profile', ['id_account' => $user->id_account])
            ->with('success', 'Profil berhasil diperbarui!');
    }




public function showProfile()
{
    $id_account = Session::get('id_account') ?? $_COOKIE['id_account'] ?? null;

    if (!$id_account) {
        return redirect()->route('login')->with('error', 'Silakan login dulu.');
    }

    $user = DB::table('account')->where('id_account', $id_account)->first();

    if (!$user) {
        return redirect()->route('login')->with('error', 'User tidak ditemukan.');
    }

    // Ambil data tambahan sesuai role
    if ($user->roles === 'User' || $user->roles === 'Pending') {
        $data_ktp_npwp = DB::table('informasi_klien')->where('id_account', $id_account)->first();
    } elseif ($user->roles === 'Agent') {
        $data_ktp_npwp = DB::table('agent')->where('id_account', $id_account)->first();
    } else {
        $data_ktp_npwp = null;
    }

    return view('profile', [
        'user' => $user,
        'informasi_klien' => $data_ktp_npwp
    ]);
}


    public function Register()
    {
        return view("register");
    }

    public function Login()
    {
        return view("login");
    }

    public function loginrequest(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:5'
        ]);

        // Ambil input username & password
        $user = $request->only("username", "password");

        // Cek di database apakah user ada
        $account = Account::where('username', $user['username'])->first();

        // Jika user ditemukan & password cocok
        if ($account && $user['password'] === $account->password) {
            $remember = $request->has('remember'); // Cek apakah "remember me" dicentang

            // Simpan di session atau cookie
            if ($remember) {
                Cookie::queue('id_account', $account->id_account, 60 * 24 * 7);
                Cookie::queue('username', $account->username, 60 * 24 * 7);
                Cookie::queue('role', $account->roles, 60 * 24 * 7);
            } else {
                session()->put('id_account', $account->id_account);
                session()->put('username', $account->username);
                session()->put('role', $account->roles);
            }

            // **Tambahan**: Ambil id_agent & simpan ke session
            $agent = \App\Models\Agent::where('id_account', $account->id_account)->first();
            if ($agent) {
                session()->put('id_agent', $agent->id_agent);
            }

            return redirect('/')->with('success', 'Login Successful!');
        }


        return redirect('login')->with('error', 'Login Failed! Please Try Again.');
    }

    public function logoutrequest(Request $request)
    {
        if (session()->has('id_account') || Cookie::get('id_account') != null) {
            session()->pull('id_account');
            session()->pull('username');
            session()->pull('role');

            Cookie::queue(Cookie::forget('id_account'));
            Cookie::queue(Cookie::forget('username'));
            Cookie::queue(Cookie::forget('role'));
        }
        return redirect('/')->with('success', 'Account successfully logged out');
    }

    public function registerrequest(Request $request)
    {
        $messages = [
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain.',
            'kode_referal.exists' => 'Kode referal tidak valid.',
        ];

        $request->validate([
            'nama' => 'required|string',
            'email' => 'required|email|unique:account,email',
            'nomor_telepon' => 'required|string',
            'username' => 'required|string|unique:account,username',
            'password' => 'required|string|min:5',
            'kode_referal' => 'nullable|string|regex:/^[0-9]{3}$/', // Hanya angka 3 digit
        ], $messages);

        // Tambahkan prefix "AG" kalau user isi kode referal
        $kodeReferal = $request->filled('kode_referal') ? 'AG' . str_pad($request->kode_referal, 3, '0', STR_PAD_LEFT) : null;

        // Validasi kalau kode referal diisi → pastikan ada di tabel agent
        if ($kodeReferal && !DB::table('agent')->where('id_agent', $kodeReferal)->exists()) {
            return back()->withErrors(['kode_referal' => 'Kode referal tidak ditemukan.'])->withInput();
        }

        // Simpan ke database
        $customer = Account::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_telepon' => $request->nomor_telepon,
            'username' => $request->username,
            'password' => $request->password,
            'roles' => 'User',
            'kode_referal' => $kodeReferal,
        ]);

        if (!$customer) {
            return redirect()->back()->with('error', 'Registrasi gagal, silakan coba lagi.');
        }

        return redirect('/login')->with('success', 'Registrasi berhasil, silakan login.');
    }

public function save(Request $request)
{
    $request->validate([
        'nik' => 'required',
        'jenis_kelamin' => 'required',
        'pekerjaan' => 'required',
        'alamat' => 'required',
        'berlaku_hingga' => 'nullable|string',
        'cropped_image' => 'required|string',
    ]);

    $id_account = session('id_account');

    $data = [
        'nik' => $request->nik,
        'jenis_kelamin' => $request->jenis_kelamin,
        'pekerjaan' => $request->pekerjaan,
        'alamat' => $request->alamat,
        'berlaku_hingga' => $request->has('seumur_hidup')
            ? 'Seumur Hidup'
            : $request->berlaku_hingga,
        'tanggal_diupdate' => now(),
    ];

    $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

    // Upload ke Google Drive
    if ($request->filled('cropped_image')) {
        try {
            $base64Image = $request->input('cropped_image');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $filename = 'ktp_' . \Str::uuid() . '.jpg';

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir . "/{$filename}";
            file_put_contents($tempPath, $imageData);

            $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
            $parentFolderId = config('services.google.folder_id');

            // ✅ Ambil nama klien dari table `account`
            $namaKlien = DB::table('account')->where('id_account', $id_account)->value('nama') ?? $id_account;
            $folderName = \Str::slug($namaKlien, '_'); // Contoh: "Jimmy Gunawan" -> "jimmy_gunawan"

            // Buat folder berdasarkan nama klien
            $targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);

            // Upload file ke Google Drive
            $response = Http::withToken($accessToken)
                ->attach(
                    'metadata',
                    json_encode([
                        'name' => $filename,
                        'parents' => [$targetFolderId], // folder berdasarkan nama
                    ]),
                    'metadata.json'
                )
                ->attach('file', file_get_contents($tempPath), $filename)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful() && $response->json('id')) {
                $fileId = $response->json('id');

                // Jadikan publik
                Http::withToken($accessToken)
                    ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);

                $data['gambar_ktp'] = $fileId;
            }

            // Hapus sementara
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

        } catch (\Exception $e) {
            \Log::error('Gagal upload ke Google Drive: ' . $e->getMessage());
        }
    }

    // Simpan data
    if ($existing) {
        DB::table('informasi_klien')->where('id_account', $id_account)->update($data);
    } else {
        $data['id_account'] = $id_account;
        $data['tanggal_dibuat'] = now();
        DB::table('informasi_klien')->insert($data);
    }

    return redirect()->route('profile', ['id_account' => $id_account])
        ->with('success', 'Data KTP berhasil disimpan dan diunggah ke Google Drive!');
}


public function editKtp(Request $request)
{
    $id_account = session('id_account');

    $request->validate([
        'nik' => 'required|string|max:50',
        'alamat' => 'required|string',
        'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
        'pekerjaan' => 'nullable|string|max:100',
        'berlaku_hingga' => 'nullable|date',
        'seumur_hidup' => 'nullable',
        'cropped_image' => 'nullable|string',
    ]);

    $berlakuHingga = $request->has('seumur_hidup') ? 'Seumur Hidup' : $request->berlaku_hingga;

    $data = [
        'nik' => $request->nik,
        'alamat' => $request->alamat,
        'jenis_kelamin' => $request->jenis_kelamin,
        'pekerjaan' => $request->pekerjaan,
        'berlaku_hingga' => $berlakuHingga,
        'tanggal_diupdate' => now(),
    ];

    // Ambil data lama dari DB
    $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

    if ($request->filled('cropped_image')) {
        try {
            $base64Image = $request->cropped_image;
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $filename = 'ktp_' . \Str::uuid() . '.jpg';

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir . "/{$filename}";
            file_put_contents($tempPath, $imageData);

            $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
            $parentFolderId = config('services.google.folder_id');

            // Folder berdasarkan nama atau fallback ke ID
// Ambil nama dari tabel account
$namaKlien = DB::table('account')->where('id_account', $id_account)->value('nama') ?? $id_account;
$folderName = \Str::slug($namaKlien, '_');
$targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);


            // Hapus file lama di Google Drive
            if (!empty($existing->gambar_ktp)) {
                Http::withToken($accessToken)
                    ->delete("https://www.googleapis.com/drive/v3/files/{$existing->gambar_ktp}");
            }

            // Upload file baru ke folder user
            $response = Http::withToken($accessToken)
                ->attach(
                    'metadata',
                    json_encode([
                        'name' => $filename,
                        'parents' => [$targetFolderId],
                    ]),
                    'metadata.json'
                )
                ->attach('file', file_get_contents($tempPath), $filename)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful() && $response->json('id')) {
                $fileId = $response->json('id');

                // Set akses publik
                Http::withToken($accessToken)
                    ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);

                $data['gambar_ktp'] = $fileId;
            }

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

        } catch (\Exception $e) {
            \Log::error('Gagal edit file KTP di Google Drive: ' . $e->getMessage());
        }
    }

    DB::table('informasi_klien')
        ->where('id_account', $id_account)
        ->update($data);

    return redirect()->route('profile', ['id_account' => $id_account])
        ->with('success', 'Data KTP berhasil diperbarui!');
}



    // NPWP
    public function saveNPWP(Request $request)
{
    $request->validate([
        'nomor_npwp' => 'required|string|max:30',
        'cropped_image_npwp' => 'nullable|string',
    ]);

    $data = [
        'nomor_npwp' => $request->nomor_npwp,
        'tanggal_diupdate' => now(),
    ];

    $id_account = session('id_account');
    $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

    if ($request->filled('cropped_image_npwp')) {
        try {
            $base64Image = $request->input('cropped_image_npwp');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $filename = 'npwp_' . \Str::uuid() . '.jpg';

            // Simpan sementara
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir . "/{$filename}";
            file_put_contents($tempPath, $imageData);

            $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
            $parentFolderId = config('services.google.folder_id');

            // Nama folder dari nama klien atau ID
// Ambil nama dari tabel account
$namaKlien = DB::table('account')->where('id_account', $id_account)->value('nama') ?? $id_account;
$folderName = \Str::slug($namaKlien, '_');
$targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);


            // Upload ke folder milik klien
            $response = Http::withToken($accessToken)
                ->attach(
                    'metadata',
                    json_encode([
                        'name' => $filename,
                        'parents' => [$targetFolderId],
                    ]),
                    'metadata.json'
                )
                ->attach('file', file_get_contents($tempPath), $filename)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful() && $response->json('id')) {
                $fileId = $response->json('id');

                // Set akses publik
                Http::withToken($accessToken)
                    ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);

                $data['gambar_npwp'] = $fileId;
            }

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

        } catch (\Exception $e) {
            \Log::error('Gagal upload NPWP ke Google Drive: ' . $e->getMessage());
        }
    }

    if ($existing) {
        DB::table('informasi_klien')->where('id_account', $id_account)->update($data);
    } else {
        $data['id_account'] = $id_account;
        $data['tanggal_dibuat'] = now();
        $data['nik'] = ''; // atau default placeholder
        DB::table('informasi_klien')->insert($data);
    }

    return redirect()->route('profile', ['id_account' => $id_account])
        ->with('success', 'Data NPWP berhasil disimpan dan diunggah ke Google Drive!');
}



public function editNpwp(Request $request)
{
    $id_account = session('id_account');

    $request->validate([
        'nomor_npwp' => 'required|string|max:30',
        'cropped_image_npwp' => 'nullable|string',
    ]);

    $data = [
        'nomor_npwp' => $request->nomor_npwp,
        'tanggal_diupdate' => now(),
    ];

    $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

    if ($request->filled('cropped_image_npwp')) {
        try {
            $base64Image = $request->input('cropped_image_npwp');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $filename = 'npwp_' . \Str::uuid() . '.jpg';

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir . "/{$filename}";
            file_put_contents($tempPath, $imageData);

            $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
            $parentFolderId = config('services.google.folder_id');

            // Gunakan nama klien atau id_account sebagai nama folder
// Ambil nama dari tabel account
$namaKlien = DB::table('account')->where('id_account', $id_account)->value('nama') ?? $id_account;
$folderName = \Str::slug($namaKlien, '_');
$targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);


            // Hapus file lama jika ada
            if (!empty($existing->gambar_npwp)) {
                Http::withToken($accessToken)
                    ->delete("https://www.googleapis.com/drive/v3/files/{$existing->gambar_npwp}");
            }

            // Upload file baru ke folder user
            $response = Http::withToken($accessToken)
                ->attach(
                    'metadata',
                    json_encode([
                        'name' => $filename,
                        'parents' => [$targetFolderId],
                    ]),
                    'metadata.json'
                )
                ->attach('file', file_get_contents($tempPath), $filename)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful() && $response->json('id')) {
                $fileId = $response->json('id');

                // Jadikan file publik
                Http::withToken($accessToken)
                    ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);

                $data['gambar_npwp'] = $fileId;
            }

            // Hapus file lokal sementara
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

        } catch (\Exception $e) {
            \Log::error('Gagal upload NPWP ke Google Drive: ' . $e->getMessage());
        }
    }

    // Update DB
    DB::table('informasi_klien')
        ->where('id_account', $id_account)
        ->update($data);

    return redirect()->route('profile', ['id_account' => $id_account])
        ->with('success', 'Data NPWP berhasil diperbarui dan diunggah ke Google Drive!');
}


    // Rekening
    public function saveRekening(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'atas_nama' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:30|same:konfirmasi_rekening',
            'konfirmasi_rekening' => 'required|string|max:30',
        ]);

        $data = [
            'nama_bank' => $request->nama_bank,
            'atas_nama' => $request->atas_nama,
            'nomor_rekening' => $request->nomor_rekening,
            'tanggal_diupdate' => now(),
        ];

        $id_account = session('id_account');
        $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

        if ($existing) {
            DB::table('informasi_klien')
                ->where('id_account', $id_account)
                ->update($data);
        } else {
            $data['id_account'] = $id_account;
            $data['tanggal_dibuat'] = now();
            DB::table('informasi_klien')->insert($data);
        }

        return redirect()->route('profile', ['id_account' => $id_account])
            ->with('success', 'Data rekening berhasil disimpan!');
    }

    public function editRekening()
    {
        $id_account = session('id_account');
        $informasi_klien = DB::table('informasi_klien')->where('id_account', $id_account)->first();
        return view('informasi_klien.rekening_edit', compact('informasi_klien'));
    }

    public function showForgotForm()
    {
        return view('pass.forget');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:account,email',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Enter a valid email address.',
            'email.exists' => 'We couldn\'t find that email in our system.',
        ]);

        $email = $request->email;

        try {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Store in DB
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => $otp, 'created_at' => now()]
            );

            // Send email
            Mail::to($email)->send(new VerificationCodeMail($otp));

            // Save email in session
            session(['email' => $email]);

            return redirect()->route('otp.form')->with('status', 'An OTP has been sent to your email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send OTP. Try again.');
        }
    }

    public function showOtpForm()
    {
        return view('pass.otp'); // view input OTP
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('email');
        $storedOtp = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($storedOtp && $request->otp == $storedOtp->token) {
            return redirect()->route('password.reset.form', ['email' => $email]);
        }

        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    public function resendOtp(Request $request)
    {
        $email = session('email');  // Ambil email yang disimpan di session

        if (!$email) {
            return redirect()->route('forgot.password')->with('error', 'Session expired. Silakan masukkan email Anda lagi.');
        }

        // Generate OTP baru
        $otp = rand(100000, 999999);

        // Update OTP di tabel password_resets
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $otp, 'created_at' => now()]
        );

        // Kirim email lagi
        Mail::to($email)->send(new VerificationCodeMail($otp));

        return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    public function showResetPasswordForm($email)
    {
        return view('pass.reset', compact('email'));
    }

    public function updatePassword(Request $request, $email)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password',
        ],[
            'confirm_password.required' => 'Konfirmasi password wajib diisi.',
            'confirm_password.same' => 'Konfirmasi password tidak sama.',
        ]);

        // Update password di tabel users
        DB::table('account')->where('email', $email)->update([
            'password' => $request->new_password
        ]);

        // Hapus OTP setelah berhasil reset password
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect('/login')->with('status', 'Password berhasil direset. Silakan login.');
    }

}
