<?php
namespace OEModule\PASAPI\components;

use OEModule\PASAPI\models\PasApiAssignment;

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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

        // would be nice to have some kind of NO_PROXY here (or somewhere) to exclude localhost
        $default_proxy = isset(\Yii::app()->params['curl_proxy']) ? \Yii::app()->params['curl_proxy'] : false;
        $proxy = isset(\Yii::app()->params['pasapi']['proxy']) ? \Yii::app()->params['pasapi']['proxy'] : $default_proxy;
        $request_timeout = isset(\Yii::app()->params['pasapi']['curl_timeout']) ? \Yii::app()->params['pasapi']['curl_timeout'] : 60;

        curl_setopt($this->_curl->curl, CURLOPT_PROXY, $proxy);
        curl_setopt($this->_curl->curl, CURLOPT_TIMEOUT, $request_timeout);

        $this->_xml_helper = new XmlHelper();
    }

    public function search($data)
    {

        if( !$this->isAvailable() ){
            // log ?
            return false;
        }

        $resource_model = "\\OEModule\\PASAPI\\resources\\Patient";

        //will be accessed at the \Patient model's search function
        $results = &$data['results'];
        $patient = $data['patient'];

        if( !$this->isPASqueryRequired($patient) ){
            // no need to update the record
            return false;
        }

        $xml = $this->pasRequest($data);

        //loading the xml
        $this->_xml_helper->xml($xml);

        // validating the XML
        if (!$this->_xml_helper->isValid()) {
            \OELog::log('PASAPI invalid XML from API request. ' . print_r(array_merge($this->_xml_helper->getErrors(), array(
                    'hos_num' => isset($data['patient']->hos_num) ? $data['patient']->hos_num : '',
                    'nhs_num' => isset($data['patient']->nhs_num) ? $data['patient']->nhs_num : '',
                    'first_name' => isset($data['params']['last_name']) ? $data['params']['last_name'] : $data['params']['last_name'],
                    'last_name' => isset($data['params']['last_name']) ? $data['params']['last_name'] : '',
                )), true) );

            $data['patient']->addPasError('Error occurred during the PAS synchronization, some data may be out of date or incomplete');
            \Yii::app()->user->setFlash('warning.pas_unavailable', 'PAS is currently unavailable, some data may be out of date or incomplete');

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
                       //'update_only' => true,
                ));

                $resource->id = $node->HospitalNumber;

                $_assignment = $resource->getAssignment();
                $_patient = $_assignment->getInternal();

                // If the patient is in our DB or only 1 patient returned we save it
                if ( !$_patient->isNewRecord || $patient_count == 1) {

                    // we could check the $_assignment->isStale() but the request already done, we have the new data, why would we throw it away

                    if (!$resource->save() && ($data['patient'] instanceof \Patient)) {
                        $data['patient']->addPasError('Patient not updated from PAS, some data may be out of date or incomplete');
                        \OELog::log('PASAPI Patient resource model could not be saved. Hos num: ' . $node->HospitalNumber . ' ' . print_r($resource->errors, true));
                    }
                } else {
                    // we do not save this Patient, just display on the patient/view page's list
                    $patient = $this->buildPatientObject($resource);
                    $results[] = $patient;
                }

                $xml_handler->next('Patient');
            }

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log("PASAPI : " . $e->getMessage());
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
                $error = 'PASAPI cURL error occurred on API request. Request error: ' . curl_error($ch) . " ";
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


    /**
     * Checks if we have to query the PAS or not
     * If hos_num or nhs_num was searched we check the patient if his/her record is stale
     *
     * @param \Patient $patient
     * @return bool
     */
    public function isPASqueryRequired(\Patient $patient)
    {
        if( !empty($patient->hos_num) || !empty($patient->nhs_num)){

            // validate the hos_num and hns_num
            $patient_search = new \PatientSearch();
            $hos_num = $patient_search->getHospitalNumber($patient->hos_num);
            $nhs_num = $patient_search->getNHSnumber($patient->nhs_num);

            //get the patient
            $patient_criteria = new \CDbCriteria();
            $patient_criteria->addCondition('hos_num =:hos_num', 'OR');
            $patient_criteria->addCondition('nhs_num =:nhs_num', 'OR');
            $patient_criteria->params[':nhs_num'] = $nhs_num;
            $patient_criteria->params[':hos_num'] = $hos_num;

            $_patient = \Patient::model()->find($patient_criteria);

            if( $_patient ){
                // get the assignment
                $criteria = new \CDbCriteria();
                $criteria->addCondition('resource_type ="Patient"');
                $criteria->addCondition('internal_id =:id');
                $criteria->params[':id'] = $_patient->id;

                $assignment = PasApiAssignment::model()->find($criteria);

                if($assignment && !$assignment->isStale()){
                    return false;
                }
            }
        }

        return true;
    }

}
