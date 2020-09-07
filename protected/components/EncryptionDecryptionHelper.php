<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
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

class EncryptionDecryptionHelper
{
    public function encryptData($plainText) {
        try {
            // Decrypt the data first, to check if the data is already encrypted or not.
            $this->decryptData($plainText);
            return $plainText;
        } catch (Exception $e) {
            try {
                if ( file_exists(Yii::app()->params['sodium_crypto_key_path']) ) {
                    $key = sodium_hex2bin(rtrim(file_get_contents(Yii::app()->params['sodium_crypto_key_path'])));
                    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
                    $ciphertext = sodium_crypto_secretbox($plainText, $nonce, $key);

                    // cleanup
                    sodium_memzero($plainText);
                    sodium_memzero($key);

                    $encoded = base64_encode($nonce . $ciphertext);
                    return $encoded;
                }
            } catch (Exception $e) {
                throw new \Exception($e);
            }
        }
    }

    public function decryptData($text) {
        try {
            if (file_exists(Yii::app()->params['sodium_crypto_key_path'])) {
                $key = sodium_hex2bin(rtrim(file_get_contents(Yii::app()->params['sodium_crypto_key_path'])));
                $decoded = base64_decode($text);

                // check for general failures
                if ($decoded === false) {
                    throw new \Exception('The encoding failed');
                }

                $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
                $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
                $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);

                // cleanup
                sodium_memzero($ciphertext);
                sodium_memzero($key);

                return $plaintext;
            }
        } catch (Exception $e) {
            throw new \Exception($e);
        }
    }
}