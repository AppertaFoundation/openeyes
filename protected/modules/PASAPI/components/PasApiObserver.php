<?php
namespace OEModule\PASAPI\components;

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class PasApiObserver
{
    /**
     * Objet to parsing XML
     * @var null
     */
    private $_xml_helper = null;

    /**
     * Object to making http requests
     * @var null
     */
    private $_curl = null;

    private $available = false;

    public function __construct()
    {
        $this->isAvailable();

        $this->_curl = new \Curl();
        $this->_xml_helper = new XmlHelper();
    }

    public function search($data)
    {

        if( !$this->isAvailable() ){
            // log ?
            return false;
        }

        $resource_model = "\\OEModule\\PASAPI\\resources\\Patient";

        //will be accessed at the Patient model's search function
        $results = &$data['results'];

        $xml = $this->pasRequest($data);

        //loading the xml
        $this->_xml_helper->xml($xml);

        // validationg the XML
        if (!$this->_xml_helper->isValid()) {
            \OELog::log('PASAPI invalid XML from API request. ' . print_r(array_merge($this->_xml_helper->getErrors(), array(
                    'hos_num' => isset($data['patient']->hos_num) ? $data['patient']->hos_num : '',
                    'nhs_num' => isset($data['patient']->nhs_num) ? $data['patient']->nhs_num : '',
                    'first_name' => isset($data['params']['last_name']) ? $data['params']['last_name'] : $data['params']['last_name'],
                    'last_name' => isset($data['params']['last_name']) ? $data['params']['last_name'] : '',
                )), true) );

            $data['patient']->addPasError('Error occurred during the PAS synchronization, some data may be out of date or incomplete');

            // XML captured in DB : audit.data
        }

        // count the Patient nodes
        $patient_count = $this->_xml_helper->countNodes('Patient');

        if(!$patient_count){
            // empty <PatientList>, nothing to do here
            return true;
        }

        $transaction = \Yii::app()->db->beginTransaction();
        try {

            $xml_handler = $this->_xml_helper->getHandler();

            // move to the first <patient /> node
            while ($xml_handler->read() && $xml_handler->name !== 'Patient');

            // now that we're at the right depth, hop to the next <patient/> until the end of the tree
            while ($xml_handler->name === 'Patient') {
                $node = new \SimpleXMLElement($xml_handler->readOuterXML());

                //$resource is an instance of \OEModule\PASAPI\resources\Patient
                $resource = $resource_model::fromXml('V1', $xml_handler->readOuterXML(), array(
                    //   'update_only' => true,
                ));

                $resource->id = $node->HospitalNumber;

                $_assignment = $resource->getAssignment();
                $_patient = $_assignment ? $_assignment->getInternal() : null;

                //XML contains a list of patients so we are building \Patient objects for those who are not in our DB
                if (!isset($_patient) && $patient_count > 1) {

                    // we do not save this Patient, just display on the patient/view page's list
                    $patient = $this->buildPatientObject($resource);
                    $results[] = $patient;
                }

                // If the patient is in our DB or only 1 patient returned we save it
                if ( ($_patient instanceof \Patient) || $patient_count == 1) {

                    // we could check the $_assignment->isStale() but the request already done, we have the new data, why would we throw away

                    if (!$resource->save() && ($data['patient'] instanceof \Patient)) {
                        $data['patient']->addPasError('Patient not updated from PAS, some data may be out of date or incomplete');
                        \OELog::log('PASAPI Patient resource model could not be saved. Hos num: ' . $node->HospitalNumber . ' ' . print_r($resource->errors, true));
                    }
                }

                $xml_handler->next('Patient');
            }

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log("PASAIP : " . $e->getMessage());
        }

        //we do not return anything here
        //either a Patient was saved or the data will be available in the referenced $results array
    }

    /**
     * Build Patient Object from XML without saving
     * @param \OEModule\PASAPI\resources\Patient $resource
     * @return \Patient
     */
    private function buildPatientObject(\OEModule\PASAPI\resources\Patient $resource)
    {
        $patient = new \Patient();
        $contact = new \Contact();
        $patient->contact = $contact;

        $resource->assignProperty($patient, 'nhs_num', 'NHSNumber');
        $resource->assignProperty($patient, 'hos_num', 'HospitalNumber');
        $resource->assignProperty($patient, 'dob', 'DateOfBirth');
        $resource->assignProperty($patient, 'date_of_death', 'DateOfDeath');
        $resource->assignProperty($patient, 'is_deceased', 'IsDeceased');

        $resource->assignProperty($contact, 'title', 'Title');
        $resource->assignProperty($contact, 'first_name', 'FirstName');
        $resource->assignProperty($contact, 'last_name', 'Surname');
        $resource->assignProperty($contact, 'primary_phone', 'TelephoneNumber');

        return $patient;
    }

    /**
     * Building the query string and making a GET call to the API
     * @param $data
     * @return bool|mixed
     */
    private function pasRequest($data)
    {
        $_patient = $data['patient'];
        $params = $data['params'];
        $xml = false;

        $url = \Yii::app()->params['pasapi']['url'];

        $query = array();

        if($_patient->hos_num){
            $query['hosnum'] = $_patient->hos_num;
        } elseif($_patient->nhs_num){
            $query['nhsnum'] = $_patient->nhs_num;
        } elseif($params['last_name']){
            $query['familyname'] = $params['last_name'];

            if($params['first_name']){
                $query['givenname'] = $params['first_name'];
            }
        }

        $error = '';
        if( !empty($query) ){
            $xml = $this->_curl->get($url . '?' . http_build_query($query));
            $ch = $this->_curl->curl;

            if(curl_errno($ch)){
                $error = 'PASAIP cURL error occurred on API request. Request error: ' . curl_error($ch) . " ";
                \OELog::log($error);
            }
        }

        \Audit::add('PASAPI', 'GET request', $error . (string)$xml);

        return $xml;
    }

    /**
     * Is PAS enabled and up?
     */
    public function isAvailable()
    {
        $enabled = (isset(\Yii::app()->params['pasapi']['enabled']) && \Yii::app()->params['pasapi']['enabled'] === true);
        $available = $enabled && (isset(\Yii::app()->params['pasapi']['url']) && !empty( \Yii::app()->params['pasapi']['url']));

        return $this->available = $available;
    }

}