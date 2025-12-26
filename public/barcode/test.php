<?php
    //echo "Hello"; exit;
    require 'vendor/autoload.php';

    // This will output the barcode as HTML output to display in the browser
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $code = $_GET['code'] ?? "123";
    $fileName = "{$code}.png";
    $code = preg_replace('/[^A-Za-z0-9\-]/', '', $code);
    file_put_contents($fileName,$generator->getBarcode($code, $generator::TYPE_CODE_128));
    header("Content-type: image/png");
    readfile($fileName);
    unlink($fileName);
