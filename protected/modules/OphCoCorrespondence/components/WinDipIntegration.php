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


namespace OEModule\OphCoCorrespondence\components;

/**
 * Class WinDipIntegration
 *
 * Integration component for WinDip referral - provides link data to WinDip based on module
 * configuration.
 *
 * In current implementation, is only example of 3rd party; aspects should be abstracted as and when
 * further integrations are created.
 *
 * @package OEModule\OphCoCorrespondence\components
 */
class WinDipIntegration extends \CApplicationComponent implements ExternalIntegration
{
    protected $yii;

    // At this stage (only WIF xml generation implemented) these params are not required
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

    protected $template_path = 'application.modules.OphCoCorrespondence.views.windipintegration';

    /**
     * Template path for the WinDip request
     *
     * @var string
     */

    //@TODO: fix the following paths as the filename and the path is separated now
    protected $request_template = 'OphCoCorrespondence.views.windipintegration.request_xml';
    protected $new_event_template = 'OphCoCorrespondence.views.windipintegration.popup_newreferral';
    protected $wif_xml_template = 'wif_xml';

    public function getTemplatePath()
    {
        return $this->template_path;
    }

    public function getWIFxmlTemplate()
    {
        return $this->wif_xml_template;
    }

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

        //TODO: set this validation back if more WinDip services will be integrated
//        foreach (static::$required_params as $p) {
//            if (!isset($this->$p) || $this->$p === null) {
//                throw new \Exception("Missing required parameter {$p}");
//            }
//        }
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
     *
     *      This function generates data for other WinDip services we did not implemented yet.
     *
     *      Keep in mind when refactor: commands/InternalReferralCommand using this (constructRequestData) method to get the data
     *
     * Build a request for the given event
     *
     * @param \Event $event
     * @param \DateTime $when
     * @param $message_id
     * @return array
     */
    /*protected function constructRequestData(\Event $event, \DateTime $when, $message_id)
    {

        $is_new_event = \Yii::app()->user->getState("new_referral", false);

        //TODO: better way of handling mysql date to datetime
        $event_date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->event_date);

        $indexes = array();
        $indexes[] = array(
            'id' => 'hosnum',
            'value' => $event->episode->patient->hos_num
        );

        if ( isset(\Yii::app()->user->id) ){
            $user = \User::model()->findByPk(\Yii::app()->user->id);
        }


        return array(
            'timestamp' => $when->format('Y-m-d H:i:s'),
            'message_id' => $message_id,
            'application_id' => $this->application_id,
            'username' => isset($user) ? $user->username : '',
            'user_displayname' => isset($user) ? $user->getReversedFullName() : '',
            'event_id' => $event->id,
            'windip_type_id' => !$is_new_event ? '' : $this->form_id,
            'event_date' => $event_date->format('Y-m-d'),
            'event_time' => $event_date->format('H:i:s'),
            'additional_indexes' => !$is_new_event ? array() : $indexes,
            'is_new_event' => $is_new_event
        );
    }*/

    /**
     * Generates data for WinDip XML
     *
     * @param \Event $event
     * @param null $file_with_path
     * @return array
     */
    public function constructRequestData(\Event $event, $file_with_path = null)
    {

        $letter = \ElementLetter::model()->findByAttributes(array('event_id' => $event->id));

        $primary_identifier_value = \PatientIdentifierHelper::getIdentifierValue(\PatientIdentifierHelper::getIdentifierForPatient(
            \Yii::app()->params['display_primary_number_usage_code'],
            $event->episode->patient->id,
            $event->institution_id, $event->site_id
        ));

        $indexes = array(
            array('id' => 'hos_num', 'value' => $primary_identifier_value),
            array('id' => 'date_od_birth', 'value' => $event->episode->patient->dob),
            array('id' => 'event_id', 'value' => $event->id),
            array('id' => 'event_date', 'value' => $event->event_date),
            array('id' => 'generated_date', 'value' => date('Y-m-d H:i:s')),

            array('id' => 'service_to', 'value' => ( isset($letter->toSubspecialty) ? $letter->toSubspecialty->ref_spec : '' ) ),
            array('id' => 'consultant_to', 'value' => ( isset($letter->event->episode->firm) ? $letter->event->episode->firm->pas_code : '' ) ),
            array('id' => 'is_same_condition', 'value' => ( $letter->is_same_condition ? 'True' : 'False' ) ),
            array('id' => 'is_urgent', 'value' => $letter->is_urgent ? 'True' : 'False'),
        );

        return array(
            'event_id' => $event->id,
            'windip_type_id' => '', // $this->form_id, // not sure
            'keep_prior_documents' => 'True',
            'workflow_importance' => (int)$letter->is_urgent,
            'file_path' => $file_with_path,
            'additional_indexes' => $indexes,
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
    public function generateDocumentListRequest($patient)
    {
        $this->request_template = 'OphCoCorrespondence.views.windipintegration.document_list_xml';
        $user = \User::model()->findByPk(\Yii::app()->user->id);

        $primary_identifier_value = \PatientIdentifierHelper::getIdentifierValue(\PatientIdentifierHelper::getIdentifierForPatient(
            \Yii::app()->params['display_primary_number_usage_code'],
            $patient->id,
            \Institution::model()->getCurrent()->id,
            \Yii::app()->session['selected_site_id']
        ));

        $when = new \DateTime();
        $data['username'] = \Yii::app()->session['user_auth']->username;
        $data['user_displayname'] = $user->getReversedFullName();
        $data['timestamp'] = $when->format('Y-m-d H:i:s');
        $data['message_id'] = $this->getMessageId(new \Event());
        $data['application_id'] = $this->application_id;
        $data['event_id'] = '';
        $data['hos_num'] = $primary_identifier_value;
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
    public function generateUrlForDocumentList($patient)
    {
        $xml = $this->generateDocumentListRequest($patient);
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
            ));
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
