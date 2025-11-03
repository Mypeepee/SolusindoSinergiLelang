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

        // Jika user pilih ID tertentu, utamakan itu
        $selectedIds = collect([]);
        if ($request->filled('selected_ids')) {
            $selectedIds = collect(array_filter(array_map('trim', explode(',', $request->get('selected_ids')))));
            if ($selectedIds->isNotEmpty()) {
                $q->whereIn('id_listing', $selectedIds->all());
            }
        }

        // Ambil data untuk export (alias-kan kolom link agar tidak bentrok)
        $rows = $q->orderBy('id_listing', 'asc')->get([
            'id_listing',
            'lokasi',
            'tipe',
            'luas',
            'harga',
            'sertifikat',
            'gambar',
            'link as property_link',  // <— PENTING: pakai alias
            'id_agent',
        ]);

        // Header kolom export
        $headers = [
            'ID',
            'Lokasi',
            'Tipe',
            'Luas',
            'Harga',
            'Sertifikat',
            'Gambar',
            'Link',
            'Link Solusindo',
        ];

        $solusindoLinkBase = 'https://solusindolelang.com/property-detail';

        // Transform rows sesuai header (pakai alias property_link)
        $exportArray = $rows->map(function ($r) use ($solusindoLinkBase) {
            $id  = $r->id_listing;
            $aid = $r->id_agent;
            $linkSolusindo = "{$solusindoLinkBase}/{$id}/{$aid}";

            // Pakai getOriginal sebagai jaring pengaman kalau ada accessor aneh
            $propertyLink = $r->getOriginal('property_link') ?? $r->property_link ?? '';

            return [
                (string)$id,                 // ID
                (string)($r->lokasi ?? ''),  // Lokasi
                (string)($r->tipe ?? ''),    // Tipe
                $r->luas,                    // Luas
                $r->harga,                   // Harga
                (string)($r->sertifikat ?? ''), // Sertifikat
                (string)($r->gambar ?? ''),     // Gambar (CSV URL atau path)
                trim((string)$propertyLink),    // Link dari kolom property.link
                $linkSolusindo,                 // Link Solusindo
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
            // Debug header (opsional)
            $filledLinkCount = collect($exportArray)->where(fn($r) => !empty($r[7]))->count(); // kolom ke-8 = Link
            $response->headers->set('X-Export-Debug', 'wantXlsx=1; excelAvailable=1; zipAvailable=1');
            $response->headers->set('X-Export-Link-Filled', (string)$filledLinkCount);
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

        $filledLinkCount = collect($exportArray)->where(fn($r) => !empty($r[7]))->count();
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filenameBase.'.csv"');
        $response->headers->set('X-Export-Debug', 'wantXlsx=' . ($wantXlsx?1:0) . '; excelAvailable=' . ($excelAvailable?1:0) . '; zipAvailable=' . ($zipAvailable?1:0));
        $response->headers->set('X-Export-Link-Filled', (string)$filledLinkCount);

        return $response;
    }

}
