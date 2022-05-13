<?php

namespace OEModule\PASAPI\components\Pases;
use OEModule\PASAPI\models\PasApiAssignment;

/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class DefaultPas extends BasePAS
{
    /**
     * Holds the config for the PAS
     * @var array
     */
    private $config = [];

    /**
     * With init we set the config
     *
     * @param $config
     */
    public function init($config)
    {
        $this->config = $config;

        // would be nice to have some kind of NO_PROXY here (or somewhere) to exclude localhost
        $default_proxy = $this->config['curl_proxy'] ?? false;
        $proxy = $this->config['pasapi']['proxy'] ?? $default_proxy;
        $request_timeout = $this->config['pasapi']['curl_timeout'] ?? 60;

        curl_setopt($this->curl->curl, CURLOPT_PROXY, $proxy);
        curl_setopt($this->curl->curl, CURLOPT_TIMEOUT, $request_timeout);
    }

    /**
     * Checks if the connections enabled
     *
     * @return bool
     */
    public function isAvailable() : bool
    {
        return isset($this->config['enabled']) && $this->config['enabled'] === true ? true : false;
    }

    /**
     * Checks if the PAS request is required or not
     *
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function isPASqueryRequired($params) : bool
    {
        $pasapi_allowed_search_params = $this->getValidAllowedSearchParams();

        if (is_array($pasapi_allowed_search_params) && !empty($pasapi_allowed_search_params)) {
            foreach ($params as $key => $param) {
                if ($param != null && $param != "" && !in_array($key, $pasapi_allowed_search_params)) {
                    return false;
                }
            }
        }

        if (!empty($params['patient_identifier_value'])) {
            //get the patient
            $criteria = new \CDbCriteria();

            $criteria->join = " JOIN patient_identifier pi ON t.internal_id = pi.patient_id";

            $criteria->addCondition('pi.value =:value');
            $criteria->addCondition('pi.patient_identifier_type_id =:patient_identifier_type_id');
            $criteria->addCondition('resource_type ="Patient"');

            $criteria->params[':value'] = $params['patient_identifier_value'];
            $criteria->params[':patient_identifier_type_id'] = $params['patient_identifier_type_id'];

            $assignment = PasApiAssignment::model()->count($criteria);

            // we do not update patients, only save new, so if it exist than PAS request does not needed
            if ($assignment) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns parameters allowed to be searched
     *
     * empty array if unset
     * @return array
     */
    public function getPasApiAllowedSearchParams() : array
    {
        $allowed_params = [];
        if ( array_key_exists('allowed_params', $this->config)) {
            $allowed_params = $this->config['allowed_params'];
        }

        return is_array($allowed_params) ? $allowed_params : [];
    }

    /**
     * Returns parameters allowed to be searched
     *
     * @return array
     */
    public function getValidAllowedSearchParams() : array
    {
        $allowed_search_params = $this->getPasApiAllowedSearchParams();
        $invalid = array_diff($allowed_search_params, $this->config['search_params']);
        return array_diff($allowed_search_params, $invalid);
    }

    /**
     * Fires a query to retrieve the patient resources
     *
     * @param $data
     * @return Patient[] of protected/modules/PASAPI/resources/Patient.php
     * @throws Exception
     */
    public function request($data) : array
    {
        $xml = false;
        $query = [];


        // for global search, we search types with GLOBAL number
        if (isset($data['is_global_search']) && $data['is_global_search']) {
            $query = ['nhsnum' => $data['patient_identifier_value']];
        } elseif (isset($data['patient_identifier_value']) && $data['patient_identifier_value']) {
            if ($this->type->usage_type === 'LOCAL') {
                $query['hosnum'] = $data['patient_identifier_value'];
            } elseif ($this->type->usage_type === 'GLOBAL') {
                $query['nhsnum'] = $data['patient_identifier_value'];
            }
        } elseif (isset($data['last_name']) && $data['last_name']) {
            $query['familyname'] = $data['last_name'];

            if ($data['first_name']) {
                $query['givenname'] = $data['first_name'];
                if (isset($data['dob']) && $data['dob']){
                    $query['dob'] = $data['dob'];
                }
            }
        }

        $error = '';
        if (!empty($query)) {
            $xml = $this->curl->get($this->config['url'] . '?' . http_build_query($query));
            $ch = $this->curl->curl;

            if (curl_errno($ch)) {
                $error = 'PASAPI cURL error occurred on API request. Request error: ' . curl_error($ch) . " ";
                \OELog::log($error);
            }
        }

        \Audit::add('PASAPI', 'GET request', $error . (string)$xml);

        //loading the xml
        $this->parser->xml($xml);

        if (!$this->parser->isValid()) {
            \OELog::log('PASAPI invalid XML from API request. ' . print_r(array_merge($this->parser->getErrors(), $data), true));

            $data['patient']->addPasError('Error occurred during the PAS synchronization, some data may be out of date or incomplete');
            \Yii::app()->user->setFlash('warning.pas_unavailable', 'Invalid response from PAS, some data may be out of date or incomplete');

            // XML captured in DB : audit.data
        }

        // count the Patient nodes
        $patient_count = $this->parser->countNodes('Patient');

        if (!$patient_count) {
            // empty <PatientList>, nothing to do here
            return [];
        }

        $resource_model = "\\OEModule\\PASAPI\\resources\\Patient";
        $resources = [];

        try {
            $xml_handler = $this->parser->getHandler();

            // move to the first <patient /> node
            while ($xml_handler->read() && $xml_handler->name !== 'Patient');

            // now that we're at the right depth, hop to the next <patient/> until the end of the tree
            while ($xml_handler->name === 'Patient') {
                $node = new \SimpleXMLElement($xml_handler->readOuterXML());

                // $resource is an instance of \OEModule\PASAPI\resources\Patient
                $resource = $resource_model::fromXml('V1', $xml_handler->readOuterXML());

                $resource->id = $node->HospitalNumber;
                $resources[] = $resource;

                $xml_handler->next('Patient');
            }
        } catch (Exception $e) {
            \OELog::log("PASAPI : " . $e->getMessage());
        }

        return $resources;
    }
}
