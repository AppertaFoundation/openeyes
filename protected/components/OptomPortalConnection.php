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
 * Date: 14/08/2016
 * Time: 22:58
 */

//use Zend;
require_once 'Zend/Http/Client.php';

class OptomPortalConnection
{
    private $yii;

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
     * @var Zend_Http_Client
     */
    protected $client;
    protected $config = array();

    /**
     * @return Zend_Http_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * OptomPortalConnection constructor.
     * 
     * @param CApplication $yii - for dependency injection/testing
     */
    public function __construct($yii = null)
    {
        if ($yii === null) {
            $this->yii = Yii::app();
        }

        $this->setConfig();
        $this->initClient();
        $this->login();
    }

    /**
     * Set portal config.
     *
     * @throws InvalidArgumentException
     */
    protected function setConfig()
    {
        $config = $this->yii->params['portal'];
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
     * Init HTTP client.
     *
     * @throws Zend_Http_Client_Exception
     */
    protected function initClient()
    {
        $clientConfig = array();
        if($this->yii->params['curl_proxy']){
            $proxy = parse_url($this->yii->params['curl_proxy']);
            $clientConfig['adapter'] = 'Zend_Http_Client_Adapter_Proxy';
            $clientConfig['proxy_host'] = $proxy['host'];
            if(array_key_exists('port', $proxy)){
                $clientConfig['proxy_port'] = $proxy['port'];
            }
        }

        $client = new Zend_Http_Client($this->config['uri'], $clientConfig);
        $client->setHeaders('Accept', 'application/vnd.OpenEyesPortal.v1+json');

        // Revert to pre-PHP5.6 defaults for peer verification, for sites behind a proxy.
        $streamOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                )
            );

        $adapter = $client->getAdapter();
        $adapter->setStreamContext($streamOptions);

        $this->client = $client;
    }

    /**
     * Login to the API, set the auth header.
     * @throws Zend_Http_Client_Exception
     * @throws Exception
     */
    protected function login()
    {
        $this->client->setUri($this->config['uri'] . $this->config['endpoints']['auth']);
        $this->client->setParameterPost($this->config['credentials']);
        $response = $this->client->request('POST');
        if ($response->getStatus() > 299) {
            throw new Exception('Unable to login, user credentials in config incorrect');
        }
        $jsonResponse = json_decode($response->getBody(), true);
        $this->client->resetParameters();
        $this->client->setHeaders('Authorization', 'Bearer ' . $jsonResponse['access_token']);
    }

    /**
     * Search the API for signatures.
     *
     * @return mixed
     * @throws Zend_Http_Client_Exception
     */
    public function signatureSearch($start_date = null, $uniqueId = null)
    {
        if ($uniqueId && $this->client) {
            $this->client->setUri($this->config['uri'] . str_replace('searches', $uniqueId,
                    $this->config['endpoints']['signatures']));
            $method = 'GET';
            // just to make sure that start date is not specified
            $start_date = null;
        } else {
            if ($this->client) {
                $this->client->setUri($this->config['uri'] . $this->config['endpoints']['signatures']);
                $method = 'POST';
            }
        }

        if ($start_date && $this->client) {
            $this->client->setParameterPost(array('start_date' => $start_date));
        }

        if ($this->client) {
            $response = $this->client->request($method);
            return json_decode($response->getBody(), true);
        }
    }

    /**
     * Creates a new ProtectedFile for the new signature image
     *
     * @param $imageData
     * @return ProtectedFile
     */
    public function createNewSignatureImage($imageData, $fileId)
    {
        $protected_file = new \ProtectedFile();
        $protected_file = $protected_file->createForWriting('cvi_signature_' . $fileId);

        if (file_put_contents($protected_file->getPath(), $imageData)) {
            $protected_file->save();
            return $protected_file;
        }
    }
}
