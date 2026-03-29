<?php
namespace app\services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrService
{
    public function generateDataUri(string $url, int $size = 200): string
    {
        $options = new QROptions([
            'version'    => 5,
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 5,
            'imageBase64' => true,
        ]);

        $qr = new QRCode($options);
        $pngData = $qr->render($url); // возвращает Data URI в формате base64

        return $pngData; // готовый Data URI для вставки в <img src="...">
    }
}