<?php

namespace App\Http\Controllers;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class propertyagentController extends Controller
{
    // public function PropertyAgent()
    // {
    //     return view("property-agent");
    // }

    public function showagent()
    {
        // Ambil semua data agen dari database
        $agents = Agent::all();
        // Kirim data agen ke view
        return view('property-agent', compact('agents'));
    }

    public function showPropertyAgent(Request $request)
{
    // daftar agent (untuk grid/selector) – ambil field yang dipakai di blade
    $agents = DB::table('agent')
        ->where('status', 'Aktif')
        ->select('id_agent', 'nama', 'picture')
        ->orderBy('nama')
        ->get();

    $selectedAgent = null;

    // default: paginator kosong biar blade yang expect paginator tetap aman
    $properties = new LengthAwarePaginator([], 0, 18, 1);

    if ($request->filled('agent_id')) {
        // pastikan agent valid + aktif
        $selectedAgent = DB::table('agent')
            ->where('id_agent', $request->agent_id)
            ->where('status', 'Aktif')
            ->first();

        if ($selectedAgent) {
            $properties = DB::table('property')
                ->leftJoin('agent', 'agent.id_agent', '=', 'property.id_agent')
                ->where('property.id_agent', $selectedAgent->id_agent)
                ->where('property.status', 'Tersedia') // prefix table!
                ->select(
                    'property.*',
                    'agent.nama as agent_nama',
                    'agent.picture as agent_picture'
                )
                ->orderByDesc('property.tanggal_dibuat')
                ->paginate(18)
                ->appends($request->query());
        }
    }

    return view('property-agent', compact('agents', 'properties', 'selectedAgent'));
}



    public function filterPropertyByAgent(Request $request)
    {
        $selectedAgent = DB::table('agent')->where('id_agent', $request->agent_id)->first();

        if (!$selectedAgent) {
            return redirect()->route('property.agent')->with('error', 'Agent tidak ditemukan');
        }

        $query = DB::table('property')
            ->where('id_agent', $selectedAgent->id_agent)
            ->where('status', 'Tersedia'); // hanya properti tersedia

        // ✅ Filter harga minimum
        if ($request->filled('min_price')) {
            $minPrice = str_replace('.', '', $request->min_price);
            $query->where('harga', '>=', $minPrice);
        }

        // ✅ Filter harga maksimum
        if ($request->filled('max_price')) {
            $maxPrice = str_replace('.', '', $request->max_price);
            $query->where('harga', '<=', $maxPrice);
        }

        // ✅ Filter tipe properti
        if ($request->filled('property_type')) {
            $query->where('tipe', $request->property_type);
        }

        // ✅ Filter provinsi
        if ($request->filled('province')) {
            $query->where('provinsi', $request->province);
        }

        // ✅ Filter kota
        $selectedCities = [];
        if ($request->filled('selected_city_values')) {
            $selectedCities = explode(',', $request->selected_city_values);
            $query->whereIn('kota', $selectedCities);
        }

        $properties = $query->paginate(12);

        return view('property-agent', [
            'agents' => DB::table('agent')->get(),
            'properties' => $properties,
            'selectedAgent' => $selectedAgent,
            'selectedCities' => $selectedCities
        ]);
    }


    public function showagentindex()
    {
        // Ambil semua data agen dari database
        $agents = Agent::all();
        // Kirim data agen ke view
        return view('index', compact('agents'));
    }

    private function getOrCreateFolder($folderName, $parentFolderId, $accessToken)
{
    // Cek apakah folder dengan nama yang sama sudah ada
    $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and '{$parentFolderId}' in parents and trashed=false";
    $search = Http::withToken($accessToken)->get('https://www.googleapis.com/drive/v3/files', [
        'q' => $query,
        'fields' => 'files(id, name)',
    ]);

    $files = $search->json('files');
    if (!empty($files)) {
        return $files[0]['id']; // Folder sudah ada
    }

    // Jika belum ada, buat folder baru
    $create = Http::withToken($accessToken)->post('https://www.googleapis.com/drive/v3/files', [
        'name' => $folderName,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentFolderId],
    ]);

    return $create->json('id');
}


    public function updateKTP(Request $request)
    {
        $request->validate([
            'cropped_image' => 'required',
        ]);

        try {
            $agent = DB::table('agent')->where('id_account', session('id_account'))->first();
            if (!$agent) {
                return redirect()->back()->with('error', 'Agent tidak ditemukan.');
            }

            $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
            $parentFolderId = '1u8faFug3GV3lB6y0L2TbwEX48IPAUtiQ';
            $folderName = \Str::slug($agent->nama ?? $agent->id_account, '_');
            $targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);

            // Hapus gambar lama di Drive jika ada
            if (!empty($agent->gambar_ktp)) {
                Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$agent->gambar_ktp}");
            }

            // Simpan file baru ke Drive
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_image));
            $filename = 'ktp_' . \Str::uuid() . '.jpg';
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

                // Set akses publik
                Http::withToken($accessToken)
                    ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);

                DB::table('agent')->where('id_account', $agent->id_account)->update([
                    'gambar_ktp' => $fileId,
                    'tanggal_diupdate' => now()
                ]);
            }

            return redirect()->back()->with('success', 'KTP berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Update KTP Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui KTP.');
        }
    }


    public function updateNPWP(Request $request)
    {
        $request->validate([
            'cropped_image_npwp' => 'required',
        ]);

        try {
            $agent = DB::table('agent')->where('id_account', session('id_account'))->first();
            if (!$agent) {
                return redirect()->back()->with('error', 'Agent tidak ditemukan.');
            }

            $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();
            $parentFolderId = '1u8faFug3GV3lB6y0L2TbwEX48IPAUtiQ';
            $folderName = \Str::slug($agent->nama ?? $agent->id_account, '_');
            $targetFolderId = $this->getOrCreateFolder($folderName, $parentFolderId, $accessToken);

            // Hapus gambar lama di Drive jika ada
            if (!empty($agent->gambar_npwp)) {
                Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$agent->gambar_npwp}");
            }

            // Simpan file baru ke Drive
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_image_npwp));
            $filename = 'npwp_' . \Str::uuid() . '.jpg';
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

                // Set akses publik
                Http::withToken($accessToken)
                    ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);

                DB::table('agent')->where('id_account', $agent->id_account)->update([
                    'gambar_npwp' => $fileId,
                    'tanggal_diupdate' => now()
                ]);
            }

            return redirect()->back()->with('success', 'NPWP berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Update NPWP Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui NPWP.');
        }
    }


}
