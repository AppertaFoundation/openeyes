<?php
/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2022
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class SignatureHelper
 *
 * Static wrapper class to encapsulate useful functions for handling signatures and associated functionality
 */

class SignatureHelper {
    public static function getUserForSigning() : User
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        if (!$user) {
            throw new Exception("An error occurred while trying to fetch your signature. Please contact support.");
        }

        if(is_null($user->signature)) {
            self::bootstrapUserSignature($user->id);
            $user->refresh();
        }

        return $user;
    }

    public static function bootstrapUserSignature($user_id) {
        $user = \User::model()->findByPk($user_id);

        $file = ProtectedFile::createForWriting("user_signature_" . $user->id);
        $file->title = "Signature";
        $file->mimetype = "image/jpeg";

        $img = base64_decode(str_replace('data:image/jpeg;base64,', '', self::generateDefaultSignatureImage($user_id)));
        file_put_contents($file->getPath(), $img);
        if ($file->save()) {
            $user->signature_file_id = $file->id;
            $user->save(false, ["signature_file_id"]);
        }
    }

    public static function getUserSignatureFile($user_id)
    {
        $user = User::model()->findByPk($user_id);
        $signature_file_id = $user->signature_file_id;
        if(is_null($signature_file_id)) {
            self::bootstrapUserSignature($user_id);
            $signature_file_id = $user->signature_file_id;
            if(is_null($signature_file_id)) {
                throw new Exception(
                    "It seems that you haven't yet captured a signature in OpenEyes, and default signature was unable to be created.".
                    "Please go to your profile to capture a signature."
                );
            }
        }

        $file = $user->signature;
        $thumbnail1 = $file->getThumbnail("72x24", true);
        $thumbnail2 = $file->getThumbnail("150x50", true);

        $thumbnail1_source = file_get_contents($thumbnail1['path']);
        $thumbnail_src1 = 'data:' . $file->mimetype . ';base64,' . base64_encode($thumbnail1_source);

        $thumbnail2_source = file_get_contents($thumbnail2['path']);
        $thumbnail_src2 = 'data:' . $file->mimetype . ';base64,' . base64_encode($thumbnail2_source);

        return [
            'signature_file_id' => $user->signature->id,
            'thumbnail_src1' => $thumbnail_src1,
            'thumbnail_src2' => $thumbnail_src2
        ];
    }

    /**
     * Creates a proof that can be safely transferred to the client
     * without the risk of being spoofed. This proof then can be used
     * to recreate the signature date on the server when the form is
     * finally submitted.
     *
     * @param int|null $signature_file_id
     * @return string
     */
    public static function getSignatureProof(?int $signature_file_id, DateTime $date_time, int $user_id) : string
    {
        return (new EncryptionDecryptionHelper())->encryptData(serialize([
            "signature_file_id" => $signature_file_id,
            "timestamp" => $date_time->getTimestamp(),
            "user_id" => $user_id,
        ]));
    }

    /**
     * Creates a default signature by rasterising the user's name in text to an image and returning the base64 encoded image as a string
     * @param int $user_id
     * @return string
     */
    public static function generateDefaultSignatureImage(int $user_id) {
        //We need to provide a precision as otherwise we end up with rounding errors
        $precision = 10;
        //Define a scalar which can be used to a small amount of padding around the text
        $text_size_scalar = 0.9;
        //Determine which font we're using
        $font_path = Yii::app()->basePath . '/assets/fonts/Roboto/Roboto-ThinItalic.ttf';

        //Define the size of our signature image and initialise it
        $image_size = ['width' => 900, 'height' => 300];
        $image = imagecreatetruecolor($image_size['width'], $image_size['height']);

        //Define colours and allocate them to the image
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $background_color);

        //Find the user name to be rendered as a signature
        $user = \User::model()->findByPk($user_id);
        $user_name = $user->getFullName();

        //Find the size of text to be rendered
        $raw_text_bounds = imagettfbbox($precision, 0.0, $font_path, $user_name);
        $raw_text_size = [
            'width' => $raw_text_bounds[4] - $raw_text_bounds[1],
            'height' => $raw_text_bounds[1] - $raw_text_bounds[5]
        ];

        //Find the text size for each axis
        $component_text_sizes = [
            'x' => $image_size['width'] / $raw_text_size['width'], 
            'y' => $image_size['height'] / $raw_text_size['height']
        ];

        //Choose the smaller of the two text sizes so we don't draw text out of bounds
        $smallest_text_size = min($component_text_sizes['x'], $component_text_sizes['y']);

        //Assign our final text size
        $final_text_size = $smallest_text_size * $precision * $text_size_scalar;

        //Usig our final text size, determine how large the text will be rendered and compute the coordinates needed to center it in the area
        $resulting_text_bounds = imagettfbbox($final_text_size, 0.0, $font_path, $user_name);
        $resulting_text_size = [
            'width' => $resulting_text_bounds[4] - $resulting_text_bounds[1], 
            'height' => $resulting_text_bounds[1] - $resulting_text_bounds[5]
        ];
        $center_text_position = [
            'x' => $image_size['width'] / 2 - $resulting_text_size['width'] / 2, 
            'y' => ($image_size['height'] + $resulting_text_size['height']) / 2
        ];

        //Rasterise the text to the image
        imagettftext($image, $final_text_size, 0, $center_text_position['x'], $center_text_position['y'], $text_color, $font_path, $user_name);
        
        //Output the image to a buffer to capture the raw data
        ob_start();
        imagejpeg($image);
        $image_string = ob_get_contents();
        imagedestroy($image);
        ob_end_clean();

        //Return the encoded image data
        return base64_encode($image_string);
    }
}