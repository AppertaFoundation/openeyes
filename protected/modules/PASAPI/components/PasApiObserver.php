<?php
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

    public function __construct()
    {
        $this->_curl = new Curl();
        $this->_xml_helper = new XmlHelper();
    }

    public function search($data)
    {
        $resource_model = "\\OEModule\\PASAPI\\resources\\Patient";

        //will be accessed at the Patient model search function
        $results = &$data['results'];

        $xml = $this->_curl->get('http://192.168.90.100/getXML.php');

        //loading the xml
        $this->_xml_helper->xml($xml);

        // count the Patient nodes
        $patient_count = $this->_xml_helper->countNodes('Patient');

        //save the exact match
        if( $patient_count == 1){

            $transaction = Yii::app()->db->beginTransaction();
            try {

                preg_match('/<HospitalNumber>(.*)<\/HospitalNumber>/', $xml, $matches);

                //@TODO: error handling
                $external_id = $matches[1]; //this is the hospital_number

                //$resource - \OEModule\PASAPI\resources\Patient
                $resource = $resource_model::fromXml('V1', $xml);

                $resource->id = $external_id; //hos_num

                if(!$resource->save()){
                    throw new Exception('Unable to save patient resource: '.print_r($resource->errors, true));
                }

                $transaction->commit();

            } catch (Exception $e) {
                $transaction->rollback();
                OELog::log("PASAIP : " . $e->getMessage());

            }

        } else {

            $xml_handler = $this->_xml_helper->getHandler();

            // move to the first <patient /> node
            while ($xml_handler->read() && $xml_handler->name !== 'Patient');

            // now that we're at the right depth, hop to the next <patient/> until the end of the tree
            while ($xml_handler->name === 'Patient')
            {
                //$resource - \OEModule\PASAPI\resources\Patient
                $resource = $resource_model::fromXml('V1', $xml_handler->readOuterXML() );

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

                $results[] = $patient;

                $xml_handler->next('Patient');
            }
        }

        //we do not return anything here
        //either a Patient was saved or the data will be available in the referenced $results array
    }
}