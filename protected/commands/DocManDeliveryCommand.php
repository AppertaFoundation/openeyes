<?php

use OEModule\PASAPI\models\PasApiAssignment;
use OEModule\PASAPI\resources\PatientAppointment;

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

class DocManDeliveryCommand extends BaseDeliveryCommand
{
    protected $with_internal_referral = true;
    public bool $with_print = true;

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
            $xml_generated = $this->generateXMLOutput($file_info['filename'], $this->event, $document);

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
            curl_setopt($ch, CURLOPT_COOKIE , "institution_id=$event->institution_id;site_id=$event->site_id");

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
            if (getenv("RTF_HOSTNAME") != null) {
                $html_url = "http://localhost/OphCoCorrespondence/default/printForRecipient/" . $event->id;

                $footer = Yii::app()->db->createCommand()->select('footer')->from('document_instance')->where('correspondence_event_id=:event_id', array(':event_id'=>$event_id))->queryRow()['footer'];
                $footer = addslashes("<div style=\"font-size: 7.4pt;\">" . substr($footer, strpos($footer, "<div class"), strpos($footer, "</body>") - strpos($footer, "<div class")) . "</div>");

                $file = fopen("/tmp/cookie.txt", 'r');
                $cookie_file = fread($file, filesize("/tmp/cookie.txt"));
                $oe_cookie = trim(explode(' ', preg_split('/OESESSID/', $cookie_file)[1])[0]);
                fclose($file);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://".getenv("RTF_HOSTNAME").":4567/");
                curl_setopt($ch, CURLOPT_POST, 1);
                $payload = json_encode(array("html"=>$html_url, "cookie"=>$oe_cookie, "footer"=>$footer));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                $content_rtf = curl_exec($ch);
                $http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_response != 200) {
                    echo 'Generating Docman file' . $filename . '.rtf failed, with response ' . $http_response . PHP_EOL;
                    return false;
                }

                $rtf_generated = (file_put_contents($this->path . "/" . $filename . ".rtf", $content_rtf) !== false);
            }

            if (substr($content, 0, 4) !== "%PDF") {
                echo 'File is not a PDF for event id: '.$this->event->id."\n";
                $this->updateFailedDelivery($output_id);
                return false;
            }

            $pdf_generated = (file_put_contents($this->path . "/" . $filename . ".pdf", $content) !== false);
            if ($this->generate_xml) {
                $xml_generated = $this->generateXMLOutput($filename, $this->event, $document_output);
            }

            if (!$pdf_generated || ($this->generate_xml && !$xml_generated)) {
                echo 'Generating Docman file ' . $filename . ' failed' . PHP_EOL;
                return false;
            }

            if ($this->updateDelivery($output_id)) {
                $element_letter = ElementLetter::model()->findByAttributes(array("event_id" => $event->id));

                $local_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
                    'LOCAL',
                    $event->episode->patient->id,
                    $event->institution_id, $event->site_id
                ));

                $this->logData(array(
                    'hos_num' => $local_identifier_value,
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

        $local_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
            'LOCAL',
            $this->event->episode->patient->id,
            $this->event->institution_id, $this->event->site_id
        ));

        foreach ($templateStrings as $templateString) {
            switch ($templateString) {
                case '{prefix}':
                    $replacePairs[$templateString] = isset($prefix) && !empty($prefix) ? $prefix . '_' : '';
                    break;
                case '{patient.hos_num}':
                    $replacePairs[$templateString] = $local_identifier_value;
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
    private function generateXMLOutput($filename, \Event $event, $document_output)
    {
        $element_letter = ElementLetter::model()->findByAttributes(array("event_id" => $event->id));
        $pasapi_assignment =  PasApiAssignment::model()->findByAttributes([
                    'resource_type' => PatientAppointment::$resource_type,
                    'internal_id' => $event->worklist_patient_id,
                    'internal_type' => '\WorklistPatient']);

        $data = $this->getGeneralDataForTemplate($filename, $event);
        $extra_data = [
            'site_name' => $element_letter->site->name,
            'site_short_name' => $element_letter->site->short_name,
            'letter_type' => $element_letter->letterType->name ?? '',
            'letter_type_id' => $element_letter->letterType->id ?? '',
            'with_internal_referral' => $this->with_internal_referral,
            'service_to' => isset($element_letter->toSubspecialty) ? $element_letter->toSubspecialty->ref_spec : '',
            'consultant_to' => isset($element_letter->toFirm) ? $element_letter->toFirm->pas_code : '',
            'consultant_title' => isset($element_letter->toFirm->consultant) ? $element_letter->toFirm->consultant->title : '',
            'consultant_first_name' => isset($element_letter->toFirm->consultant) ? $element_letter->toFirm->consultant->first_name : '',
            'consultant_last_name' => isset($element_letter->toFirm->consultant) ? $element_letter->toFirm->consultant->last_name : '',
            'is_urgent' => $element_letter->is_urgent ? 1 : '',
            'is_same_condition' => $element_letter->is_same_condition ? 'True' : 'False',
            'location_code' => isset($element_letter->toLocation) ? $element_letter->toLocation->site->location_code : '',
            'visit_id' => $pasapi_assignment ? $pasapi_assignment->resource_id : '',
            'output_type' => $document_output->output_type,
            'with_internal_referral' => $this->with_internal_referral
        ];

        // $extra_data + $data :
        // for keys that exist in both arrays, the elements from the left-hand ($extra_data) array will be used,
        // and the matching elements from the right-hand ($data) array will be ignored
        $xml = $this->renderFile($this->xml_template, ['data' => ($extra_data + $data)], true);

        return file_put_contents($this->path . "/" . $filename . ".xml", $this->cleanXML($xml)) !== false;
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
}
