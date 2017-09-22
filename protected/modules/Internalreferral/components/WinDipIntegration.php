<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\Internalreferral\components;

/**
 * Class WinDipIntegration
 *
 * Integration component for WinDip referral - provides link data to WinDip based on module
 * configuration.
 *
 * In current implementation, is only example of 3rd party; aspects should be abstracted as and when
 * further integrations are created.
 *
 * @package OEModule\Internalreferral\components
 */
class WinDipIntegration extends \CApplicationComponent implements ExternalIntegration
{
    protected $yii;
    /*protected static $required_params = array(
        'launch_uri',
        'application_id',
        'hashing_function',
        'form_id'
    );*/

    public $launch_uri;
    public $application_id;
    public $hashing_function;
    public $form_id;

    /**
     * Template path for the WinDip request
     *
     * @var string
     */
    protected $request_template = 'Internalreferral.views.windipintegration.request_xml';
    protected $new_event_template = 'Internalreferral.views.windipintegration.popup_newreferral';

    /**
     * WinDipIntegration constructor.
     *
     * @param \CApplication|null $yii
     * @param array $params
     * @throws \Exception
     */
    public function init()
    {
        if ($this->yii === null) {
            $this->yii = \Yii::app();
        }
        
        foreach (static::$required_params as $p) {
            if (!isset($this->$p) || $this->$p === null) {
                throw new \Exception("Missing required parameter {$p}");
            }
        }
    }

    /**
     * Convenience wrapper to allow template rendering.
     *
     * @param $view
     * @param array $parameters
     * @return mixed
     */
    public function renderPartial($view, $parameters = array())
    {
        return $this->yii->controller->renderPartial($view, $parameters, true);
    }

    /**
     * Build a request for the given event
     *
     * @param \Event $event
     * @param \DateTime $when
     * @param $message_id
     * @return array
     */
    protected function constructRequestData(\Event $event, \DateTime $when, $message_id)
    {
        
        $is_new_event = \Yii::app()->user->getState("new_referral", false);
        
        //TODO: better way of handling mysql date to datetime
        $event_date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->event_date);

        $indexes = array();
        $indexes[] = array(
            'id' => 'hosnum',
            'value' => $event->episode->patient->hos_num
        );

        $user = \User::model()->findByPk(\Yii::app()->user->id);

        return array(
            'timestamp' => $when->format('Y-m-d H:i:s'),
            'message_id' => $message_id,
            'application_id' => $this->application_id,
            'username' => $user->username,
            'user_displayname' => $user->getReversedFullName(),
            'event_id' => $event->id,
            'windip_type_id' => !$is_new_event ? '' : $this->form_id,
            'event_date' => $event_date->format('Y-m-d'),
            'event_time' => $event_date->format('H:i:s'),
            'additional_indexes' => !$is_new_event ? array() : $indexes,
            'is_new_event' => $is_new_event
        );
    }

    /**
     * Generate a unique ID for the event message to be sent to WinDip
     *
     * @TODO: determine if ID should be stored with the event and maintained for subsequent links
     *
     * @param \Event $event
     * @return string
     */
    protected function getMessageId(\Event $event)
    {
        return \Helper::generateUuid();
    }

    /**
     * Generate the authentication hash for the WinDip request.
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    private function generateAuthenticationHash($data)
    {
        if (!is_callable($this->hashing_function)) {
            throw new \Exception('A hashing function must be provided to generate the authentication hash for the WinDip integration.');
        }

        return call_user_func($this->hashing_function, $this, $data, $this->request_template);
    }

    /**
     *
     * @param \Event $event
     * @return mixed
     * @throws \Exception
     */
    public function generateXmlRequest(\Event $event)
    {
        $when = new \DateTime();
        $message_id = $this->getMessageId($event);

        $data = $this->constructRequestData($event, $when, $message_id);

        $data['authentication_hash'] = $this->generateAuthenticationHash($data);

        $request = $this->renderPartial($this->request_template, $data);
        return $this->cleanRequest($request);
    }
    
    /**
     * Generate a request XML for document list
     * @return type
     */
    public function generateDocumentListRequest()
    {
        //$this->request_template = 'Internalreferral.views.windipintegration.document_list_xml_test';
        $user = \User::model()->findByPk(\Yii::app()->user->id);
        
        $when = new \DateTime();
        $data['username'] = $user->username;
        $data['user_displayname'] = $user->getReversedFullName();
        $data['timestamp'] = $when->format('Y-m-d H:i:s');
        $data['message_id'] = $this->getMessageId(new \Event());
        $data['application_id'] = $this->application_id;
        $data['event_id'] = '';
        $data['event_date'] = $when->format('Y-m-d');
        $data['event_time'] = $when->format('H:i:s');
        $data['is_new_event'] = false;
        $data['additional_indexes'] = array();//array(array('id' => 'HosNum','value' => 0123456));
        
        $data['authentication_hash'] = $this->generateAuthenticationHash($data);

        $request = $this->renderPartial($this->request_template, $data);
        return $this->cleanRequest($request);
    }

    /**
     * Generate the external application URL for a WinDip referral event.
     *
     * @param \Event $event
     * @return string
     * @throws \Exception
     */
    public function generateUrlForEvent(\Event $event)
    {
        $xml = $this->generateXmlRequest($event);
        return $this->launch_uri . '?XML=' . urlencode($xml);
    }
    
    /**
     * Generate the external application URL for document list
     * @return string
     */
    public function generateUrlForDocumentList()
    {
        $xml = $this->generateDocumentListRequest();
        return $this->launch_uri . '?XML=' . urlencode($xml);
    }

    /**
     * parsing function that ensure consistent clean up of request XML
     *
     * @param $request
     * @return string
     */
    public function cleanRequest($request)
    {
        $request = preg_replace('/>\s+</', '><', $request);
        $request = preg_replace('/[\n\r]/', '', $request);

        return trim($request);
    }

    /**
     * Generates the HTML/JS to be inserted into the view page of the referral event.
     *
     * @param \Event $event
     * @return string
     */
    public function renderEventView(\Event $event)
    {
        return $this->renderPartial($this->new_event_template, array(
                'external_link' => $this->generateUrlForEvent($event),
                'event' => $event,
                'is_new_referral' => \Yii::app()->user->getState("new_referral", false),
            )
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function processExternalResponse($data = array())
    {
        return array(200, 'OK');
    }
}
