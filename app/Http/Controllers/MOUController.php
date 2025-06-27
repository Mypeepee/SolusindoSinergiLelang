<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
class MOUController extends Controller
{
    public function generateMOU(Request $request)
    {
        // Ambil template MOU
        $templatePath = storage_path('storage/app/templates/MOU Kosongan.docx');
        $template = new TemplateProcessor($templatePath);

        // Ganti placeholder dalam dokumen
        $template->setValue('tanggal', date('d-m-Y'));
        $template->setValue('nama_pihak_pertama', $request->nama);
        $template->setValue('nik_pihak_pertama', $request->nik);
        $template->setValue('alamat_pihak_pertama', $request->alamat);
        $template->setValue('harga', $request->harga);
        $template->setValue('lokasi_properti', $request->lokasi_properti);

        // Simpan dokumen Word baru
        $outputPath = storage_path('app/public/mou-generated.docx');
        $template->saveAs($outputPath);

        // Konversi Word ke PDF
        $pdfPath = storage_path('app/public/mou-generated.pdf');
        $pdf = Pdf::loadView('pdf.mou', ['file' => asset('storage/mou-generated.docx')])->save($pdfPath);

        return response()->download($pdfPath);
    }
}
