<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{
    public function downloadKTP($id)
    {
        $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();

        // Ambil metadata (untuk nama file)
        $metadata = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$id}?fields=name");

        if (!$metadata->successful()) {
            abort(404, 'File tidak ditemukan');
        }

        $fileName = $metadata->json('name');

        // Ambil file content (actual file)
        $fileResponse = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/octet-stream'])
            ->get("https://www.googleapis.com/drive/v3/files/{$id}?alt=media");

        if (!$fileResponse->successful()) {
            abort(500, 'Gagal mengunduh file dari Google Drive');
        }

        return Response::make($fileResponse->body(), 200, [
            'Content-Type' => $fileResponse->header('Content-Type'),
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function downloadNPWP($id)
    {
        $accessToken = app(\App\Http\Controllers\GdriveController::class)->token();

        // Ambil metadata file NPWP (nama file)
        $metadata = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$id}?fields=name");

        if (!$metadata->successful()) {
            abort(404, 'File NPWP tidak ditemukan');
        }

        $fileName = $metadata->json('name');

        // Ambil isi file dari Google Drive
        $fileResponse = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/octet-stream'])
            ->get("https://www.googleapis.com/drive/v3/files/{$id}?alt=media");

        if (!$fileResponse->successful()) {
            abort(500, 'Gagal mengunduh file NPWP dari Google Drive');
        }

        return Response::make($fileResponse->body(), 200, [
            'Content-Type' => $fileResponse->header('Content-Type'),
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

}
