<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 11/08/2016
 * Time: 15:30
 */

namespace OEModule\OphCoCvi\components;

use \Endroid\QrCode\QrCode;

class SignatureQRCodeGenerator
{
    public function createQRCode($text)
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText($text)
            ->setSize(160)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setDrawQuietZone(false)
            ->setDrawBorder(false)
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

        return $qrCode->getImage();

    }

    public function generateQRSignatureBox( $text, $returnObject = true )
    {
        $canvas = imagecreatetruecolor(700,186);
        $black = imagecolorallocate($canvas, 0,0,0);
        $white = imagecolorallocate($canvas, 255,255,255);
        imagefill($canvas,0,0,$black);

        imagefilledrectangle($canvas, 3, 3, imagesx($canvas)-4, imagesy($canvas)-4, $white);

        $qrCode = $this->createQRCode( $text );
        imagecopy($canvas, $qrCode, (imagesx($canvas)-imagesx($qrCode))-3, 3, 0, 0, imagesx($qrCode), imagesy($qrCode));

        if($returnObject){
            return $canvas;
        }else {
            // Output and free from memory
            header('Content-Type: image/jpeg');

            imagejpeg($canvas);
            imagedestroy($canvas);
        }
    }
}