<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait ImageUploadTrait
{
    /**
     * Upload an image and return the path.
     *
     * @param Request $request
     * @param string $fieldName
     * @param string $folder
     * @return string|null
     */
    public function uploadImage(Request $request, $fieldName, $folder = 'images')
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $path = $file->store($folder, 'public');
            return Storage::url($path);
        }

        return null;
    }
}
