<?php

namespace App\Http\Controllers;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Ambil semua agent
        $agents = DB::table('agent')->get();

        $properties = [];
        $selectedAgent = null;

        // Jika ada agent_id di query, ambil property milik agent itu
        if ($request->filled('agent_id')) {
            $selectedAgent = DB::table('agent')->where('id_agent', $request->agent_id)->first();

            if ($selectedAgent) {
                $properties = DB::table('property')
                    ->where('id_agent', $selectedAgent->id_agent)
                    ->where('status', 'Tersedia')
                    ->paginate(12);
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

    public function updateKTP(Request $request)
{
    $request->validate([
        'cropped_image' => 'required', // pastikan hasil crop ada
    ]);

    try {
        // Ambil agent yang login
        $agent = DB::table('agent')->where('id_account', session('id_account'))->first();

        if (!$agent) {
            return redirect()->back()->with('error', 'Agent tidak ditemukan.');
        }

        // Simpan file hasil crop
        $data = $request->input('cropped_image');
        $data = str_replace('data:image/jpeg;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $imageName = 'ktp_' . time() . '.jpg';
        \Storage::put('public/agent_ktp/' . $imageName, base64_decode($data));

        // Update kolom gambar_ktp
        DB::table('agent')
            ->where('id_account', session('id_account'))
            ->update([
                'gambar_ktp' => 'agent_ktp/' . $imageName,
                'tanggal_diupdate' => now()
            ]);

        return redirect()->back()->with('success', 'KTP berhasil diperbarui.');
    } catch (\Exception $e) {
        \Log::error('Update KTP Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui KTP.');
    }
}

public function updateNPWP(Request $request)
{
    $request->validate([
        'cropped_image_npwp' => 'required', // hasil crop wajib ada
    ]);

    try {
        $agent = DB::table('agent')->where('id_account', session('id_account'))->first();

        if (!$agent) {
            return redirect()->back()->with('error', 'Agent tidak ditemukan.');
        }

        // Simpan file hasil crop
        $data = $request->input('cropped_image_npwp');
        $data = str_replace('data:image/jpeg;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $imageName = 'npwp_' . time() . '.jpg';
        \Storage::put('public/agent_npwp/' . $imageName, base64_decode($data));

        // Update kolom gambar_npwp
        DB::table('agent')
            ->where('id_account', session('id_account'))
            ->update([
                'gambar_npwp' => 'agent_npwp/' . $imageName,
                'tanggal_diupdate' => now()
            ]);

        return redirect()->back()->with('success', 'NPWP berhasil diperbarui.');
    } catch (\Exception $e) {
        \Log::error('Update NPWP Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui NPWP.');
    }
}

}
