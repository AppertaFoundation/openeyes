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
class InternalReferralDeliveryCommand extends CConsoleCommand
{

    private $path;

    // integration required to generate xml
    private $generate_xml = false;

    private $xml_template_file;

    private $generate_csv = false;

    private $csv_format = 'OEInternalReferralLetterReport_%s.csv';

    private $file_random_number;

    public function setFileRandomNumber($number)
    {
        $this->file_random_number = $number;
    }

    public function getFileRandomNumber()
    {
        return $this->file_random_number ? $this->file_random_number : rand();
    }



    /**
     * InternalReferralDelivery constructor.
     */
    public function __construct()
    {
        /*
            checking the integration if needed

        if ( !\Yii::app()->hasComponent('internalReferralIntegration')){
            throw new Exception("No Internal Referral integration found");
        }

        $template_path = \Yii::app()->internalReferralIntegration->getTemplatePath();
        $this->xml_template_file = \Yii::getPathOfAlias($template_path) . '/' . \Yii::app()->internalReferralIntegration->getWIFxmlTemplate() . '.php';

        if (!file_exists($this->xml_template_file)){
            throw new \Exception('Template '.$template_path.' does not exist.');
        }
       */

        $this->path = \Yii::app()->params['OphCoCorrespondence_Internalreferral']['export_dir'];

        // requires integration
        //$this->generate_xml = !isset(\Yii::app()->params['OphCoCorrespondence_Internalreferral']['xml_format']) || Yii::app()->params['OphCoCorrespondence_Internalreferral']['xml_format'] !== 'none';

        $this->generate_csv = \Yii::app()->params['OphCoCorrespondence_Internalreferral']['generate_csv'];

        // check if directory exists
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
            echo "ALERT! Directory " . $this->path . " has been created!";
        }

        parent::__construct(null, null);
    }

    /**
     * Run the command.
     */
    public function actionIndex()
    {
        $pending_documents = $this->getPendingDocuments();
        foreach ($pending_documents as $document) {
            $this->actionGenerateOne($document->document_target->document_instance->correspondence_event_id);
        }
    }

    public function actionGenerateOne($event_id)
    {
        $criteria = new CDbCriteria();
        $criteria->join = "JOIN document_target ON t.document_target_id = document_target.id";
        $criteria->join .= " JOIN document_instance ON document_target.document_instance_id = document_instance.id";
        $criteria->join .= " JOIN event ON document_instance.correspondence_event_id = event.id";

        $criteria->addCondition("event.id = " . $event_id);
        $criteria->addCondition("event.deleted = 0");
        $criteria->addCondition("t.`output_type`= 'Internalreferral'");

        $document_outputs = DocumentOutput::model()->findAll($criteria);

        foreach ($document_outputs as $document) {
            $event = $document->document_target->document_instance->event;

            echo 'Processing event ' . $event->id . ' :: Internal Referral' . PHP_EOL;

            $pdf_generated = $this->savePDFFile($event->id, $document->id);
            $filename = $this->getFileName($event);

            if ($this->generate_xml) {
                $xml_generated = $this->generateXMLOutput($event, $filename);
            }

            if (!$pdf_generated || ($this->generate_xml && !$xml_generated)) {
                echo 'Generating Internal referral file ' . $filename . ' failed' . PHP_EOL;
                return false;
            }

            if ($this->updateDelivery($document->id)) {
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
                    'letter_sent_date' => date('Y-m-d H:i:s'),
                ));
            }
        }
    }

    /**
     * @return CActiveRecord[]
     */
    private function getPendingDocuments()
    {
        $criteria = new CDbCriteria();
        $criteria->join = "JOIN `document_target` tr ON t.`document_target_id` = tr.id";
        $criteria->join .= " JOIN `document_instance` i ON tr.`document_instance_id` = i.`id`";
        $criteria->join .= " JOIN event e ON i.`correspondence_event_id` = e.id";
        $criteria->addCondition("e.deleted = 0");
        $criteria->addCondition("e.delete_pending = 0");
        $criteria->addCondition("t.`output_status` = 'PENDING' AND t.`output_type`= 'Internalreferral'");

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
        $event = Event::model()->findByPk($event_id);
        $document_output = DocumentOutput::model()->findByPk($output_id);


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
            curl_setopt($ch, CURLOPT_URL, $print_url . $event->id . '?auto_print=' . (int)$inject_autoprint_js . '&print_only_gp=0');
            $content = curl_exec($ch);

            curl_close($ch);

            if (substr($content, 0, 4) !== "%PDF") {
                echo 'File is not a PDF for event id: '.$event->id."\n";
                $this->updateFailedDelivery($output_id);
                return false;
            }

            $filename = $this->getFileName($event);

            return $pdf_generated = (file_put_contents($this->path . "/" . $filename . ".pdf", $content) !== false);
        }
    }

    /**
     * Returns file name based on the event
     *
     * @param Event $event
     * @return string
     */
    private function getFileName(\Event $event)
    {
        $local_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
            'LOCAL',
            $event->episode->patient->id,
            $event->institution_id, $event->site_id
        ));
        if (!isset(Yii::app()->params['filename_format']) || Yii::app()->params['filename_format'] === 'format1') {
            $filename = "OPENEYES_Internal_" . (str_replace(' ', '', $local_identifier_value)) . '_' . $event->id . "_" . $this->getFileRandomNumber();
        } else {
            if (Yii::app()->params['filename_format'] === 'format2') {
                $filename = 'Internal_' . (str_replace(' ', '', $local_identifier_value)) . '_' . date(
                    'YmdHi',
                    strtotime($event->last_modified_date)
                ) . '_' . $event->id;
            } else {
                if (Yii::app()->params['filename_format'] === 'format3') {
                    $filename = 'Internal_' . (str_replace(' ', '', $local_identifier_value)) . '_edtdep-OEY_' .
                        date('Ymd_His', strtotime($event->last_modified_date)) . '_' . $event->id;
                }
            }
        }

        return $filename;
    }

    /**
     * Generate XML file
     *
     * @param Event $event
     * @param $filename
     * @return bool
     */
    private function generateXMLOutput(\Event $event, $filename)
    {
        $data = Yii::app()->internalReferralIntegration->constructRequestData($event, $this->path . "/" . $this->getFileName($event) . ".pdf");
        $xml = $this->renderFile($this->xml_template_file, $data, true);
        return (file_put_contents($this->path . "/" . $filename . ".XML", $this->cleanXML($xml)) !== false);
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
     * Sets document's output status to COMPLETE
     *
     * @param int $output_id
     * @return boolean
     */
    private function updateDelivery($output_id)
    {
        $output = DocumentOutput::model()->findByPk($output_id);
        $output->updateStatus(DocumentOutput::STATUS_COMPLETE);

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
            if ($put_header) {
                fputcsv($fp, array_keys($data));
            }
            fputcsv($fp, $data);
            fclose($fp);
        }
    }

    private function updateFailedDelivery($output_id)
    {
        $output = DocumentOutput::model()->findByPk($output_id);
        $output->updateStatus(DocumentOutput::STATUS_FAILED);

        return $output->save();
    }
}
