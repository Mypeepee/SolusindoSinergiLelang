<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showJoinAgentForm()
    {
        $id_account = Session::get('id_account') ?? $_COOKIE['id_account'] ?? null;

        if (!$id_account) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = DB::table('account')->where('id_account', $id_account)->first();
        $informasi_klien = DB::table('informasi_klien')->where('id_account', $id_account)->first();

        return view('register-agent', compact('user', 'informasi_klien'));
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
        ]);

        $user = Account::where('id_account', $request->id_account)->first();
        if (!$user) {
            return redirect()->route('register')->with('error', 'Akun tidak ditemukan.');
        }

        // Ambil data informasi_klien (jika ada)
        $clientData = DB::table('informasi_klien')->where('id_account', $user->id_account)->first();

        // === FOTO PROFIL AGEN ===
        $picturePath = null;

        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('data_agent', 'public');
        } elseif ($request->filled('cropped_profile_image')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_profile_image));
            $fileName = 'data_agent/agent_' . Str::random(10) . '.jpg';
            file_put_contents(storage_path('app/public/' . $fileName), $imageData);
            $picturePath = $fileName;
        }

        // === KTP ===
        $ktpFileName = null;
        if ($request->filled('cropped_image_ktp')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_image_ktp));
            $ktpFileName = 'data_agent/ktp_' . Str::random(10) . '.jpg';
            file_put_contents(storage_path('app/public/' . $ktpFileName), $imageData);
        } elseif ($clientData && $clientData->gambar_ktp) {
            $ktpFileName = $clientData->gambar_ktp;
        } else {
            return back()->withErrors(['cropped_image_ktp' => 'Gambar KTP wajib diunggah.']);
        }

        // === NPWP ===
        $npwpFileName = null;
        if ($request->filled('cropped_image_npwp')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_image_npwp));
            $npwpFileName = 'data_agent/npwp_' . Str::random(10) . '.jpg';
            file_put_contents(storage_path('app/public/' . $npwpFileName), $imageData);
        } elseif ($clientData && $clientData->gambar_npwp) {
            $npwpFileName = $clientData->gambar_npwp;
        }

        // Update role user jadi Pending
        $user->roles = 'Pending';
        $user->save();

        // Simpan/Update data ke tabel agent
        Agent::updateOrCreate(
            ['id_account' => $user->id_account],
            [
                'id_account' => $user->id_account,
                'nama' => $request->nama,
                'email' => $request->email ?? $user->email,
                'deskripsi' => $request->deskripsi,
                'nomor_telepon' => $request->nomor_telepon,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
                'kota' => $request->lokasi_kerja,
                'status' => 'Pending',
                'picture' => $picturePath,
                'gambar_ktp' => $ktpFileName,
                'gambar_npwp' => $npwpFileName,
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ]
        );

        // Kosongkan session agar user login ulang
        session()->flush();

        return redirect()->back()->with('success', 'Pendaftaran agen berhasil. Menunggu verifikasi dari Owner.');
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

        // Jika pakai base64 dari cropper
        if ($request->filled('cropped_profile_image')) {
            $base64Image = $request->cropped_profile_image;
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $filename = 'data_agent/' . uniqid('agent_') . '.jpg';

            Storage::disk('public')->put($filename, $imageData);
            $agent->picture = $filename;
        }

        $agent->save();

        return redirect()->route('profile', ['id_account' => $id_account])->with('success', 'Foto profil berhasil diperbarui.');
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
        // Validasi inputan yang boleh diubah user
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string|max:15',
            // 'message' => 'nullable|string',
        ]);

        // Ambil id_account dari session
        $id_account = session('id_account');
        if (!$id_account) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cari user berdasarkan id_account
        $user = Account::where('id_account', $id_account)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Update field yang boleh diubah
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->nomor_telepon = $request->nomor_telepon;

        // Update pesan tambahan jika ada
        // $user->message = $request->filled('message') ? $request->message : null;

        // Simpan perubahan
        $user->save();

        return redirect()->route('profile', ['id_account' => $user->id_account])->with('success', 'Profil berhasil diperbarui!');

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
            'informasi_klien' => $data_ktp_npwp  // <== INI DIA YANG DIMAKSUD
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

            // Debugging: Tampilkan semua data session & cookie yang tersimpan

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
        // Custom error messages
        $messages = [
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain.',
        ];

        // Validasi data input
        $request->validate([
            'nama' => 'required|string',
            'email' => 'required|email|unique:account,email',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
            'username' => 'required|string|unique:account,username',
            'password' => 'required|string|min:5',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
        ], $messages);

        // Buat akun baru
        $customer = Account::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon,
            'username' => $request->username,
            'password' => $request->password, // NOTE: tidak di-bcrypt
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
        ]);

        if (!$customer) {
            return redirect()->back()->with('error', 'Registrasi gagal, silakan coba lagi.');
        }

        \Log::info('Customer created successfully: ', ['id' => $customer->id_account]);

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

        // Decode base64 cropped image dan simpan
        if ($request->filled('cropped_image')) {
            $base64Image = $request->input('cropped_image');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $filename = 'ktp_' . uniqid() . '.jpg';
            Storage::disk('public')->put("ktp/{$filename}", $imageData);
            $data['gambar_ktp'] = "ktp/{$filename}";
        }

        $id_account = session('id_account');
        $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

        if ($existing) {
            DB::table('informasi_klien')->where('id_account', $id_account)->update($data);
        } else {
            $data['id_account'] = $id_account;
            $data['tanggal_dibuat'] = now();
            DB::table('informasi_klien')->insert($data);
        }

        return redirect()->route('profile', ['id_account' => $id_account])
            ->with('success', 'Data KTP berhasil disimpan!');
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

        if ($request->filled('cropped_image')) {
            $base64Image = $request->cropped_image;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $fileName = 'ktp/' . uniqid() . '.jpg';
                Storage::disk('public')->put($fileName, $imageData);
                $data['gambar_ktp'] = $fileName;
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
            // 'gambar_npwp' => 'nullable|image|max:2048', // bisa diabaikan karena pakai cropped_image_npwp
        ]);

        $data = [
            'nomor_npwp' => $request->nomor_npwp,
            'tanggal_diupdate' => now(),
        ];

        if ($request->filled('cropped_image_npwp')) {
            // Decode base64
            $imageData = $request->cropped_image_npwp;
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData) = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            // Buat nama file unik di folder npwp
            $fileName = 'npwp/' . uniqid() . '.jpg';

            // Simpan file di disk public
            Storage::disk('public')->put($fileName, $imageData);

            // Simpan path ke DB
            $data['gambar_npwp'] = $fileName;
        }

        $id_account = session('id_account');
        $existing = DB::table('informasi_klien')->where('id_account', $id_account)->first();

        if ($existing) {
            DB::table('informasi_klien')->where('id_account', $id_account)->update($data);
        } else {
            $data['id_account'] = $id_account;
            $data['tanggal_dibuat'] = now();

            // Isi kolom wajib lainnya yang belum diisi
            $data['nik'] = ''; // atau 'BELUM-DIISI', tergantung skema kamu

            DB::table('informasi_klien')->insert($data);
        }


        return redirect()->route('profile', ['id_account' => $id_account])
            ->with('success', 'Data NPWP berhasil disimpan!');
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

        if ($request->filled('cropped_image_npwp')) {
            // Decode base64
            $imageData = $request->cropped_image_npwp;
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData) = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            // Buat nama file unik di folder npwp
            $fileName = 'npwp/' . uniqid() . '.jpg';

            // Simpan file di disk public
            Storage::disk('public')->put($fileName, $imageData);

            // Simpan path ke DB
            $data['gambar_npwp'] = $fileName;
        }

        DB::table('informasi_klien')
        ->where('id_account', $id_account)
        ->update($data);

        return redirect()->route('profile', ['id_account' => $id_account])->with('success', 'Data NPWP berhasil diperbarui!');
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
}
