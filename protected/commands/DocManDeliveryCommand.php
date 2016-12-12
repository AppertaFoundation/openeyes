<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class DocManDeliveryCommand extends CConsoleCommand
{
    private $path;

    private $event;

    public function __construct()
    {
        $this->path = Yii::app()->params['docman_export_dir'];
        // check if directory is exists

        if(!is_dir($this->path))
        {
            mkdir($this->path);
            echo "ALERT! Directory ".$this->path." has been created!";
        }
        parent::__construct(null, null);
    }

    public function actionIndex()
    {
        $pending_documents = $this->getPendingDocuments();
        foreach($pending_documents as $document)
        {
            $event_id = $document->document_target->document_instance->correspondence_event_id;
            //var_dump($event_id);
            $this->savePDFFile($event_id, $document->id);
        }
    }

    private function getPendingDocuments()
    {
        $documents = DocumentOutput::model()->findAllByAttributes(array("output_status"=>"PENDING","output_type"=>"Docman"));
        return $documents;
    }

    private function savePDFFile($event_id, $output_id)
    {
        if($this->event = Event::model()->findByPk($event_id)) {

            $login_page = Yii::app()->params['docman_login_url'];
            $username = Yii::app()->params['docman_user'];
            $password = Yii::app()->params['docman_password'];
            $print_url = Yii::app()->params['docman_print_url'];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $login_page);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                die(curl_error($ch));
            }

            preg_match("/YII_CSRF_TOKEN = '(.*)';/", $response, $token);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_POST, true);

            $params = array(
                'LoginForm[username]' => $username,
                'LoginForm[password]' => $password,
                'LoginForm[YII_CSRF_TOKEN]' => $token[0],
                'YII_CSRF_TOKEN' => $token[0],
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

            curl_exec($ch);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_URL, $print_url . $this->event->id);
            $content = curl_exec($ch);

            curl_close($ch);

            if (!isset(Yii::app()->params['docman_filename_format']) || Yii::app()->params['docman_filename_format'] == 'format1') {
                $filename = "OPENEYES_" . $this->event->episode->patient->hos_num . '_' . $this->event->id . "_" . rand();
            } else if (Yii::app()->params['docman_filename_format'] == 'format2') {
                $filename = $this->event->episode->patient->hos_num . '_' . date('YmdHi',
                        strtotime($this->event->last_modified_date)) . '_' . $this->event->id;
            } else if (Yii::app()->params['docman_filename_format'] == 'format3') {
                $filename = $this->event->episode->patient->hos_num . '_edtdep-OEY_' . date('Ymd_His',
                        strtotime($this->event->last_modified_date)) . '_' . $this->event->id;
            }
            file_put_contents($this->path . "/" . $filename . ".pdf", $content);
            if (!isset(Yii::app()->params['docman_xml_format']) || Yii::app()->params['docman_xml_format'] != 'none') {
                $this->generateXMLOutput($filename, $output_id);
            }
            $this->updateDelivery($output_id);
        }
    }

    private function generateXMLOutput($filename)
    {
        $element_letter = ElementLetter::model()->findByAttributes(array("event_id"=>$this->event->id));
        $letter_types = array("0"=>"","1"=>"Clinic discharge letter","2"=>"Post-op letter","3"=>"Clinic letter","4"=>"Other letter");
        
        $subspeciality = isset($this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->ref_spec) ? $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->ref_spec : 'SS';
        $subspeciality_name = isset($this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->name) ? $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->name : 'Support Services';
        $nat_id = isset($this->event->episode->patient->gp->nat_id) ? $this->event->episode->patient->gp->nat_id : null;
        $gp_name = isset($this->event->episode->patient->gp->contact) ? $this->event->episode->patient->gp->contact->getFullName() : null;
        $practice_code = isset($this->event->episode->patient->practice->code) ? $this->event->episode->patient->practice->code : '';
        $address = isset($this->event->episode->patient->contact->address) ? $this->event->episode->patient->contact->address->getLetterArray() : array();
        $address1 = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->address1) : '';
        $city = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->city) : '';
        $county = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->county) : '';
        $city = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->city) : '';
        $post_code = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->postcode) : '';

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <DocumentInformation>
            <PatientNumber>".$this->event->episode->patient->hos_num."</PatientNumber>
            <NhsNumber>".$this->event->episode->patient->nhs_num."</NhsNumber>
            <Name>".$this->event->episode->patient->contact->getFullName()."</Name>
            <Surname>".$this->event->episode->patient->contact->last_name."</Surname>
            <FirstForename>".$this->event->episode->patient->contact->first_name."</FirstForename>
            <SecondForename></SecondForename>
            <Title>".$this->event->episode->patient->contact->title."</Title>
            <DateOfBirth>".$this->event->episode->patient->dob."</DateOfBirth>
            <Sex>".$this->event->episode->patient->gender."</Sex>
            <Address>".implode(", ", $address)."</Address>
            <AddressName></AddressName>
            <AddressNumber></AddressNumber>
            <AddressStreet>" . $address1 . "</AddressStreet>
            <AddressDistrict></AddressDistrict>
            <AddressTown>".$city."</AddressTown>
            <AddressCounty>".$county."</AddressCounty>
            <AddressPostcode>".$post_code."</AddressPostcode>
            <GP>" . $nat_id . "</GP>
            <GPName>" . $gp_name . "</GPName>
            <Surgery>" . $practice_code . "</Surgery>
            <SurgeryName></SurgeryName>
            <LetterType>".$letter_types[$element_letter->letter_type]."</LetterType>
            <ActivityID>".$this->event->id."</ActivityID>
            <ActivityDate>".$this->event->event_date."</ActivityDate>
            <ClinicianType></ClinicianType>
            <Clinician></Clinician>
            <ClinicianName></ClinicianName>
            <Specialty>".$subspeciality."</Specialty>
            <SpecialtyName>".$subspeciality_name."</SpecialtyName>
            <Location>" . $element_letter->site->short_name . "</Location>
            <LocationName>" . $element_letter->site->name . "</LocationName>
            <SubLocation></SubLocation>
            <SubLocationName></SubLocationName>
            </DocumentInformation>";

        file_put_contents($this->path."/".$filename.".XML", $this->cleanXML($xml) );
    }
    
    /**
     * Special function to sanitize XML
     * 
     * @param type $xml
     * @return type
     */
    private function cleanXML($xml)
    {
        return str_replace ("&", "&amp;", $xml);
    }

    private function updateDelivery($output_id)
    {
        $output = DocumentOutput::model()->findByPk($output_id);
        $output->output_status = "COMPLETE";
        $output->save();
    }
}