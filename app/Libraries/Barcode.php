<?php

namespace App\Libraries;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Exception;

class Barcode {
    public static function generateBarcode(string $string, string $fileName='') {
        try {
            if(empty($fileName)) {
                $date = date('Y-m-d');
                $fileName = "public/assets/seller/images/barcodes/$date/$string.png";
            }
            // Create dir
            if(!empty(dirname($fileName)) && !is_dir(dirname($fileName))) {
                @mkdir(dirname($fileName), 0777, true);
            }
            // Generate barcode
            $barcode = new BarcodeGeneratorPNG();
            file_put_contents($fileName, $barcode->getBarcode($string, $barcode::TYPE_CODE_128));
            return $fileName;
        } catch(Exception $e) {
            return '';
        }
    }
}
