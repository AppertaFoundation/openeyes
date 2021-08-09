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

class DocManDeliveryCommand extends CConsoleCommand
{
    //if export path provided it will overwrite the $path
    public $export_path = null;
    public $xml_template = '';

    private $path;

    private $event;

    private $generate_xml = false;
    private $with_print = false;

    private $generate_csv = false;
    private $csv_file_options = [
        'file_name' => null,
        'format' => 'OEGPLetterReport_%s.csv',
        'header' => [
            'hos_num',
            'clinician_name',
            'letter_type',
            'letter_finalised_date',
            'letter_created_date',
            'last_significant_event_date',
            'letter_sent_date',
        ]
    ];

    /**
     * Whether Internal referral tags generated into the xml, also processes XML for only Internal referrals as well
     *
     * BUT, it will not generate 3rd part (like WinDip) XML, to generate specific
     *
     * @var bool
     */
    private $with_internal_referral = true;

    public function getHelp()
    {
        return <<<EOH
yiic docmandelivery --xml_template=<file>
    --xml_template: full path and filename to the template : eg.: /var/tmp/test_template.php

yiic docmandelivery generateone --event_id=<event_id>
    --event_id: id of the event
EOH;
    }

    /**
     * DocManDeliveryCommand constructor.
     */
    public function __construct()
    {

        $this->path = $this->export_path ? $this->export_path : Yii::app()->params['docman_export_dir'];

        $template_path = dirname(Yii::app()->basePath) . '/protected/modules/OphCoCorrespondence/views/templates/xml/docman/';
        if (!$this->xml_template) {
            $template_name = isset(\Yii::app()->params['docman_xml_template']) ? \Yii::app()->params['docman_xml_template'] : 'default';
            $this->xml_template = $template_path . $template_name . '.php';
        }

        $this->generate_xml = isset(\Yii::app()->params['docman_generate_xml']) && \Yii::app()->params['docman_generate_xml'];
        $this->with_internal_referral = !isset(Yii::app()->params['docman_with_internal_referral']) || Yii::app()->params['docman_with_internal_referral'] !== false;
        $this->with_print = isset(\Yii::app()->params['docman_with_print']) && \Yii::app()->params['docman_with_print'];

        $this->checkPath($this->path);

        if ($this->generate_csv = Yii::app()->params['docman_generate_csv']) {
            $this->csv_file_options['file_name'] = implode(DIRECTORY_SEPARATOR, array($this->path, sprintf($this->csv_file_options['format'], date('Ymd'))));
            $this->createCSVFile();
        }

        parent::__construct(null, null);
    }

    private function createCSVFile()
    {
        //if file doesn't exists we create one and put the header
        if (!file_exists($this->csv_file_options['file_name'])) {
            try {
                $fp = fopen($this->csv_file_options['file_name'], 'ab');
                fputcsv($fp, $this->csv_file_options['header']);
                fclose($fp);
            } catch (\Exception $exception) {
                \OELog::log($exception->getMessage());
            }
        }
    }

    private function writeCSVFile($data)
    {
        $fp = fopen($this->csv_file_options['file_name'], 'ab');
        fputcsv($fp, $data);
        fclose($fp);
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
        if ($document->output_type == 'Docman') {
            echo 'Processing event ' . $document->document_target->document_instance->correspondence_event_id . ' :: Docman' . PHP_EOL;
            $this->savePDFFile($document->document_target->document_instance->correspondence_event_id, $document->id);
            // $this->savePDFFile generates xml if required
        } elseif ($document->output_type == 'Internalreferral') {
            $file_info = $this->getFileName($document->id, 'Internal');
            //Docman xml will be used
            $xml_generated = $this->generateXMLOutput($file_info['filename'], $document);

            if ($xml_generated) {
                $internal_referral_command = new InternalReferralDeliveryCommand();
                $internal_referral_command->setFileRandomNumber($file_info['rand']);

                //now we only generate PDF file, until the integration, the generate_xml is set to false in the InternalReferralDeliveryCommand
                $internal_referral_command->actionGenerateOne($this->event->id);
            }
        } elseif ($document->output_type == 'Print' && $this->with_print) {
            echo 'Processing event ' . $document->document_target->document_instance->correspondence_event_id . ' :: Print' . PHP_EOL;
            $this->savePDFFile($document->document_target->document_instance->correspondence_event_id, $document->id);
        }
    }

    public function actionGenerateOne($event_id, $path = null)
    {

        if ( $path ) {
            $this->path = $path;
            $this->checkPath($path);
        }

        if (!$this->event) {
            $this->event = Event::model()->findByPk($event_id);
        }

        $criteria = new CDbCriteria();
        $criteria->join = "JOIN document_target ON t.document_target_id = document_target.id";
        $criteria->join .= " JOIN document_instance ON document_target.document_instance_id = document_instance.id";

        $criteria->join .= " JOIN event ON document_instance.correspondence_event_id = event.id";

        $criteria->addCondition("event.id = " . $event_id);
        $criteria->addCondition("event.deleted = 0");

        $criteria_string = '';
        if ($this->with_internal_referral) {
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
        $criteria->addCondition("e.delete_pending = 0");

        $criteria_string = '';
        if ($this->with_internal_referral) {
            $criteria_string = " OR t.`output_type`= 'Internalreferral'";
        }

        if ($this->with_print) {
            $criteria_string .= " OR t.`output_type`= 'Print'";
        }

        if ($output_status) {
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
            if (Yii::app()->params['disable_ssl_certificate_check']) {
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

            $filename = $this->getFileName($output_id)['filename'];

            $content_rtf = null;
            if (isset(Yii::app()->modules['RTFGeneration'])) {
                $html_url = "http://localhost/OphCoCorrespondence/default/printForRecipient/" . $event->id;

                $footer = Yii::app()->db->createCommand()->select('footer')->from('document_instance')->where('correspondence_event_id=:event_id', array(':event_id'=>$event_id))->queryRow()['footer'];

                require("/var/www/openeyes/protected/modules/RTFGeneration/RTFGenerationModule.php");
                $content_rtf = \RTFGenerationModule::generateRTF($html_url, $footer);

                $rtf_generated = (file_put_contents($this->path . "/" . $filename . ".rtf", $content_rtf) !== false);

                if (!$rtf_generated) {
                    echo 'Generating Docman file' . $filename . '.rtf failsed' . PHP_EOL;
                    return false;
                }
            }

            if (substr($content, 0, 4) !== "%PDF") {
                echo 'File is not a PDF for event id: '.$this->event->id."\n";
                $this->updateFailedDelivery($output_id);
                return false;
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

    /**
     * @param $string
     * @return array Return the array of strings that needs to be replaced
     */
    private function getStringsToReplace($string)
    {
        $tokens = [
            '{' => '}',
        ];

        $closeTokens = array_flip($tokens);
        $results = [];
        $stack = [];
        $result = "";
        for ($i = 0; $i < strlen($string); ++$i) {
            $s = $string[$i];
            if (isset($tokens[$s])) {
                $stack[] = $s;
                $result .= $s;
            } elseif (isset($closeTokens[$s])) {
                $result .= $s;
                $results[] = $result;
                $result = "";
                array_pop($stack);
            } elseif (!empty($stack)) {
                $result .= $s;
            }
        }

        return $results;
    }

    /**
     * @param int $document_output_id The id column of the document_output table
     * @param string $prefix Prefix to be prepended to the filename
     * @return array Name of the output file
     */
    private function getFileName($document_output_id, $prefix = '')
    {
        $replacePairs = [
            '{prefix}' => '',
            '{event.id}' => '',
            '{patient.hos_num}' => '',
            '{random}' => '',
            '{gp.nat_id}' => '',
            '{document_output.id}' => '',
            '{event.last_modified_date}' => '',
            '{date}' => ''
        ];

        $fileNameFormat = Yii::app()->params['docman_filename_format'];
        $templateStrings = $this->getStringsToReplace($fileNameFormat);

        foreach ($templateStrings as $templateString) {
            switch ($templateString) {
                case '{prefix}':
                    $replacePairs[$templateString] = isset($prefix) && !empty($prefix) ? $prefix . '_' : '';
                    break;
                case '{patient.hos_num}':
                    $replacePairs[$templateString] = $this->event->episode->patient->hos_num;
                    break;
                case '{event.id}':
                    $replacePairs[$templateString] = $this->event->id;
                    break;
                case '{random}':
                    $rand = rand();
                    $replacePairs[$templateString] = $rand;
                    break;
                case '{gp.nat_id}':
                    $gp = $this->event->episode->patient->gp;
                    if ($gp) {
                        $replacePairs[$templateString] = $gp->nat_id;
                    }
                    break;
                case '{document_output.id}':
                    $replacePairs[$templateString] = $document_output_id;
                    break;
                case '{event.last_modified_date}':
                    $replacePairs[$templateString] = date('Ymd_His', strtotime($this->event->last_modified_date));
                    break;
                case '{date}':
                    $replacePairs[$templateString] = date('YmdHis');
                    break;
            }
        }

        $filename = strtr($fileNameFormat, $replacePairs);

        return ['filename' => $filename, 'rand' => isset($rand) ? $rand : ''];
    }

    /**
     * Generating and XML file
     *
     * @param $filename
     * @param $document_output
     * @return bool
     * @throws CException
     */
    private function generateXMLOutput($filename, $document_output)
    {
        $element_letter = ElementLetter::model()->findByAttributes(array("event_id" => $this->event->id));
        $sub_obj = isset($this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty) ? $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty : null;
        $pasapi_assignment =  \OEModule\PASAPI\models\PasApiAssignment::model()->findByAttributes([
                    'resource_type' => \OEModule\PASAPI\resources\PatientAppointment::$resource_type,
                    'internal_id' => $this->event->worklist_patient_id,
                    'internal_type' => '\WorklistPatient']);

        //I decided to pass each value separately to keep the XML files clean and easier to modify each value if necessary
        $data = [
            'hos_num' => $this->event->episode->patient->hos_num,
            'nhs_num' => $this->event->episode->patient->nhs_num,
            'full_name' => $this->event->episode->patient->contact->getFullName(),
            'last_name' => $this->event->episode->patient->contact->last_name,
            'first_name' => $this->event->episode->patient->contact->first_name,
            'patient_title' => $this->event->episode->patient->contact->title,
            'second_forename' => '',
            'title' => $this->event->episode->patient->contact->title,
            'dob' => $this->event->episode->patient->dob,
            'date_of_death' => $this->event->episode->patient->date_of_death,
            'gender' => $this->event->episode->patient->gender,
            'address' => isset($this->event->episode->patient->contact->address) ? $this->event->episode->patient->contact->address->getLetterArray() : [],
            'address1' => isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->address1) : '',
            'city' => isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->city) : '',
            'county' => isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->county) : '',
            'post_code' => isset($this->event->episode->patient->contact->address) ? ($this->event->episode->patient->contact->address->postcode) : '',

            'gp_nat_id' => isset($this->event->episode->patient->gp->nat_id) ? $this->event->episode->patient->gp->nat_id : null,
            'gp_name' => isset($this->event->episode->patient->gp->contact) ? $this->event->episode->patient->gp->contact->getFullName() : null,
            'gp_first_name' => isset($this->event->episode->patient->gp->contact) ? $this->event->episode->patient->gp->contact->first_name : null,
            'gp_last_name' => isset($this->event->episode->patient->gp->contact) ? $this->event->episode->patient->gp->contact->last_name : null,
            'gp_title' => isset($this->event->episode->patient->gp->contact) ? $this->event->episode->patient->gp->contact->title : null,

            'practice_code' => isset($this->event->episode->patient->practice->code) ? $this->event->episode->patient->practice->code : '',
            'event_id' => $this->event->id,
            'event_date' => $this->event->event_date,
            'subspeciality' => isset($sub_obj->ref_spec) ? $sub_obj->ref_spec : 'SS',
            'subspeciality_name' => isset($sub_obj->name) ? $sub_obj->name : 'Support Services',
            'site_name' => $element_letter->site->name,
            'site_short_name' => $element_letter->site->short_name,
            'letter_type' => isset($element_letter->letterType->name) ? $element_letter->letterType->name : '',
            'letter_type_id' => isset($element_letter->letterType->id) ? $element_letter->letterType->id : '',
            'with_internal_referral' => $this->with_internal_referral,
            'service_to' => isset($element_letter->toSubspecialty) ? $element_letter->toSubspecialty->ref_spec : '',
            'consultant_to' => isset($element_letter->toFirm) ? $element_letter->toFirm->pas_code : '',
            'consultant_title' => isset($element_letter->toFirm->consultant) ? $element_letter->toFirm->consultant->title : '',
            'consultant_first_name' => isset($element_letter->toFirm->consultant) ? $element_letter->toFirm->consultant->first_name : '',
            'consultant_last_name' => isset($element_letter->toFirm->consultant) ? $element_letter->toFirm->consultant->last_name : '',

            'is_urgent' => $element_letter->is_urgent ? 1 : '',
            'is_same_condition' => $element_letter->is_same_condition ? 'True' : 'False',
            'location_code' => isset($element_letter->toLocation) ? $element_letter->toLocation->site->location_code : '',
            'output_type' => $document_output->output_type,

            'visit_id' => $pasapi_assignment ? $pasapi_assignment->resource_id : '',
            'document_links' => [$filename],
        ];

        $xml = $this->renderFile($this->xml_template, ['data' => $data], true);

        return file_put_contents($this->path . "/" . $filename . ".xml", $this->cleanXML($xml)) !== false;
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

            $this->writeCSVFile($data);
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
        if ($op_note = Event::model()->find($criteria)) {
            $last_opnote_date = $op_note->event_date;
        }

        $event_type = EventType::model()->find('class_name=?', array('OphCiExamination'));
        $event_type_id = $event_type->id;

        $criteria = new CDbCriteria();
        $criteria->condition = "episode_id = '" . $event->episode->id
            . "' AND event_date <= '$correspondence_date' AND deleted = 0 AND event_type_id = '$event_type_id'";
        $criteria->order = 'event_date desc, created_date desc';

        $last_exam_date = '';
        if ($examEvent = Event::model()->find($criteria)) {
            $last_exam_date = $examEvent->event_date;
        }

        $last_significant_event_date = '';
        if (!$last_exam_date && $last_opnote_date) {
            $last_significant_event_date = $last_opnote_date;
        }
        if ($last_exam_date && !$last_opnote_date) {
            $last_significant_event_date = $last_exam_date;
        }
        if (!$last_exam_date && !$last_opnote_date) {
            $last_significant_event_date = null;
        }
        if ($last_exam_date && $last_opnote_date) {
            $diff = date_diff(date_create($last_exam_date), date_create($last_opnote_date));
            if ($diff->days >= 0) {
                $last_significant_event_date = $last_opnote_date;
            } else {
                $last_significant_event_date = $last_exam_date;
            }
        }

        return $last_significant_event_date;
    }
}
