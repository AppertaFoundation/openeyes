<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 14/08/2016
 * Time: 22:58
 */

namespace OEModule\OphCoCvi\components;

//use Zend;
require_once 'Zend/Http/Client.php';

class optomPortalConnection
{

    protected $client;

    protected $config = array();

    public function __construct()
    {
        $this->setConfig();
        $this->initClient();
        $this->login();
    }

    /**
     * Set portal config.
     */
    protected function setConfig()
    {
        $this->config = \Yii::app()->params['portal'];
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
        $client = new \Zend_Http_Client($this->config['uri']);
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
    public function signatureSearch()
    {
        $this->client->setUri($this->config['uri'].$this->config['endpoints']['signatures']);
//      $this->client->setParameterPost(array('start_date' => $lastExam->updated_at));
        $response = $this->client->request('POST');

        return json_decode($response->getBody(), true);
    }
}