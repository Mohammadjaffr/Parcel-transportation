<?php


namespace App\Services;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Save the image and return its path.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $folder
     * @return string
     */
    public function saveImage($image, $folder)
    {
        $filename = uniqid('', true) . '.' . $image->getClientOriginalExtension();
        $filePath = $folder . '/' . $filename;

        $image->storeAs($folder, $filename, 'public');
       
        return 'storage'. '/' . $filePath;
    }


public function deleteImage(?string $imagePath): bool
{
    if (empty($imagePath)) {
        return false;
    }
    try {
        $cleanPath = str_replace('storage/', '', $imagePath);
        $filePath = public_path('storage/' . $cleanPath);

        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
}

}