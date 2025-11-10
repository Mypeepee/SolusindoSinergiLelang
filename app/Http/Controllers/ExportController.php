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

    // Pakai file yang bener: storage/app/templates/LBH Jaksa.docx
    $templatePath = storage_path('app/templates/LBH Jaksa.docx');
    if (!is_file($templatePath)) {
        return back()->with('error','Template tidak ditemukan di storage/app/templates/LBH Jaksa.docx');
    }

    $today      = now()->translatedFormat('d F Y');
    $tmpDir     = storage_path('app/tmp_letters');
    @mkdir($tmpDir, 0775, true);

    $generated  = [];

    foreach ($rows as $r) {
        $tp = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Hitung harga asli (sebelum markup 28.9%)
        // Harga_saat_ini = harga_asli * 1.289  =>  harga_asli = harga_saat_ini / 1.289
        $hargaSaatIni = (int) ($r->harga ?? 0);
        $hargaAsli    = (int) round($hargaSaatIni / 1.289);

        // Isi nilai teks sesuai placeholder template
        $tp->setValue('lokasi',      (string)($r->lokasi ?? ''));
        $tp->setValue('luas',        (string)($r->luas ?? ''));
        $tp->setValue('vendor',      (string)($r->vendor ?? ''));
        $tp->setValue('kota',        (string)($r->kota ?? ''));
        $tp->setValue('sertifikat',  (string)($r->sertifikat ?? ''));
        $tp->setValue('harga_asli',  'Rp '.number_format($hargaAsli, 0, ',', '.'));
        $tp->setValue('tanggal',     $today);
        $tp->setValue('link',        (string)($r->link ?? ''));

        $outName = 'Surat_'.preg_replace('/\s+/', '_', substr($r->kota ?? 'Dokumen', 0, 40)).'_'.uniqid().'.docx';
        $outPath = $tmpDir.'/'.$outName;
        $tp->saveAs($outPath);
        $generated[] = $outPath;
    }

    // 1 file → kirim langsung
    if (count($generated) === 1) {
        return response()->download($generated[0])->deleteFileAfterSend(true);
    }

    // Banyak file → zip
    $zipName = 'Surat_'.now()->format('Ymd_His').'.zip';
    $zipPath = $tmpDir.'/'.$zipName;
    $zip = new \ZipArchive();
    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
        foreach ($generated as $path) {
            $zip->addFile($path, basename($path));
        }
        $zip->close();
    }
    return response()->download($zipPath)->deleteFileAfterSend(true);
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
    $selectedIds = collect([]);
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
        // ===== TAMBAHAN UNTUK 3 KOLOM TERAKHIR =====
        'kelurahan',
        'kecamatan',
        'provinsi',
    ]);

    // Map id_agent -> nama agent
    $agentIds   = $rows->pluck('id_agent')->filter()->unique()->values();
    $agentNames = \App\Models\Agent::whereIn('id_agent', $agentIds)->pluck('nama', 'id_agent'); // ubah 'nama' jika field-mu 'name'

    // Header kolom export
    $headers = [
        'Bank',
        'No',
        'PELISTING',
        'Kota',
        'Alamat',
        'Bukti Kepemilikan',
        'TYPE',
        'LT',
        'Jenis Transaksi',
        'Harga Jual',
        // ===== HEADER TAMBAHAN DI PALING AKHIR =====
        'KELURAHAN',
        'KECAMATAN',
        'PROPINSI',
    ];

    // Transform rows
    $exportArray = $rows->map(function ($r) use ($agentNames) {
        $idListing = (int) $r->id_listing;
        $noRaw     = (string) (2000000 + $idListing); // <-- TANPA TITIK PEMISAH

        $pelisting = (string) ($agentNames[$r->id_agent] ?? $r->id_agent);

        return [
            (string)($r->vendor ?? ''),        // Bank
            $noRaw,                            // No (2jt + id_listing) tanpa pemisah
            $pelisting,                        // PELISTING (nama agent)
            (string)($r->kota ?? ''),          // Kota
            (string)($r->lokasi ?? ''),        // Alamat
            (string)($r->sertifikat ?? ''),    // Bukti Kepemilikan
            (string)($r->tipe ?? ''),          // TYPE
            $r->luas,                          // LT
            'LELANG',                          // Jenis Transaksi
            $r->harga,                         // Harga Jual
            // ===== NILAI TAMBAHAN DI PALING AKHIR =====
            (string)($r->kelurahan ?? ''),     // KELURAHAN
            (string)($r->kecamatan ?? ''),     // KECAMATAN
            (string)($r->provinsi ?? ''),      // PROPINSI
        ];
    })->toArray();

    $filenameBase = 'export_properti_' . now()->format('Ymd_His');

    // ===== Keputusan format =====
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
        $response->headers->set('X-Export-Debug', 'wantXlsx=1; excelAvailable=1; zipAvailable=1');
        $response->headers->set('X-Export-Count', (string)count($exportArray));
        return $response;
    }

    // ===== Fallback CSV =====
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
    $response->headers->set('X-Export-Debug', 'wantXlsx=' . ($wantXlsx?1:0) . '; excelAvailable=' . ($excelAvailable?1:0) . '; zipAvailable=' . ($zipAvailable?1:0));
    $response->headers->set('X-Export-Count', (string)count($exportArray));

    return $response;
}




}
