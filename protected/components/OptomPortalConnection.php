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

class OptomPortalConnection
{
    private $proxy = null;
    private $header = ["Accept: application/vnd.OpenEyesPortal.v1+json"];
    protected $config = [];

    /**
     * For validating the configuration keys
     * @var array
     */
    protected static $required_config_keys = array(
        'uri',
        'endpoints.auth',
        'endpoints.signatures',
        'credentials.username',
        'credentials.password',
        'credentials.grant_type',
        'credentials.client_id',
        'credentials.client_secret'
    );

    /**
     * OptomPortalConnection constructor.
     */
    public function __construct()
    {
        $this->proxy = SettingMetadata::model()->getSetting('curl_proxy');

        $this->setConfig();
        $this->getPortalAccessToken();
    }

    /**
     * Set portal config.
     *
     * @throws InvalidArgumentException
     */
    private function setConfig()
    {
        $config = SettingMetadata::model()->getSetting('portal');
        if (!$config) {
            throw new InvalidArgumentException('Missing portal configuration for ' . __CLASS__);
        }

        foreach (static::$required_config_keys as $k) {
            if (Helper::elementFinder($k, $config) === null) {
                throw new InvalidArgumentException('Missing required config parameter for ' . __CLASS__);
            }
        }

        $this->config = $config;
    }

    /**
     * @param string $url
     * @param array|NULL $params
     * @param bool $use_post
     * @return false|resource
     */
    private function setCurl(string $url, ?array $params = null, bool $use_post = true)
    {
        $ch = curl_init($url);

        if (!empty($this->proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_POST, $use_post);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);

        return $ch;
    }

    private function executeCurl($curl, $error_message)
    {
        $curl_result = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new Exception("$error_message Curl error: " . curl_error($curl));
        }
        return $curl_result;
    }

    /**
     * Get the access token from the API and set the auth header.
     * @throws Exception
     */
    private function getPortalAccessToken()
    {
        $curl = $this->setCurl($this->config["uri"] . $this->config["endpoints"]["auth"], $this->config["credentials"]);
        $curl_result = $this->executeCurl($curl, "Unable to get access token.");
        $json_response = json_decode($curl_result, true);
        curl_close($curl);

        if (!is_array($json_response) || !array_key_exists('access_token', $json_response)) {
            throw new \Exception('The server did not send a valid JSON reply.');
        }

        $this->header[] = "Authorization: Bearer " . $json_response['access_token'];
    }

    public function getExaminations(array $params)
    {
        $curl = $this->setCurl($this->config["uri"] . $this->config['endpoints']['examinations'], $params);
        $curl_result = $this->executeCurl($curl, "Unable to retrieve examinations.");
        $json_response = json_decode($curl_result, true);
        curl_close($curl);

        return $json_response;
    }

    /**
     * Search the API for signatures.
     *
     * @return mixed
     * @throws Exception
     */
    public function signatureSearch($start_date = null, $uniqueId = null)
    {
        if ($uniqueId) {
            $url = $this->config['uri'] . str_replace('searches', $uniqueId, $this->config['endpoints']['signatures']);
            $use_post = false;
            // just to make sure that start date is not specified
            $start_date = null;
        } else {
            $url = $this->config['uri'] . $this->config['endpoints']['signatures'];
            $use_post = true;
        }

        $params = null;
        if ($start_date) {
            $params = ['start_date' => $start_date];
        }

        $curl = $this->setCurl($url, $params, $use_post);

        $curl_result = $this->executeCurl($curl, "Unable to retrieve signatures.");

         return json_decode($curl_result, true);
    }
}
