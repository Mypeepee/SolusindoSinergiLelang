<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Property;

class WhatsappController extends Controller
{
    public function verifyWebhook(Request $request)
    {
        $verify_token = env('WEBHOOK_VERIFY_TOKEN', 'mysecret');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verify_token) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function handleWebhook(Request $request)
    {
        $value = $request->input('entry.0.changes.0.value');

        // Jangan Log semua karena terlalu dalam
        Log::info('Webhook diterima');

        // Pastikan pesan ada
        if (isset($value['messages'][0]['text']['body'])) {
            $message = $value['messages'][0]['text']['body'];
        } else {
            $message = null;
        }

        $from = $value['messages'][0]['from'] ?? null;
        $phoneId = $value['metadata']['phone_number_id'] ?? env('WHATSAPP_PHONE_ID');

        Log::info('Pesan user:', ['msg' => $message, 'from' => $from]);

        if (!$message || !$from) {
            return response()->json(['status' => 'invalid message']);
        }

        // Cache keys
        $historyKey = "wa_history_" . $from;
        $propertyKey = "wa_property_" . $from;

        // 🔹 Cari propertyId baru dari pesan
        preg_match('/property-detail\/(\d+)/', $message, $matches);
        $propertyId = $matches[1] ?? null;

        if ($propertyId) {
            // update lock ke property baru
            cache()->put($propertyKey, $propertyId, now()->addHour());
        } else {
            // kalau tidak ada id baru → pakai property lama
            $propertyId = cache()->get($propertyKey);
        }

        $property = $propertyId ? Property::find($propertyId) : null;

        // 🔹 Ambil history dari cache
        $history = cache()->get($historyKey, []);
        $history[] = ['role' => 'user', 'content' => $message];

        // 🔹 Cari rekomendasi properti lain jika diminta
        $recommendationsText = "";
        if ($property && preg_match('/(rekomendasi|lain|sekitar|daerah|lokasi)/i', $message)) {
            $recommendations = Property::where('kecamatan', $property->kecamatan)
                ->where('id_listing', '!=', $property->id_listing)
                ->limit(3)
                ->get();

            if ($recommendations->count() > 0) {
                $recommendationsText = "\nRekomendasi properti lain di kecamatan {$property->kecamatan}:\n";
                foreach ($recommendations as $rec) {
                    $recommendationsText .= "- {$rec->judul} | Rp {$rec->harga} | {$rec->lokasi} | Sertifikat: {$rec->sertifikat} | Link: https://solusindolelang.com/property-detail/{$rec->id_listing}\n";
                }
            }
        }

        // 🔹 Cari properti berdasarkan lokasi/kecamatan dari pesan user
        $searchResultsText = "";
        if (preg_match('/(di|area|lokasi|cari|daerah)\s+([A-Za-z\s]+)/i', $message, $locMatch)) {
            $searchLocation = trim($locMatch[2]);
            $searchResults = Property::where('lokasi', 'like', "%$searchLocation%")
                ->orWhere('kecamatan', 'like', "%$searchLocation%")
                ->limit(3)
                ->get();

            if ($searchResults->count() > 0) {
                $searchResultsText = "\nBeberapa properti di daerah {$searchLocation}:\n";
                foreach ($searchResults as $res) {
                    $searchResultsText .= "- {$res->judul} | Rp {$res->harga} | {$res->lokasi} | Sertifikat: {$res->sertifikat} | Link: https://solusindolelang.com/property-detail/{$res->id_listing}\n";
                }
            }
        }

        // 🔹 Tentukan apakah ini chat pertama
        $isFirstMessage = count($history) <= 1;

        // 🔹 Prompt
        $prompt = "
        Kamu adalah agen properti lelang profesional, sekaligus pemilik dari Balai Lelang Solusindo bernama Jason.
        Tugasmu adalah membalas WA calon pembeli layaknya agent properti profesional.

        Aturan gaya komunikasi:" .
        ($isFirstMessage ? "
        - Pada pesan pertama, selalu perkenalkan diri: 'Halo, saya Jason dari Balai Lelang Solusindo.'
        " : "") . "
        - Balas singkat, jelas, persuasif, seperti chat WhatsApp (bukan brosur panjang).
        - Selalu sebutkan harga, sertifikat, lokasi, keamanan lelang (KPKNL, rekening negara, Solusindo yang urus dokumen).
        - Tekankan: beli lewat lelang adalah cara paling aman membeli properti.
        - Setelah memberi info, tanyakan minat klien (teknik closing).
        - Jika klien tanya harga pasaran, jelaskan keunggulan harga lelang dibanding pasar.
        - Jika klien ragu, yakinkan bahwa lelang resmi & legal.
        - Jika klien tanya lokasi → berikan alamat/lokasi properti dengan catatan:
          • Viewing hanya diperkenankan dari luar (tidak boleh masuk ke dalam rumah/bangunan).
          • Tujuannya agar calon pembeli bisa menilai lingkungan sekitar, kondisi luar bangunan, dan akses jalan.
          • Hal ini untuk menghindari kejadian yang tidak diinginkan karena properti masih dihuni/debitur.
        - Jika klien tanya prosedur pembelian, jelaskan dengan kerangka '5 Deal':
          1. Deal Lokasi → pastikan klien sudah cek lokasi untuk memastikan sendiri kondisi bangunan dan akses jalannya.
          2. Deal Harga → harga limit + biaya dokumen, belum termasuk biaya pengosongan.
          3. Deal Kondisi Bangunan → beli tanah bonus bangunan, kondisi dalam tidak bisa dilihat, tapi biasanya layak huni.
          4. Deal Sertifikat → dokumen diverifikasi ketat, dan pengurusan sertifikat langsung oleh Balai Lelang Solusindo.
             (Lebih murah & cepat: 5–8 minggu, dibanding notaris bisa 4 bulan).
          5. Deal Cara Pembelian → melalui lelang resmi KPKNL & rekening negara.
        - Tekankan: 'Klien tinggal deal, duduk, ambil dokumen, serah terima kunci. Sisanya kami yang urus.'

        - Jika klien minta detail timeline, jelaskan secara ringkas:
          • Tanda minat 10% hari ini → penjadwalan lelang ±1 bulan (verifikasi & pengumuman).
          • Pengumuman 1 & 2 di KPKNL (1 minggu jeda).
          • Uang jaminan 20% via Virtual Account KPKNL (atas nama klien).
          • Menang lelang → pelunasan maksimal H+5.
          • Kalah / batal / error sistem → uang jaminan kembali 100%.
          • Setelah menang & lunas → keluar Risalah Lelang, lalu sertifikat balik nama ±5–8 minggu (lebih cepat dari notaris).
          • Penguasaan aset: diutamakan kekeluargaan, kalau tidak bisa → eksekusi PN (±6 bulan).

        Property detail (LOCKED):
        " . ($property ? "
        ID: {$property->id_listing}
        Judul: {$property->judul}
        Tipe: {$property->tipe}
        Harga limit: Rp {$property->harga}
        Lokasi: {$property->lokasi}
        Luas: {$property->luas} m²
        Sertifikat: {$property->sertifikat}
        Kecamatan: {$property->kecamatan}
        Link: https://solusindolelang.com/property-detail/{$property->id_listing}
        " : "") . "

        $recommendationsText
        $searchResultsText
        - Jika memberi rekomendasi properti lain (berdasarkan kecamatan/lokasi):
          • Tampilkan ringkas: harga, luas, lokasi, sertifikat.
          • Selalu sertakan link properti dalam format:
            https://solusindolelang.com/property-detail/{id_listing}
          • Buat agar link bisa langsung diklik user WA.

        Pesan terakhir dari klien: \"$message\"
        Balas seolah-olah kamu agen properti profesional membalas pesan ini secara langsung, lanjutkan percakapan, jangan reset dari awal.
        ";

        try {
            // 🔹 Panggil Gemini API pakai API Key
            $response = Http::post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro-preview-03-25:generateContent?key=' . env('GEMINI_API_KEY'),
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );

            $geminiData = $response->json();
            Log::info('Gemini raw response:', $geminiData ?? []);

            $reply = $geminiData['candidates'][0]['content']['parts'][0]['text']
                ?? "Maaf kak, sedang ada kendala teknis.";

            // simpan history
            $history[] = ['role' => 'assistant', 'content' => $reply];
            cache()->put($historyKey, $history, now()->addHours(2));

        } catch (\Exception $e) {
            Log::error('Gemini error: '.$e->getMessage());
            $reply = "Maaf kak, server sedang sibuk. Coba lagi sebentar ya 🙏";
        }

        // 🔹 Kirim balasan ke WhatsApp
        try {
            $waResponse = Http::withToken(env('WHATSAPP_TOKEN'))
                ->post("https://graph.facebook.com/v17.0/{$phoneId}/messages", [
                    "messaging_product" => "whatsapp",
                    "to" => $from,
                    "type" => "text",
                    "text" => ["body" => mb_substr((string) $reply, 0, 4000)]
                ]);

            Log::info('WA API response:', $waResponse->json());
        } catch (\Exception $e) {
            Log::error('WA API error: ' . $e->getMessage());
        }

        return response()->json(['status' => 'ok']);
    }


}
