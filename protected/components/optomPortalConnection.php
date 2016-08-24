<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 14/08/2016
 * Time: 22:58
 */

//use Zend;
require_once 'Zend/Http/Client.php';

class optomPortalConnection
{

    protected $client;

    protected $config = array();

    public function __construct()
    {
        if($this->setConfig())
        {
            $this->initClient();
            $this->login();
        }
    }

    /**
     * Set portal config.
     */
    protected function setConfig()
    {
        if(Yii::app()->params['portal'])
        {
            $this->config = Yii::app()->params['portal'];
            return true;
        }else
        {
            return false;
        }
    }


    /**
     * Init HTTP client.
     *
     * @return Zend_Http_Client
     *
     * @throws Zend_Http_Client_Exception
     */
    protected function initClient()
    {
        $client = new Zend_Http_Client($this->config['uri']);
        $client->setHeaders('Accept', 'application/vnd.OpenEyesPortal.v1+json');

        $this->client = $client;
    }

    /**
     * Login to the API, set the auth header.
     */
    protected function login()
    {
        $this->client->setUri($this->config['uri'].$this->config['endpoints']['auth']);
        $this->client->setParameterPost($this->config['credentials']);
        $response = $this->client->request('POST');
        if ($response->getStatus() > 299) {
            throw new Exception('Unable to login, user credentials in config incorrect');
        }
        $jsonResponse = json_decode($response->getBody(), true);
        $this->client->resetParameters();
        $this->client->setHeaders('Authorization', 'Bearer '.$jsonResponse['access_token']);
    }

    /**
     * Search the API for signatures.
     *
     * @return mixed
     */
    public function signatureSearch( $startDate = null, $uniqueId = null)
    {
        if($uniqueId && $this->client){
            $this->client->setUri($this->config['uri'] . str_replace('searches',$uniqueId, $this->config['endpoints']['signatures']));
            $method = 'GET';
            // just to make sure that start date is not specified
            $startDate = null;
        }else if ($this->client)
        {
            $this->client->setUri($this->config['uri'] . $this->config['endpoints']['signatures']);
            $method = 'POST';
        }

        if($startDate && $this->client) {
            $this->client->setParameterPost(array('start_date' => $startDate));
        }

        if($this->client) {
            $response = $this->client->request($method);
            return json_decode($response->getBody(), true);
        }
    }

    /**
     * Creates a new ProtectedFile for the new signature image
     *
     * @param $imageData
     */
    public function createNewSignatureImage($imageData, $fileId)
    {
        $pFile = new \ProtectedFile();
        $pFile = $pFile->createForWriting("cvi_signature_".$fileId);

        if(file_put_contents($pFile->getPath(), $imageData))
        {
            $pFile->save();
            return $pFile;
        }
        return false;
    }
}