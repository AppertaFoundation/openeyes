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
    private $_data = array();

    /**
     * Objet to parsing XML
     * @var null
     */
    private $_xml_parser = null;

    /**
     * Object to making http requests
     * @var null
     */
    private $_request = null;

    public function __construct($request = null, $xml_parser = null)
    {
        $this->_request = $request ? $request : new Curl();
        $this->_xml_parser = $xml_parser ? $xml_parser : new PatientXmlParser();
    }

    public function search($data)
    {
        $results = array();

        $xml = $this->_request->get('http://localhost:4200/patient/search?hos_num=123456');

        //at this points we built the Patient(and contact) objects BUT DID NOT SAVE THEM
        $patients = $this->_xml_parser->parse($xml);

        // save the exact match
        if( count($patients) == 1 ){
            $patient = array_shift($patients);

            $transaction = Yii::app()->db->beginTransaction();
            try {

                if(!$patient->contact->save()){
                    throw new Exception('Unable to save contact: '.print_r($patient->contact->getErrors(), true));
                }

                if(!$patient->save()){
                    throw new Exception('Unable to save patient: '.print_r($patient->getErrors(), true));
                }

                $transaction->commit();

            } catch (Exception $e) {
                $transaction->rollback();
                OELog::log("PASAIP : " . $e->getMessage());
            }

        } else {
            foreach($patients as $patient){
                $results[] = $patient;
            }
        }
    }
}