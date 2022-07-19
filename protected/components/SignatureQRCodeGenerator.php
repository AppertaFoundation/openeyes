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

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

$writer = new PngWriter();


class SignatureQRCodeGenerator
{
    /**
     * @param $text
     * @param $size
     * @return base64 string
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    public function createQRCode($text, $size, $box_size)
    {
        $result = null;
        $writer = new PngWriter();
        $qrCode = QrCode::create($text)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize($size)
            ->setMargin(0)
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(
                new Color(255, 255, 255)
            );

        $raw_file_source = explode(',', $writer->write($qrCode)->getDataUri())[1];

        $canvas = imagecreatetruecolor($box_size[0], $box_size[1]);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $black);

        imagefilledrectangle($canvas, 3, 3, imagesx($canvas) - 4, imagesy($canvas) - 4, $white);
        $qrCodeImg = imagecreatefromstring(base64_decode($raw_file_source));
        imagecopy(
            $canvas,
            $qrCodeImg,
            (imagesx($canvas) - imagesx($qrCodeImg)) - 15,
            15,
            0,
            0,
            imagesx($qrCodeImg),
            imagesy($qrCodeImg)
        );

        // Return base64 string
        ob_start();
            imagepng($canvas);
            $image_data = ob_get_contents();
        ob_end_clean();

        return base64_encode($image_data);
    }
}