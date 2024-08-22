<?php
/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\concerns\InteractsWithApp;

class DocmanRetriever extends CApplicationComponent
{
    use InteractsWithApp;

    private array $query_params = [];

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function addQueryParam($key, $value): void
    {
        $this->query_params[$key] = $value;
    }

    public function contentForEvent($event, bool $print_only_gp = false, $document_target_id = null): string
    {
        $login_page = $this->getApp()->params['docman_login_url'];
        $username = $this->getApp()->params['docman_user'];
        $password = $this->getApp()->params['docman_password'];
        $print_url = $this->getApp()->params['docman_print_url'];
        $inject_autoprint_js = $this->getApp()->params['docman_inject_autoprint_js'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $login_page);
        // disable SSL certificate check for locally issued certificates
        if (Yii::app()->params['disable_ssl_certificate_check']) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "institution_id={$event->institution_id};site_id={$event->site_id}");
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            die(curl_error($ch));
        }

        preg_match("/YII_CSRF_TOKEN = '(.*)';/", $response, $token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        $params = [
            'LoginForm[username]' => $username,
            'LoginForm[password]' => $password,
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        curl_exec($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);

        $url = $print_url . $event->id;
        $url .= $document_target_id
            ? "?document_target_id=" . $document_target_id
            : "?auto_print=" . (int)$inject_autoprint_js
            . "&print_only_gp=" . $print_only_gp;

        if ($this->query_params) {
            $url .= "&" . http_build_query($this->query_params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $content = curl_exec($ch);

        curl_close($ch);

        return $content;
    }
}
