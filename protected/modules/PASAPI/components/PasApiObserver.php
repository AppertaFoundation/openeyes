<?php

namespace OEModule\PASAPI\components;

use Audit;
use CDbCriteria;
use Contact;
use Curl;
use Exception;
use Institution;
use OELog;
use OEModule\PASAPI\models\PasApiAssignment;
use Patient;
use PatientSearch;
use SimpleXMLElement;
use Yii;

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
    const SEARCH_PARAMS = ['hos_num', 'nhs_num', 'first_name', 'last_name', 'maiden_name', 'dob'];
    /**
     * Objet to parsing XML
     * @var null
     */
    private $_xml_helper;

    /**
     * Object to making http requests
     * @var null
     */
    private $_curl;

    public function __construct()
    {
        $this->isAvailable();

        $this->_curl = new Curl();

        // would be nice to have some kind of NO_PROXY here (or somewhere) to exclude localhost
        $default_proxy = isset(Yii::app()->params['curl_proxy']) ? Yii::app()->params['curl_proxy'] : false;
        $proxy = isset(Yii::app()->params['pasapi']['proxy']) ? Yii::app()->params['pasapi']['proxy'] : $default_proxy;
        $request_timeout = isset(Yii::app()->params['pasapi']['curl_timeout']) ? Yii::app()->params['pasapi']['curl_timeout'] : 60;

        if (!empty($proxy)) curl_setopt($this->_curl->curl, CURLOPT_PROXY, $proxy);
        curl_setopt($this->_curl->curl, CURLOPT_TIMEOUT, $request_timeout);

        $this->_xml_helper = new XmlHelper();
    }

    /**
     * @param $data
     * @return bool|void
     * @throws Exception
     */
    public function search($data)
    {

        if ( !$this->isAvailable() ) {
            // log ?
            return false;
        }

        $resource_model = "\\OEModule\\PASAPI\\resources\\Patient";
        $pas_params = array();

        //will be accessed at the \Patient model's search function
        $results = &$data['results'];
        $patient = $data['patient'];
        $params = $data['params'];
        $params['hos_num'] = $patient->hos_num;
        $params['nhs_num'] = $patient->nhs_num;

        foreach (self::SEARCH_PARAMS as $param) {
            $pas_params[$param] = $params[$param] ?? "";
        }

        if (!$this->isPASqueryRequired($pas_params)) {
            // no need to update the record
            return false;
        }

        $xml = $this->pasRequest($data);

        //loading the xml
        $this->_xml_helper->xml($xml);

        // validating the XML
        if (!$this->_xml_helper->isValid()) {
            OELog::log('PASAPI invalid XML from API request. ' . print_r(array_merge($this->_xml_helper->getErrors(), array(
                    'hos_num' => isset($data['patient']->hos_num) ? $data['patient']->hos_num : '',
                    'nhs_num' => isset($data['patient']->nhs_num) ? $data['patient']->nhs_num : '',
                    'first_name' => isset($data['params']['first_name']) ? $data['params']['first_name'] : '',
                    'last_name' => isset($data['params']['last_name']) ? $data['params']['last_name'] : '',
                    'dob' => isset($data['params']['dob']) ? $data['params']['dob'] : '',
                )), true));

            $data['patient']->addPasError('Error occurred during the PAS synchronization, some data may be out of date or incomplete');
            Yii::app()->user->setFlash('warning.pas_unavailable', 'PAS is currently unavailable, some data may be out of date or incomplete');

            // XML captured in DB : audit.data
        }

        // count the Patient nodes
        $patient_count = $this->_xml_helper->countNodes('Patient');

        if (!$patient_count) {
            // empty <PatientList>, nothing to do here
            return true;
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $xml_handler = $this->_xml_helper->getHandler();

            while ($xml_handler->read() && $xml_handler->name !== 'Patient');

            // now that we're at the right depth, hop to the next <patient/> until the end of the tree
            while ($xml_handler->name === 'Patient') {
                $node = new SimpleXMLElement($xml_handler->readOuterXML());

                /**
                 * @var $resource \OEModule\PASAPI\resources\Patient
                 */
                $resource = $resource_model::fromXml('V1', $xml_handler->readOuterXML(), array());

                $resource->id = $node->HospitalNumber;

                $_assignment = $resource->getAssignment();
                $_patient = $_assignment->getInternal();

                $save_resource = function() use ($resource, $_patient, $data, $node) {
                    $resource->partial_record = !$_patient->isNewRecord;
                    if (!$resource->save() && ($data['patient'] instanceof Patient)) {
                        $data['patient']->addPasError('Patient not updated from PAS, some data may be out of date or incomplete');
                        OELog::log('PASAPI Patient resource model could not be saved. Hos num: ' . $node->HospitalNumber . ' ' . print_r($resource->errors, true));
                    }
                };

                //if XML contains only one patient we always save
                if ($patient_count == 1) {
                    $save_resource();
                } elseif ($patient_count > 1) {
                    //XML contains more patients, some of them may exist in the local DB, we save those
                    //but not add to the $results array because Patient model's search function will retrieve them
                    if (!$_patient->isNewRecord) {
                        $save_resource();
                    } else {
                        //unsaved patients are added to the $results array - it will displayed to the user to pick one then PASAPI will
                        //perform a search by hos_num, XML will return only one result and it will be saved
                        $patient = $this->buildPatientObject($resource);
                        $results[] = $patient;
                    }
                }

                $xml_handler->next('Patient');
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            OELog::log("PASAPI : " . $e->getMessage());
        }

        //we do not return anything here
        //either a Patient was saved or the data will be available in the referenced $results array
    }

    /**
     * Build Patient Object from XML without saving
     * @param \OEModule\PASAPI\resources\Patient $resource
     * @return Patient
     */
    private function buildPatientObject(\OEModule\PASAPI\resources\Patient $resource)
    {
        $patient = new Patient();
        $contact = new Contact();
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
     * @throws Exception
     */
    private function pasRequest($data)
    {
        $_patient = $data['patient'];
        $params = $data['params'];
        $xml = false;

        $url = Yii::app()->params['pasapi']['url'];

        $query = array();
        $institution = Institution::model()->getCurrent();

        if ($_patient->hos_num) {
            $query['hosnum'] = $_patient->hos_num;
            $query['authorityid'] = $institution->pas_key;
        } elseif ($_patient->nhs_num) {
            $query['nhsnum'] = $_patient->nhs_num;
        } elseif ($params['last_name']) {
            $query['familyname'] = $params['last_name'];
            if ($params['first_name']) {
                $query['givenname'] = $params['first_name'];
            }
            if ($params['dob']) {
                $query['dob'] = date('Y-m-d', strtotime($params['dob']));
            }
        }

        $error = '';
        if (!empty($query)) {
            $xml = $this->_curl->get($url . '?' . http_build_query($query));
            $ch = $this->_curl->curl;

            if (curl_errno($ch)) {
                $error = 'PASAPI cURL error occurred on API request. Request error: ' . curl_error($ch) . " ";
                OELog::log($error);
            }
        }

        Audit::add('PASAPI', 'GET request', $error . (string)$xml);

        return $xml;
    }

    /**
     * Is PAS enabled and up?
     */
    public function isAvailable()
    {
        $enabled = (isset(Yii::app()->params['pasapi']['enabled']) && Yii::app()->params['pasapi']['enabled'] === true);
        return $enabled && (isset(Yii::app()->params['pasapi']['url']) && !empty(Yii::app()->params['pasapi']['url']));
    }


    /**
     * Checks if we have to query the PAS or not
     * If hos_num or nhs_num was searched we check the patient if his/her record is stale
     *
     * @param $params
     * @return bool
     */
    public function isPASqueryRequired($params)
    {
        $pasapi_allowed_search_params = $this->getValidAllowedSearchParams();
        if (is_array($pasapi_allowed_search_params) && !empty($pasapi_allowed_search_params)) {
            foreach ($params as $key => $param) {
                if ($param != null && $param != "" && !in_array($key, $pasapi_allowed_search_params)) {
                    return false;
                }
            }
        }
        if (!empty($params['hos_num']) || !empty($params['nhs_num'])) {
            // validate the hos_num and hns_num
            $patient_search = new PatientSearch();
            $hos_num = $patient_search->getHospitalNumber($params['hos_num']);
            $nhs_num = $patient_search->getNHSnumber($params['nhs_num']);

            //get the patient
            $patient_criteria = new CDbCriteria();
            $patient_criteria->addCondition('hos_num =:hos_num', 'OR');
            $patient_criteria->addCondition('nhs_num =:nhs_num', 'OR');
            $patient_criteria->params[':nhs_num'] = $nhs_num;
            $patient_criteria->params[':hos_num'] = $hos_num;

            $_patient = Patient::model()->find($patient_criteria);

            if ($_patient) {
                // get the assignment
                $criteria = new CDbCriteria();
                $criteria->addCondition('resource_type ="Patient"');
                $criteria->addCondition('internal_id =:id');
                $criteria->params[':id'] = $_patient->id;

                $assignment = PasApiAssignment::model()->find($criteria);

                if ($assignment && !$assignment->isStale()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getPasApiAllowedSearchParams()
    {
        if ( array_key_exists('allowed_params', Yii::app()->params['pasapi'])) {
            $pas_api_allowed_params = Yii::app()->params['pasapi']['allowed_params'];
        }
        return isset($pas_api_allowed_params) && is_array($pas_api_allowed_params)? $pas_api_allowed_params : [];
    }

    public function getValidAllowedSearchParams()
    {
        $allowed_search_params = $this->getPasApiAllowedSearchParams();
        $invalid = array_diff($allowed_search_params, self::SEARCH_PARAMS);
        return array_diff($allowed_search_params, $invalid);
    }
}
