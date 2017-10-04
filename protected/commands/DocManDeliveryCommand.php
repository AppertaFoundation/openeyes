<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DocManDeliveryCommand extends CConsoleCommand
{
    //if export path provided it will overwrite the $path
    public $export_path = null;

    private $path;

    private $event;

    private $csv_format = 'OEGPLetterReport_%s.csv';

    private $generate_xml = false;

    private $generate_csv = false;

    /**
     * Whether Internal referral tags generated into the xml, also processes XML for only Internal referrals as well
     *
     * BUT, it will not generate 3rd part (like WinDip) XML, to generate specific
     *
     * @var bool
     */
    private $with_internal_referral = true;

    /**
     * DocManDeliveryCommand constructor.
     */
    public function __construct()
    {
        $this->path = $this->export_path ? $this->export_path : Yii::app()->params['docman_export_dir'];

        $this->generate_xml = !isset(Yii::app()->params['docman_xml_format']) || Yii::app()->params['docman_xml_format'] !== 'none';
        $this->generate_csv = Yii::app()->params['docman_generate_csv'];
        $this->with_internal_referral = !isset(Yii::app()->params['docman_with_internal_referral']) || Yii::app()->params['docman_with_internal_referral'] !== false;

        $this->checkPath($this->path);

        parent::__construct(null, null);
    }

    /**
     * Create directory if not exist
     * @param $path
     */
    private function checkPath($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            echo "ALERT! Directory " . $this->path . " has been created!";
        }
    }


    /**
     * Run the command.
     */
    public function actionIndex()
    {
        $pending_documents = $this->getDocumentsByOutputStatus("PENDING");
        foreach ($pending_documents as $document) {

            $this->event = Event::model()->findByPk($document->document_target->document_instance->correspondence_event_id);

            $this->processDocumentOutput($document);
        }
    }

    private function processDocumentOutput($document)
    {
        if($document->output_type == 'Docman'){
            echo 'Processing event ' . $document->document_target->document_instance->correspondence_event_id . ' :: Docman' . PHP_EOL;
            $this->savePDFFile($document->document_target->document_instance->correspondence_event_id, $document->id);
        } else if($document->output_type == 'Internalreferral'){

            $file_name = $this->getFileName('Internal');
            //Docman xml will be used
            $xml_generated = $this->generateXMLOutput($file_name, $document);

            if ($xml_generated){
                $internal_referral_command = new InternalReferralDeliveryCommand();
                $file_name_array = explode('_', $file_name);
                $internal_referral_command->setFileRandomNumber( $file_name_array[4] );

                //now we only generate PDF file, until the integration, the generate_xml is set to false in the InternalReferralDeliveryCommand
                $internal_referral_command->actionGenerateOne($this->event->id);
            }
        }
    }

    public function actionGenerateOne($event_id, $path = null)
    {

        if( $path ){
            $this->path = $path;
            $this->checkPath($path);
        }

        if(!$this->event){
            $this->event = Event::model()->findByPk($event_id);
        }

        $criteria = new CDbCriteria();
        $criteria->join = "JOIN document_target ON t.document_target_id = document_target.id";
        $criteria->join .= " JOIN document_instance ON document_target.document_instance_id = document_instance.id";

        $criteria->join .= " JOIN event ON document_instance.correspondence_event_id = event.id";

        $criteria->addCondition("event.id = " . $event_id);
        $criteria->addCondition("event.deleted = 0");

        $criteria_string = '';
        if($this->with_internal_referral){
            $criteria_string = " OR t.`output_type`= 'Internalreferral'";
        }

        $criteria->addCondition("t.`output_type`= 'Docman'" . $criteria_string);

        $document_outputs = DocumentOutput::model()->findAll($criteria);

        foreach ($document_outputs as $document) {
            $this->processDocumentOutput($document);
        }
    }

    /**
     * @return CActiveRecord[]
     */
    private function getDocumentsByOutputStatus($output_status = null)
    {   
        $criteria = new CDbCriteria();
        $criteria->join = "JOIN `document_target` tr ON t.`document_target_id` = tr.id";
        $criteria->join .= " JOIN `document_instance` i ON tr.`document_instance_id` = i.`id`";
        $criteria->join .= " JOIN event e ON i.`correspondence_event_id` = e.id";
        $criteria->addCondition("e.deleted = 0");

        $criteria_string = '';
        if($this->with_internal_referral){
            $criteria_string = " OR t.`output_type`= 'Internalreferral'";
        }

        if($output_status){
            $criteria->addCondition("t.`output_status` = :output_status");
            $criteria->params = array(':output_status' => $output_status);
        }

        $criteria->addCondition("(t.`output_type`= 'Docman'" . $criteria_string . ")");

        return DocumentOutput::model()->findAll($criteria);
    }

    /**
     * @param $event_id
     * @param $output_id
     *
     * @return bool;
     */
    private function savePDFFile($event_id, $output_id)
    {
        $pdf_generated = false;
        $xml_generated = false;
        $document_output = DocumentOutput::model()->findByPk($output_id);
        $print_only_gp = $document_output->output_type != 'Docman' ? '0' : '1';

        //@TODO: remove the $this->>event and pass it to the function as a param
        $this->event = $event = Event::model()->findByPk($event_id);

        if ($event) {
            $login_page = Yii::app()->params['docman_login_url'];
            $username = Yii::app()->params['docman_user'];
            $password = Yii::app()->params['docman_password'];
            $print_url = Yii::app()->params['docman_print_url'];
            $inject_autoprint_js = Yii::app()->params['docman_inject_autoprint_js'];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $login_page);
            // disable SSL certificate check for locally issued certificates
            if(Yii::app()->params['disable_ssl_certificate_check']) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }
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
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

            curl_exec($ch);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_URL, $print_url . $event->id . '?auto_print=' . (int)$inject_autoprint_js . '&print_only_gp=' . $print_only_gp);
            $content = curl_exec($ch);
            
            curl_close($ch);
            
            if(substr($content, 0, 4) !== "%PDF"){
                echo 'File is not a PDF for event id: '.$this->event->id."\n";
                $this->updateFailedDelivery($output_id);
                return false;
            }
            
            if (!isset(Yii::app()->params['docman_filename_format']) || Yii::app()->params['docman_filename_format'] === 'format1') {
                $filename = "OPENEYES_" . (str_replace(' ', '', $this->event->episode->patient->hos_num)) . '_' . $this->event->id . "_" . rand();
            } else {
                if (Yii::app()->params['docman_filename_format'] === 'format2') {
                    $filename = (str_replace(' ', '', $this->event->episode->patient->hos_num)) . '_' . date('YmdHi',
                            strtotime($this->event->last_modified_date)) . '_' . $this->event->id;
                } else {
                    if (Yii::app()->params['docman_filename_format'] === 'format3') {
                        $filename = (str_replace(' ', '', $this->event->episode->patient->hos_num)) . '_edtdep-OEY_' .
                            date('Ymd_His', strtotime($this->event->last_modified_date)) . '_' . $this->event->id;
                    }
                }
            }
            
            $pdf_generated = (file_put_contents($this->path . "/" . $filename . ".pdf", $content) !== false);

            if ($this->generate_xml) {
                $xml_generated = $this->generateXMLOutput($filename, $document_output);
            }

            if (!$pdf_generated || ($this->generate_xml && !$xml_generated)) {
                echo 'Generating Docman file ' . $filename . ' failed' . PHP_EOL;

                return false;
            }

            if ($this->updateDelivery($output_id)) {
                $element_letter = ElementLetter::model()->findByAttributes(array("event_id" => $event->id));
                $this->logData(array(
                    'hos_num' => $event->episode->patient->hos_num,
                    'clinician_name' => $event->user->getFullName(),
                    'letter_type' => (isset($element_letter->letterType->name) ? $element_letter->letterType->name : ''),
                    'letter_finalised_date' => $event->last_modified_date,
                    'letter_created_date' => $event->created_date,
                    'last_significant_event_date' => $this->getLastSignificantEventDate($event),
                    'letter_sent_date' => date('Y-m-d H:i:s'),
                ));
            }

            return true;
        }
    }

    private function getFileName($prefix = '')
    {
        if (!isset(Yii::app()->params['docman_filename_format']) || Yii::app()->params['docman_filename_format'] === 'format1') {
            $filename = "OPENEYES_" . ($prefix ? "{$prefix}_" : '') . (str_replace(' ', '', $this->event->episode->patient->hos_num)) . '_' . $this->event->id . "_" . rand();
        } else {
            if (Yii::app()->params['docman_filename_format'] === 'format2') {
                $filename = ($prefix ? "{$prefix}_" : '') . (str_replace(' ', '', $this->event->episode->patient->hos_num)) . '_' . date('YmdHi',
                        strtotime($this->event->last_modified_date)) . '_' . $this->event->id;
            } else {
                if (Yii::app()->params['docman_filename_format'] === 'format3') {
                    $filename = ($prefix ? "{$prefix}_" : '') . (str_replace(' ', '', $this->event->episode->patient->hos_num)) . '_edtdep-OEY_' .
                        date('Ymd_His', strtotime($this->event->last_modified_date)) . '_' . $this->event->id;
                }
            }
        }

        return $filename;
    }

    /**
     * @param string $filename
     * @param DocumentOutput $document_output
     *
     * @return bool
     */
    private function generateXMLOutput($filename, $document_output)
    {
        $element_letter = ElementLetter::model()->findByAttributes(array("event_id" => $this->event->id));
        $sub_obj = isset($this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty) ? $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty : null;
        $subspeciality = isset($sub_obj->ref_spec) ? $sub_obj->ref_spec : 'SS';
        $subspeciality_name = isset($sub_obj->name) ? $sub_obj->name : 'Support Services';
        $nat_id = isset($this->event->episode->patient->gp->nat_id) ? $this->event->episode->patient->gp->nat_id : null;
        $gp_name = isset($this->event->episode->patient->gp->contact) ? $this->event->episode->patient->gp->contact->getFullName() : null;
        $practice_code = isset($this->event->episode->patient->practice->code) ? $this->event->episode->patient->practice->code : '';
        $address = isset($this->event->episode->patient->contact->address) ? $this->event->episode->patient->contact->address->getLetterArray() : array();
        $address1 = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->address1) : '';
        $city = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->city) : '';
        $county = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->county) : '';
        $city = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->city) : '';
        $post_code = isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->postcode) : '';
        $letter_type = isset($element_letter->letterType->name) ? $element_letter->letterType->name : '';

        //Internal referral reference
        $service_to = isset($element_letter->toSubspecialty) ? $element_letter->toSubspecialty->ref_spec : '';
        $consultant_to = isset($element_letter->event->episode->firm) ? $element_letter->event->episode->firm->pas_code : '';
        $is_urgent = $element_letter->is_urgent ? 1 : '';
        $is_same_condition = $element_letter->is_same_condition ? 'True' : 'False';

        $location_code = isset($element_letter->toLocation) ? $element_letter->toLocation->site->location_code : '';

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <DocumentInformation>
            <PatientNumber>" . $this->event->episode->patient->hos_num . "</PatientNumber>
            <NhsNumber>" . $this->event->episode->patient->nhs_num . "</NhsNumber>
            <Name>" . $this->event->episode->patient->contact->getFullName() . "</Name>
            <Surname>" . $this->event->episode->patient->contact->last_name . "</Surname>
            <FirstForename>" . $this->event->episode->patient->contact->first_name . "</FirstForename>
            <SecondForename></SecondForename>
            <Title>" . $this->event->episode->patient->contact->title . "</Title>
            <DateOfBirth>" . $this->event->episode->patient->dob . "</DateOfBirth>
            <Sex>" . $this->event->episode->patient->gender . "</Sex>
            <Address>" . implode(", ", $address) . "</Address>
            <AddressName></AddressName>
            <AddressNumber></AddressNumber>
            <AddressStreet>" . $address1 . "</AddressStreet>
            <AddressDistrict></AddressDistrict>
            <AddressTown>" . $city . "</AddressTown>
            <AddressCounty>" . $county . "</AddressCounty>
            <AddressPostcode>" . $post_code . "</AddressPostcode>
            <GP>" . $nat_id . "</GP>
            <GPName>" . $gp_name . "</GPName>
            <Surgery>" . $practice_code . "</Surgery>
            <SurgeryName></SurgeryName>
            <ActivityID>" . $this->event->id . "</ActivityID>
            <ActivityDate>" . $this->event->event_date . "</ActivityDate>
            <ClinicianType></ClinicianType>
            <Clinician></Clinician>
            <ClinicianName></ClinicianName>
            <Specialty>" . $subspeciality . "</Specialty>
            <SpecialtyName>" . $subspeciality_name . "</SpecialtyName>
            <Location>" . $element_letter->site->short_name . "</Location>
            <LocationName>" . $element_letter->site->name . "</LocationName>
            <SubLocation></SubLocation>
            <SubLocationName></SubLocationName>
            <LetterType>" . $letter_type . "</LetterType>";

        if($this->with_internal_referral) {
            $xml .= "
            <!--Internal Referral-->
            <ServiceTo>" . $service_to . "</ServiceTo>
            <ConsultantTo>" . $consultant_to . "</ConsultantTo>
            <!-- is urgent or not -->
            <workflowimportance>" . $is_urgent . "</workflowimportance>
            <SameCondition>" . $is_same_condition . "</SameCondition>
            <ToLocationCode>" . $location_code . "</ToLocationCode>
        
            <!-- When main recipient is Internalreferral and a CC is a GP the Docman and Internalreferral XMLs look like the same. -->
            <!-- SendTo tag contains the actual output type: Either 'Docman' or 'Internalreferral' -->
            <SendTo>" . $document_output->output_type . "</SendTo>";
        }
        $xml .= "</DocumentInformation>";

        return file_put_contents($this->path . "/" . $filename . ".XML", $this->cleanXML($xml)) !== false;
    }

    /**
     * Special function to sanitize XML
     *
     * @param string $xml
     * @return string
     */
    private function cleanXML($xml)
    {
        return str_replace("&", "and", $xml);
    }

    /**
     * @param $output_id
     * @return bool
     */
    private function updateDelivery($output_id)
    {
        $output = DocumentOutput::model()->findByPk($output_id);
        $output->output_status = "COMPLETE";

        return $output->save();
    }
    
    private function updateFailedDelivery($output_id)
    {
        $output = DocumentOutput::model()->findByPk($output_id);
        $output->output_status = "FAILED";

        return $output->save();
    }

    /**
     * @param $data
     */
    private function logData($data)
    {
        if ($this->generate_csv) {
            $doc_log = new DocumentLog();
            $doc_log->attributes = $data;
            $doc_log->save();

            $csv_filename = implode(DIRECTORY_SEPARATOR, array($this->path, sprintf($this->csv_format, date('Ymd'))));
            $put_header = !file_exists($csv_filename);

            $fp = fopen($csv_filename, 'ab');
            if($put_header){
                fputcsv($fp, array_keys($data));
            }
            fputcsv($fp, $data);
            fclose($fp);
        }
    }

    private function getLastSignificantEventDate(Event $event)
    {
        $correspondence_date = $event->event_date;

        $event_type = EventType::model()->find('class_name=?', array('OphTrOperationnote'));
        $event_type_id = $event_type->id;

        $criteria = new CDbCriteria();
        $criteria->condition = "episode_id = '" . $event->episode->id
            . "' AND event_date <= '$correspondence_date' AND deleted = 0 AND event_type_id = '$event_type_id'";
        $criteria->order = 'event_date desc, created_date desc';

        $last_opnote_date = '';
        if($op_note = Event::model()->find($criteria)){
            $last_opnote_date = $op_note->event_date;
        }

        $event_type = EventType::model()->find('class_name=?', array('OphCiExamination'));
        $event_type_id = $event_type->id;

        $criteria = new CDbCriteria();
        $criteria->condition = "episode_id = '" . $event->episode->id
            . "' AND event_date <= '$correspondence_date' AND deleted = 0 AND event_type_id = '$event_type_id'";
        $criteria->order = 'event_date desc, created_date desc';

        $last_exam_date = '';
        if($examEvent = Event::model()->find($criteria)){
            $last_exam_date = $examEvent->event_date;
        }

        $last_significant_event_date = '';
        if(!$last_exam_date && $last_opnote_date) {
            $last_significant_event_date = $last_opnote_date;
        }
        if($last_exam_date && !$last_opnote_date) {
            $last_significant_event_date = $last_exam_date;
        }
        if(!$last_exam_date && !$last_opnote_date) {
            $last_significant_event_date = null;
        }
        if($last_exam_date && $last_opnote_date){
            $diff = date_diff(date_create($last_exam_date), date_create($last_opnote_date));
            if($diff->days >= 0){
                $last_significant_event_date = $last_opnote_date;
            }else{
                $last_significant_event_date = $last_exam_date;
            }
        }

        return $last_significant_event_date;
    }
}