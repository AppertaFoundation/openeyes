<?php
/**
 * OpenEyes
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 11/08/2016
 * Time: 15:30
 */

use \Endroid\QrCode\QrCode;

class SignatureQRCodeGenerator
{
    /**
     * @param $text
     * @param $size
     * @return resource
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    public function createQRCode($text, $size)
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText($text)
            ->setSize($size)
            ->setPadding(3)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setDrawQuietZone(false)
            ->setDrawBorder(false)
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

        return $qrCode->getImage();

    }

    /**
     * @param $text
     * @param bool $returnObject
     * @return resource
     */
    public function generateQRSignatureBox( $text, $returnObject = true, $box_size =array("x"=>700, "y"=>140), $qr_size =130 )
    {
        $canvas = imagecreatetruecolor($box_size["x"],$box_size["y"]);
        $black = imagecolorallocate($canvas, 0,0,0);
        $white = imagecolorallocate($canvas, 255,255,255);
        imagefill($canvas,0,0,$black);

        imagefilledrectangle($canvas, 3, 3, imagesx($canvas)-4, imagesy($canvas)-4, $white);

        $qrCode = $this->createQRCode( $text, $qr_size );
        imagecopy($canvas, $qrCode, (imagesx($canvas)-imagesx($qrCode))-3, 3, 0, 0, imagesx($qrCode), imagesy($qrCode));
        //imageloadfont("Arial.ttf");
        // TODO: check how to load font here!
        //imagestring($canvas, null, 10, 145, "This signature will be user for OpenEyes eCVI module to print.", $black);
        if ($returnObject){
            return $canvas;
        }else {
            // Output and free from memory
            header('Content-Type: image/jpeg');

            imagejpeg($canvas);
            imagedestroy($canvas);
        }
    }
}