<?php
namespace App\Helpers;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;

class GoogleDriveUploader
{
    public static function upload($localPath, $filename, $folderId)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(Google_Service_Drive::DRIVE);

        $service = new Google_Service_Drive($client);

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $filename,
            'parents' => [$folderId],
        ]);

        $content = file_get_contents($localPath);

        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => mime_content_type($localPath),
            'uploadType' => 'multipart',
        ]);

        // Buat bisa diakses publik
        $permission = new Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]);
        $service->permissions->create($file->id, $permission);

        return "https://drive.google.com/uc?id=" . $file->id;
    }
}
