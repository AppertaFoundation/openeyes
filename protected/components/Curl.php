<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Curl
{
    public function __construct()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_PROXY, Yii::app()->params['curl_proxy']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3');
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 60);
    }

    public function get($url, $referer = false)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, false);
        if ($referer) {
            curl_setopt($this->curl, CURLOPT_REFERER, $referer);
        } else {
            curl_setopt($this->curl, CURLOPT_REFERER, null);
        }

        return curl_exec($this->curl);
    }

    public function post($url, $post, $referer = false)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        if ($referer) {
            curl_setopt($this->curl, CURLOPT_REFERER, $referer);
        } else {
            curl_setopt($this->curl, CURLOPT_REFERER, null);
        }
        if (is_string($post)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
        } else {
            $postfields = '';
            foreach ($post as $key => $value) {
                if ($postfields) {
                    $postfields .= '&';
                }
                $postfields .= "$key=".rawurlencode($value);
            }
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postfields);
        }

        return curl_exec($this->curl);
    }
}
