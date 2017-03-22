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


//$this->insert('authitemchild', array('parent' => 'TaskEditDnaSample', 'child' => 'OprnViewDnaSample'));

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
    public $form_id = 11111;

    /**
     * Template path for the WinDip request
     *
     * @var string
     */
    protected $request_template = 'OphCoCorrespondence.views.windipintegration.request_xml';

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
        
        /*foreach (static::$required_params as $p) {
            if (!isset($this->$p) || $this->$p === null) {
                throw new \Exception("Missing required parameter {$p}");
            }
        }*/
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
        $path = \Yii::getPathOfAlias('application.modules.OphCoCorrespondence.views.windipintegration').'/request_xml.php';
        if(!file_exists($path)){
            throw new \Exception('Template '.$path.' does not exist.');
        }
        return $this->renderFile($path, $parameters, true);


       // return $controller->renderPartial($view, $parameters, true);
    }

    /**
     * Build a request for the given event
     *
     * @param \Event $event
     * @param \DateTime $when
     * @param $message_id
     * @return array
     */
    public function constructRequestData(\Event $event)
    {
        
        //TODO: better way of handling mysql date to datetime
        $event_date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->event_date);

        $indexes = array();
        $indexes[] = array(
            'id' => 'hosnum',
            'value' => $event->episode->patient->hos_num
        );

        $letter = \ElementLetter::model()->findByAttributes(array('event_id' => $event->id));

        $patient = $event->episode->patient;

        return array(
            'hos_num' => $patient->hos_num,
            'nhs_num' => $patient->nhs_num,
            'full_name' => $patient->fullName,
            'last_name' => $patient->last_name,
            'first_name' => $patient->first_name,
            'second_first_name' => '',  //<SecondForename>
            'title' => $patient->title,
            'date_of_birth' => $patient->dob,
            'gender' => $patient->gender,
            'address' => implode(', ', $patient->getLetterAddress()),
            'address_name' => '',   //<AddressName>
            'address_number' => '', //<AddressNumber>
            'address_street' => isset($patient->contact->address) ? $patient->contact->address->address1 : '',
            'address_district' => '',   //<AddressDistrict>
            'address_town' => isset($patient->contact->address) ? $patient->contact->address->address2 : '',
            'address_county' => isset($patient->contact->address) ? $patient->contact->address->county : '',
            'address_postcode' => isset($patient->contact->address) ? $patient->contact->address->postcode : '',

            'gp_nat_id' => isset($patient->gp) ? $patient->gp->nat_id : '',
            'gp_name' => isset($patient->gp) ? $patient->gp->fullName : '',
            'surgery_code' => isset($patient->practice) ? $patient->practice->code : '',
            'surgery_name' => '', //<SurgeryName>
            'letter_type' => isset($letter) ? $letter->letterType->name : '',
            'activity_id' => $event->id,
            'activity_date' => $event_date->format('Y-m-d'),

            'clinician_type' => '',
            'clinician' => '',

            'clinician_name' => $letter->user->fullName,

            'specialty_red_spec' => isset($letter->toSubspecialty) ? $letter->toSubspecialty->ref_spec : '',
            'specialty_name' => isset($letter->toSubspecialty) ? $letter->toSubspecialty->name : '',

            'location' => '',   //<Location>
            'location_name' => '',   //<LocationName>
            'sub_location' => '',   //<SubLocation>
            'sub_location_name' => '',   //<SubLocationName>

            'service_to' => $letter->toSubspecialty->ref_spec,
            'consultant_to' => $letter->event->episode->firm->pas_code,

            'is_urgent' => $letter->is_urgent ? 'True' : 'False',
            'is_same_condition' => $letter->is_same_condition ? 'True' : 'False',



           /* 'timestamp' => $when->format('Y-m-d H:i:s'),
            'message_id' => $message_id,
            'application_id' => $this->application_id,
            'username' => $user->username,
            'user_displayname' => $user->getReversedFullName(),
            'event_id' => $event->id,
          //  'windip_type_id' => !$is_new_event ? '' : $this->form_id,
            'event_date' => $event_date->format('Y-m-d'),
            'event_time' => $event_date->format('H:i:s'),
            'additional_indexes' => !$is_new_event ? array() : $indexes,
            'is_new_event' => $is_new_event*/
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

        $data = $this->constructRequestData($event);

        $request = $this->renderPartial($this->request_template, $data);
        return $this->cleanRequest($request);
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

}
