<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class ExportController extends Controller
{
    public function letters(Request $request)
    {
        // Validasi minimal secukupnya
        $request->validate([
            'selected_ids'  => 'required|string',   // CSV id_listing
            'search'        => 'nullable|string',
            'property_type' => 'nullable|string',
            'province'      => 'nullable|string',
            'city'          => 'nullable|string',
            'district'      => 'nullable|string',
            'template_url'  => 'required|url',      // <-- WAJIB: pakai URL, tidak pakai local
        ]);

        $ids = collect(array_filter(array_map('trim', explode(',', $request->selected_ids))));
        if ($ids->count() < 1) {
            return back()->with('error','Pilih minimal 1 listing.');
        }

        // Query data (hanya kolom yang dipakai di template)
        $q = \App\Models\Property::query()->whereIn('id_listing', $ids->all());

        // Optional: hormati filter yang aktif
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $q->where(function ($w) use ($search) {
                $w->where('id_listing', 'LIKE', "%{$search}%")
                  ->orWhere('lokasi', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('property_type')) $q->where('tipe', $request->property_type);
        if ($request->filled('province'))      $q->where('provinsi', $request->province);
        if ($request->filled('city'))          $q->where('kota', $request->city);
        if ($request->filled('district'))      $q->where('kecamatan', $request->district);

        $rows = $q->orderBy('id_listing')->get([
            'lokasi','luas','vendor','kota','sertifikat','harga','link'
        ]);

        if ($rows->isEmpty()) {
            return back()->with('error','Data tidak ditemukan.');
        }

        // ===== Normalisasi Google Docs / Drive link -> file DOCX
        $inputUrl  = trim($request->get('template_url'));
        $remoteUrl = $inputUrl;

        // docs.google.com/document/d/{ID}/...
        if (preg_match('~^https?://docs\.google\.com/document/d/([a-zA-Z0-9_-]+)~', $inputUrl, $m)) {
            $docId    = $m[1];
            $remoteUrl = "https://docs.google.com/document/d/{$docId}/export?format=docx";
        }
        // drive.google.com/file/d/{ID}/...
        elseif (preg_match('~^https?://drive\.google\.com/file/d/([a-zA-Z0-9_-]+)~', $inputUrl, $m)) {
            $fileId   = $m[1];
            $remoteUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
        }

        // ===== Siapkan folder kerja
        $tmpDir = storage_path('app/tmp_letters');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true); // 0755 aman di shared hosting
        if (!is_dir($tmpDir) || !is_writable($tmpDir)) {
            return back()->with('error', 'Folder kerja tidak bisa dibuat/ditulis: '.$tmpDir);
        }

        // ===== Unduh template ke file sementara
        $tempTemplate = $tmpDir . '/_tpl_' . uniqid() . '.docx';
        try {
            if (class_exists(\GuzzleHttp\Client::class)) {
                $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
                $res = $client->request('GET', $remoteUrl, [
                    'sink'    => $tempTemplate,
                    'headers' => ['User-Agent' => 'Mozilla/5.0 (compatible; PHP TemplateFetcher)'],
                ]);
                // Pastikan bukan HTML redirect
                if ($res->hasHeader('Content-Type') && stripos($res->getHeaderLine('Content-Type'), 'text/html') !== false) {
                    throw new \RuntimeException('Google mengembalikan HTML (kemungkinan belum public atau butuh login).');
                }
            } else {
                $ctx = stream_context_create([
                    'http' => ['timeout' => 30, 'header' => "User-Agent: Mozilla/5.0\r\n"],
                    'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
                ]);
                $data = @file_get_contents($remoteUrl, false, $ctx);
                if ($data === false) throw new \RuntimeException('Gagal mengunduh template_url.');
                file_put_contents($tempTemplate, $data);
            }
            if (!is_file($tempTemplate) || filesize($tempTemplate) < 100) {
                @unlink($tempTemplate);
                return back()->with('error', 'Template tidak valid / kosong. Pastikan Google Docs diset "Anyone with the link".');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengunduh template: '.$e->getMessage());
        }

        $templatePath = $tempTemplate;

        $today      = now()->translatedFormat('d F Y');
        $generated  = [];
        $errors     = []; // kumpulkan error per-baris agar tidak silent fail

        foreach ($rows as $r) {
            try {
                $tp = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

                // Hitung harga asli (sebelum markup 28.9%)
                $hargaSaatIni = (int) ($r->harga ?? 0);
                $hargaAsli    = (int) round($hargaSaatIni / 1.289);

                // Isi placeholder (pastikan di template pakai ${lokasi}, ${luas}, dst)
                $tp->setValue('lokasi',      (string)($r->lokasi ?? ''));
                $tp->setValue('luas',        (string)($r->luas ?? ''));
                $tp->setValue('vendor',      (string)($r->vendor ?? ''));
                $tp->setValue('kota',        (string)($r->kota ?? ''));
                $tp->setValue('sertifikat',  (string)($r->sertifikat ?? ''));
                $tp->setValue('harga_asli',  'Rp '.number_format($hargaAsli, 0, ',', '.'));
                $tp->setValue('tanggal',     $today);
                $tp->setValue('link',        (string)($r->link ?? ''));

                $safeKota = preg_replace('/\s+/', '_', trim((string)($r->kota ?? 'Dokumen')));
                $safeKota = substr($safeKota, 0, 40);
                $outName  = 'Surat_'.$safeKota.'_'.uniqid().'.docx';
                $outPath  = $tmpDir . '/' . $outName;

                $tp->saveAs($outPath);

                // verifikasi file beneran terbuat
                if (!is_file($outPath) || filesize($outPath) < 100) {
                    throw new \RuntimeException('Gagal menyimpan file surat: '.$outName);
                }

                $generated[] = $outPath;
            } catch (\Throwable $e) {
                // kumpulkan error tapi lanjut baris lain
                $errors[] = 'ID? '.($r->id_listing ?? '-') . ' → ' . $e->getMessage();
                continue;
            }
        }

        // Bersihkan template sementara
        if (!empty($tempTemplate) && is_file($tempTemplate)) {
            @unlink($tempTemplate);
        }

        // Jika tidak ada yang berhasil dibuat, laporkan penyebabnya
        if (count($generated) === 0) {
            $debug = [
                'rows'        => $rows->count(),
                'tmp_dir'     => $tmpDir,
                'dir_writable'=> is_writable($tmpDir),
                'tpl_exists'  => is_file($templatePath),
                'tpl_size'    => @filesize($templatePath),
                'errors'      => $errors,
            ];
            return back()->with('error', 'Tidak ada file yang berhasil dibuat. Debug: '.json_encode($debug));
        }

        // Satu file → kirim langsung
        if (count($generated) === 1) {
            $file = $generated[0];
            // pastikan tidak ada output buffering mengganggu
            if (function_exists('ob_get_level')) {
                while (ob_get_level() > 0) { @ob_end_clean(); }
            }
            return response()->download($file, basename($file))->deleteFileAfterSend(true);
        }

        // Banyak file → zip
        $zipName = 'Surat_'.now()->format('Ymd_His').'.zip';
        $zipPath = $tmpDir.'/'.$zipName;
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuka ZipArchive. Aktifkan ekstensi zip di server.');
        }
        foreach ($generated as $path) {
            $zip->addFile($path, basename($path));
        }
        $zip->close();

        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) { @ob_end_clean(); }
        }
        return response()->download($zipPath, basename($zipPath))->deleteFileAfterSend(true);
    }







    public function properties(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'format'         => 'required|in:csv,xlsx',
        'selected_ids'   => 'nullable|string', // CSV of IDs
        'search'         => 'nullable|string',
        'property_type'  => 'nullable|string',
        'province'       => 'nullable|string',
        'city'           => 'nullable|string',
        'district'       => 'nullable|string',
        'vendor'         => 'nullable|string',
    ]);

    // Build query dasar + filter
    $q = Property::query();

    if ($request->filled('search')) {
        $search = trim($request->get('search'));
        $q->where(function ($w) use ($search) {
            $w->where('id_listing', 'LIKE', "%{$search}%")
              ->orWhere('lokasi', 'LIKE', "%{$search}%");
        });
    }
    if ($request->filled('property_type')) $q->where('tipe', $request->get('property_type'));
    if ($request->filled('province'))      $q->where('provinsi', $request->get('province'));
    if ($request->filled('city'))          $q->where('kota', $request->get('city'));
    if ($request->filled('district'))      $q->where('kecamatan', $request->get('district'));
    if ($request->filled('vendor'))        $q->where('vendor', $request->get('vendor'));

    // Jika user pilih ID tertentu, utamakan itu
    if ($request->filled('selected_ids')) {
        $selectedIds = collect(array_filter(array_map('trim', explode(',', $request->get('selected_ids')))));
        if ($selectedIds->isNotEmpty()) {
            $q->whereIn('id_listing', $selectedIds->all());
        }
    }

    // Ambil data minimal untuk export
    $rows = $q->orderBy('id_listing', 'asc')->get([
        'id_listing',
        'vendor',
        'id_agent',
        'kota',
        'lokasi',
        'sertifikat',
        'tipe',
        'luas',
        'harga',
        'kelurahan',
        'kecamatan',
        'provinsi',
    ]);

    // Map id_agent -> nomor telepon agent
    $agentIds = $rows->pluck('id_agent')->filter()->unique()->values();
    $agentPhones = \App\Models\Agent::whereIn('id_agent', $agentIds)->pluck('nomor_telepon', 'id_agent');

    // Helper normalisasi nomor: 0xxxx -> 62xxxx, +62xxxx -> 62xxxx, biarkan 62xxxx tetap
    $normalizePhone = function (?string $raw): string {
        if (!$raw) return '';
        $s = preg_replace('/\D+/', '', $raw); // keep digits only
        if ($s === null) return '';
        if (str_starts_with($s, '0')) {
            return '62' . substr($s, 1);
        }
        if (str_starts_with($s, '62')) {
            return $s;
        }
        // Kalau awalnya 8xxx (tanpa 0/62), anggap lokal lalu prefix 62
        if (preg_match('/^8\d+$/', $s)) {
            return '62' . $s;
        }
        return $s;
    };

    // =======================
    // Header sesuai permintaan
    // =======================
    $headers = [
        'Bank',
        'Nama',
        'No. Telp',
        'No',
        'PELISTING',
        'Agency',
        'Co PIC',
        'SOLE AGENT',
        'Kota',
        'Alamat',
        'Bukti Kepemilikan',
        'TYPE',
        'LT',
        'LB',
        'KT',
        'KM',
        'Jenis Transaksi',
        'Kondisi',
        'Take Home Commision',
        'Harga Jual',
        'Harga Limit',
        'Upping',
        'Spare Bidding',
        'Contigency',
        'Min',
        'Max',
        'Down Payment',
        'Fee Lead',
        'Fee JPRO',
        'Reimburse Biaya Lelang',
        'Gentlement Aggrement',
        'PIC Bank',
        'PL',
        'JPRO',
        'PIC',
        'Dana Operational',
        'Saving & Development',
        'Gentlement Aggrement',
        'Komisi',
        'Take Home Commision',
        'UTM',
        'Down Payment',
        'Setoran Jaminan',
        'Pelunasan',
        'Perkiraan Biaya',
        'Last Monitor',
        'Fee PIC Aset',
        'Fee Co PIC Aset',
        'Team Group Reward',
        'Fee Pelisting',
        'OR lv 1',
        'OR lv 2',
        'OR lv 3',
        'Administrasi',
        'PROFIT',
        'KELURAHAN',
        'KECAMATAN',
        'PROPINSI',
    ];

    // ==========================
    // Data per baris sesuai header
    // ==========================
    $exportArray = $rows->map(function ($r) use ($agentPhones, $normalizePhone) {

        // Kolom yang ada datanya:
        $bank            = (string)($r->vendor ?? '');
        $noRaw           = (string) (2000000 + (int) $r->id_listing);     // No = 2,000,000 + id_listing
        $pelistingPhone  = '62881026757313';                               // PELISTING = nomor tetap
        $coPicPhoneRaw   = $agentPhones[$r->id_agent] ?? '';
        $coPic           = $normalizePhone($coPicPhoneRaw);               // normalisasi
        $kota            = (string)($r->kota ?? '');
        $alamat          = (string)($r->lokasi ?? '');
        $bukti           = (string)($r->sertifikat ?? '');
        $type            = (string)($r->tipe ?? '');
        $lt              = $r->luas;
        $jenisTransaksi  = 'LELANG';                                      // fixed
        $hargaJual       = $r->harga;
        $kelurahan       = (string)($r->kelurahan ?? '');
        $kecamatan       = (string)($r->kecamatan ?? '');
        $provinsi        = (string)($r->provinsi ?? '');

        // Sisanya kosong
        $blank = '';

        return [
            /* Bank */                   $bank,
            /* Nama */                   $blank,
            /* No. Telp */               $blank,
            /* No */                     $noRaw,
            /* PELISTING */              $pelistingPhone,
            /* Agency */                 $blank,
            /* Co PIC */                 $coPic,
            /* SOLE AGENT */             $blank,
            /* Kota */                   $kota,
            /* Alamat */                 $alamat,
            /* Bukti Kepemilikan */      $bukti,
            /* TYPE */                   $type,
            /* LT */                     $lt,
            /* LB */                     $blank,
            /* KT */                     $blank,
            /* KM */                     $blank,
            /* Jenis Transaksi */        $jenisTransaksi,
            /* Kondisi */                $blank,
            /* Take Home Commision */    $blank,
            /* Harga Jual */             $hargaJual,
            /* Harga Limit */            $blank,
            /* Upping */                 $blank,
            /* Spare Bidding */          $blank,
            /* Contigency */             $blank,
            /* Min */                    $blank,
            /* Max */                    $blank,
            /* Down Payment */           $blank,
            /* Fee Lead */               $blank,
            /* Fee JPRO */               $blank,
            /* Reimburse Biaya Lelang */ $blank,
            /* Gentlement Aggrement */   $blank,
            /* PIC Bank */               $blank,
            /* PL */                     $blank,
            /* JPRO */                   $blank,
            /* PIC */                    $blank,
            /* Dana Operational */       $blank,
            /* Saving & Development */   $blank,
            /* Gentlement Aggrement */   $blank, // kedua
            /* Komisi */                 $blank,
            /* Take Home Commision */    $blank, // kedua
            /* UTM */                    $blank,
            /* Down Payment */           $blank, // kedua
            /* Setoran Jaminan */        $blank,
            /* Pelunasan */              $blank,
            /* Perkiraan Biaya */        $blank,
            /* Last Monitor */           $blank,
            /* Fee PIC Aset */           $blank,
            /* Fee Co PIC Aset */        $blank,
            /* Team Group Reward */      $blank,
            /* Fee Pelisting */          $blank,
            /* OR lv 1 */                $blank,
            /* OR lv 2 */                $blank,
            /* OR lv 3 */                $blank,
            /* Administrasi */           $blank,
            /* PROFIT */                 $blank,
            /* KELURAHAN */              $kelurahan,
            /* KECAMATAN */              $kecamatan,
            /* PROPINSI */               $provinsi,
        ];
    })->toArray();

    $filenameBase = 'export_properti_' . now()->format('Ymd_His');

    // Keputusan format
    $wantXlsx       = ($validated['format'] === 'xlsx');
    $excelAvailable = class_exists(\Maatwebsite\Excel\Facades\Excel::class)
                      && interface_exists(\Maatwebsite\Excel\Concerns\FromArray::class);
    $zipAvailable   = class_exists('ZipArchive');

    if ($wantXlsx && $excelAvailable && $zipAvailable) {
        $export = new class($headers, $exportArray) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $headers; private $data;
            public function __construct($headers, $data) { $this->headers = $headers; $this->data = $data; }
            public function headings(): array { return $this->headers; }
            public function array(): array { return $this->data; }
        };

        $response = \Maatwebsite\Excel\Facades\Excel::download($export, $filenameBase . '.xlsx');
        $response->headers->set('X-Export-Count', (string)count($exportArray));
        return $response;
    }

    // Fallback CSV
    $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($headers, $exportArray) {
        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
        fputcsv($handle, $headers);
        foreach ($exportArray as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="'.$filenameBase.'.csv"');
    $response->headers->set('X-Export-Count', (string)count($exportArray));

    return $response;
}







}
