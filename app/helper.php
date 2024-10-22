<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;


if (!function_exists('store_image')) {
    /**
     * Store image and return filename
     * @param $image
     * @param string $path
     * @return string|null
     */
    function store_image($image, $path = 'uploads')
    {
        if (!$image) return null;

        // Create directory if it doesn't exist
        $storage_path = storage_path("app/public/{$path}");
        if (!file_exists($storage_path)) {
            mkdir($storage_path, 0755, true);
        }

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        
        // Move image to storage
        $image->move($storage_path, $filename);
        
        return $filename;
    }
}

if (!function_exists('delete_image')) {
    /**
     * Delete image from storage
     * @param string|null $filename
     * @param string $path
     * @return bool
     */
    function delete_image($filename, $path = 'uploads')
    {
        if (!$filename) return false;

        $filepath = storage_path("app/public/{$path}/{$filename}");
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}





?>