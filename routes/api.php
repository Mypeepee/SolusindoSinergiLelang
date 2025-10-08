<?php
use Illuminate\Support\Facades\Route;
use App\Models\Property;
use Carbon\Carbon;
use App\Http\Controllers\WhatsappController;


Route::get('/whatsapp/webhook', [WhatsappController::class, 'verifyWebhook']);
Route::post('/whatsapp/webhook', [WhatsappController::class, 'handleWebhook']);

Route::get('/ping', fn() => ['ok' => true]);

Route::get('/property/{id}', function ($id) {
    Carbon::setLocale('id');

    // ⬇️ HAPUS ->with(['images'...]) karena gak ada relasi
    $p = Property::where('id_listing', $id)->first();

    if (!$p) {
        return response()->json(['message' => 'Property tidak ditemukan'], 404);
    }

    // Normalisasi kolom "gambar" → array URL valid
    $gambarArray = [];
    $src = $p->gambar;

    if (is_array($src)) {
        $gambarArray = array_filter($src);
    } elseif (!empty($src)) {
        $raw = trim((string) $src);

        // Jika JSON array
        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $gambarArray = array_map('strval', $decoded);
            }
        }

        // Jika bukan/ gagal JSON → pecah CSV / spasi / newline / dll
        if (!$gambarArray) {
            $gambarArray = preg_split('/[,\n\r\t ;|]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        }
    }

    // Bersihkan & validasi URL
    $gambarArray = array_values(array_filter(array_map(function ($u) {
        $u = trim((string) $u, " \t\n\r\0\x0B'\"(),.");
        return filter_var($u, FILTER_VALIDATE_URL) ? $u : null;
    }, $gambarArray)));

    return response()->json([
        'id_listing' => $p->id_listing,
        'vendor' => $p->vendor ?? 'Vendor tidak diketahui',
        'lokasi' => $p->lokasi,
        'batas_akhir_penawaran' => $p->batas_akhir_penawaran
            ? Carbon::parse($p->batas_akhir_penawaran)->translatedFormat('d F Y')
            : null,
        // ⬇️ dipakai bot WA biar kirim SATU foto
        'gambar' => $gambarArray ? implode(',', $gambarArray) : null,
        'gambar_first' => $gambarArray[0] ?? null,
    ]);
});

