<?php

namespace App\Libraries;

use Exception;
use Image;

class ImageCompress {
    /**
     * Compress image file size.
     *
     * @param  Illuminate\Support\Facades\File  $file
     * @param  string $branch_type
     * @return boolean
     */
    public static function compress($file, $quality=10) {
        try {
            // Check file size is greater then 50 KB
            if($file->getSize() > 5000) {
                // Compress file size
                $image = Image::make($file->getRealPath());
                if($file->getMimeType() == 'image/png') {
                    // Encode png files to jpg
                    $image = $image->encode('jpg', $quality);
                }
                // Save file in low quality
                $image->save($file->getRealPath(), $quality);
            }
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}