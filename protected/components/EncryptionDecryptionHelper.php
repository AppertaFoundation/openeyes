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
    /**
     * Encrypts a plain text using the cryptography key that's stored in the
     * location specified by application parameter "sodium_crypto_key_path".
     *
     * @param string $plainText
     * @return string The encrypted text
     * @throws SodiumException|Exception
     */
    public function encryptData(string $plainText) : string
    {
        $key = $this->getCryptoKey();
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($plainText, $nonce, $key);

        // cleanup
        sodium_memzero($plainText);
        sodium_memzero($key);

        return base64_encode($nonce . $ciphertext);
    }

    /**
     * Decrypts an encrypted text using the cryptography key that's stored in the
     * location specified by application parameter "sodium_crypto_key_path".
     *
     * @param string $text
     * @return string The decrypted text
     * @throws SodiumException|Exception
     */
    public function decryptData(string $text) : string
    {
        $key = $this->getCryptoKey();
        $decoded = base64_decode($text, true);
        if ($decoded === false) {
            throw new Exception('Failed to decode encrypted text');
        }
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);

        // cleanup
        sodium_memzero($ciphertext);
        sodium_memzero($key);

        return $plaintext;
    }

    protected function getCryptoKey() : string
    {
        $key_path = Yii::app()->params['sodium_crypto_key_path'];
        if(is_null($key_path)) {
            throw new Exception("Application parameter 'sodium_crypto_key_path' must be set");
        }
        if(!file_exists($key_path) || !is_file($key_path)) {
            throw new Exception("Key file not found in $key_path");
        }

        return sodium_hex2bin(rtrim(file_get_contents($key_path)));
    }
}