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
        if (!$url) return false;

        // Ambil public_id dari URL
        $parts = parse_url($url);
        $path = ltrim($parts['path'], '/'); // image/upload/v123/laravel_uploads/abcd1234.jpg
        $pathParts = explode('/', $path);

        // Ambil semua setelah "upload/"
        $uploadIndex = array_search('upload', $pathParts);
        $publicIdWithExt = implode('/', array_slice($pathParts, $uploadIndex + 2));

        // Hilangkan ekstensi file
        $publicId = pathinfo($publicIdWithExt, PATHINFO_DIRNAME) . '/' . pathinfo($publicIdWithExt, PATHINFO_FILENAME);
        $publicId = trim($publicId, '/');

        // Kirim request delete ke Cloudinary
        $timestamp = time();
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $apiKey = env('CLOUDINARY_API_KEY');
        $cloudName = env('CLOUDINARY_CLOUD_NAME');

        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $response = Http::asForm()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature
        ]);

        return $response->json();
    }
}
