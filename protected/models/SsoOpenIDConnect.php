<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class SsoOpenIDConnect extends \Jumbojett\OpenIDConnectClient
{
    private $encryptPassword;

    public function __construct($provider_url = null, $client_id = null, $client_secret = null, $issuer = null, $encryptPassword = '')
    {
        $this->encryptPassword = $encryptPassword;
        parent::__construct($provider_url, $client_id, $client_secret, $issuer);
    }

    protected function getSessionKey($key)
    {
        return $_COOKIE[$key];
    }

    protected function setSessionKey($key, $value)
    {
        // Cookie expires in 10 minutes
        // HTTPOnly flag is also set as JS access is not required
        $options = [
            'expires' => time() + 600,
            'path' => '/',
            'httponly' => true
        ];

        setcookie($key, $value, $options);
    }

    protected function unsetSessionKey($key)
    {
        unset($_COOKIE[$key]);
        setcookie($key, null, -1, '/');
    }

    protected function setState($state)
    {
        $this->setSessionKey('openid_connect_state', $this->encryptCookie($state));
        return $state;
    }

    protected function getState()
    {
        return $this->decryptCookie($this->getSessionKey('openid_connect_state'));
    }

    protected function setNonce($nonce)
    {
        $this->setSessionKey('openid_connect_nonce', $this->encryptCookie($nonce));
        return $nonce;
    }

    protected function getNonce()
    {
        return $this->decryptCookie($this->getSessionKey('openid_connect_nonce'));
    }

    protected function encryptCookie($value)
    {
        return openssl_encrypt($value, "AES-128-ECB", $this->encryptPassword);
    }

    protected function decryptCookie($value)
    {
        return openssl_decrypt($value, "AES-128-ECB", $this->encryptPassword);
    }
}
