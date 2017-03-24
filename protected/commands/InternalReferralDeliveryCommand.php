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
class InternalReferralDeliveryCommand extends CConsoleCommand
{

    private $path;

    private $generate_xml = true;

    private $generate_csv = false;

    private $xml_template_file;

    /**
     * InternalReferralDelivery constructor.
     */
    public function __construct()
    {
        //checking the integration
        if( !\Yii::app()->internalReferralIntegration){
            throw new Exception("No Internal Referral integration found");
        }

        $this->path = \Yii::app()->params['OphCoCorrespondence_Internalreferral']['export_dir'];
        $this->generate_xml = !isset(\Yii::app()->params['OphCoCorrespondence_Internalreferral']['xml_format']) || Yii::app()->params['OphCoCorrespondence_Internalreferral']['xml_format'] !== 'none';
        $this->generate_csv = \Yii::app()->params['OphCoCorrespondence_Internalreferral']['generate_csv'];

        // check if directory exists
        if (!is_dir($this->path)) {
            mkdir($this->path);
            echo "ALERT! Directory " . $this->path . " has been created!";
        }

        $template_path = \Yii::app()->internalReferralIntegration->getTemplatePath();
        $this->xml_template_file = \Yii::getPathOfAlias($template_path) . '/' . \Yii::app()->internalReferralIntegration->getWIFxmlTemplate() . '.php';

        if(!file_exists($this->xml_template_file)){
            throw new \Exception('Template '.$template_path.' does not exist.');
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
            echo 'Processing event ' . $document->document_target->document_instance->correspondence_event_id . PHP_EOL;

            $data = \Yii::app()->internalReferralIntegration->generateWIFxmlRequestData($document->document_target->document_instance->event);

            $xml = $this->savePDFFile($document->document_target->document_instance->event->id, $document->id);
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
            curl_setopt($ch, CURLOPT_URL, $print_url . $event->id . '?auto_print=' . (int)$inject_autoprint_js . '&print_only_gp=0');
            $content = curl_exec($ch);

            curl_close($ch);

            if(substr($content, 0, 4) !== "%PDF"){
                echo 'File is not a PDF for event id: '.$event->id."\n";
                $this->updateFailedDelivery($output_id);
                return false;
            }

            if (!isset(Yii::app()->params['filename_format']) || Yii::app()->params['filename_format'] === 'format1') {
                $filename = "OPENEYES_" . (str_replace(' ', '', $event->episode->patient->hos_num)) . '_' . $event->id . "_" . rand();
            } else {
                if (Yii::app()->params['filename_format'] === 'format2') {
                    $filename = (str_replace(' ', '', $event->episode->patient->hos_num)) . '_' . date('YmdHi',
                            strtotime($event->last_modified_date)) . '_' . $event->id;
                } else {
                    if (Yii::app()->params['filename_format'] === 'format3') {
                        $filename = (str_replace(' ', '', $event->episode->patient->hos_num)) . '_edtdep-OEY_' .
                            date('Ymd_His', strtotime($event->last_modified_date)) . '_' . $event->id;
                    }
                }
            }

            $pdf_generated = (file_put_contents($this->path . "/" . $filename . ".pdf", $content) !== false);

            if ($this->generate_xml) {

                $wif_data = Yii::app()->internalReferralIntegration->generateWIFxmlRequestData($event, $this->path . "/" . $filename . ".pdf");
                $xml = $this->renderFile($this->xml_template_file, $wif_data, true);

                $xml_generated = $this->generateXMLOutput($filename, $xml);
            }

            if (!$pdf_generated || ($this->generate_xml && !$xml_generated)) {
                echo 'Generating for file ' . $filename . ' failed' . PHP_EOL;

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
                    'letter_sent_date' => date('Y-m-d H:i:s'),
                ));
            }

            return true;
        }
    }

    private function generateXMLOutput($filename, $xml)
    {
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

    private function updateDelivery($output_id)
    {
        throw new Exception("Updating database after XML/PDF generation is not implemented yet.");
    }

    private function logData()
    {
        return true;
    }

}