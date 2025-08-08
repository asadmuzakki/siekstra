<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class CloudinaryService extends ServiceProvider
{
    public static function uploadImage($file, $folder = 'laravel_uploads')
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET');

        $response = Http::asMultipart()
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                'upload_preset' => $uploadPreset,
                'folder' => $folder,
            ]);

        return $response->json();
    }

    public static function deleteImageById($publicId)
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        // Generate timestamp
        $timestamp = time();

        // Buat signature
        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $response = Http::asForm()
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
                'public_id' => $publicId,
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);

        return $response->json();
    }

    public static function deleteImageByUrl($url)
    {
        $publicId = self::extractPublicIdFromUrl($url);

        if (!$publicId) {
            return ['error' => 'URL tidak valid'];
        }

        return self::deleteImageById($publicId);
    }

    private static function extractPublicIdFromUrl($url)
    {
        $urlPath = parse_url($url, PHP_URL_PATH);
        $parts = explode('/', $urlPath);

        $uploadIndex = array_search('upload', $parts);
        if ($uploadIndex === false || !isset($parts[$uploadIndex + 1])) {
            return null;
        }

        $publicIdWithExt = implode('/', array_slice($parts, $uploadIndex + 1));

        return pathinfo($publicIdWithExt, PATHINFO_DIRNAME) . '/' . pathinfo($publicIdWithExt, PATHINFO_FILENAME);
    }
}
