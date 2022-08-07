<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCoCvi\components\OphCoCvi_API;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Consent;

class CviDeliveryCommand extends BaseDeliveryCommand
{
    /** @var OphCoCvi_API */
    private $api;
    public $xml_template = '';

    public function __construct()
    {
        parent::__construct();

        \Yii::import('application.modules.OphCoCvi.components.OphCoCvi_API');
        $this->api = new OphCoCvi_API();

        foreach (SettingMetadata::model()->findAll() as $metadata) {
            if (!$metadata->element_type) {
                if (!isset(Yii::app()->params[$metadata->key])) {
                    \Yii::app()->params[$metadata->key] = $metadata->getSetting($metadata->key);
                }
            }
        }
    }

    public function getHelp()
    {
        return <<<EOH
yiic cvidelivery --xml_template=<file>
    --xml_template: full path and filename to the template : eg.: /var/tmp/test_template.php
EOH;
    }

    private function formatOutput($str)
    {
        return $str;
    }

    private function log_info($str)
    {
        Yii::log($this->formatOutput($str), "info", "cvidelivery");
    }

    private function log_error($str)
    {
        Yii::log($this->formatOutput($str), "error", "cvidelivery");
    }

    public function actionIndex()
    {
        $this->log_info("CVI delivery started");

        try {
            $pending = $this->api->getPendingDeliveryEvents();
            $this->log_info("Number of events in this batch: " . count($pending));

            foreach ($pending as $event) {
                $this->log_info("Processing Event ID " . $event->id);
                $info = $this->api->getManager()->getEventInfoElementForEvent($event);
                $demographics = $this->api->getManager()->getDemographicsElementForEvent($event);
                $this->sendToGP($event, $info);
                $this->sendToLA($event, $info, $demographics);
                $this->sendToRCOP($event, $info);
            }
        } catch (Exception $e) {
            $this->log_error("Early exiting due to error in file " . $e->getFile() . " in line " . $e->getLine());
            $this->log_error("Message: " . $e->getMessage());
            $this->log_error("Trace: " . $e->getTraceAsString());
            exit(1);
        }

        $this->log_info("Performing normal shutdown");
        exit(0);
    }

    private function sendToGP(\Event $event, Element_OphCoCvi_EventInfo $info)
    {
        if (!Element_OphCoCvi_Consent::isDocmanEnabled()) {
            $this->log_info("Sending to GP disabled");
            return;
        }

        if ($info->gp_delivery == 0 || $info->gp_delivery_status == Element_OphCoCvi_EventInfo::DELIVERY_STATUS_SENT) {
            return;
        }

        $this->log_info("Sending to GP via Docman");
        $file = $info->generated_document->getPath();
        if (!file_exists($file)) {
            $this->log_error("File not found: $file");
            $info->gp_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $filename = $info->generated_document->uid;

        $dest = $this->path . "/" . $filename . ".pdf";
        if (copy($file, $dest)) {
            if ($this->generate_xml) {
                $xml_generated = $this->generateXMLOutput($filename, $event);
                if (!$xml_generated) {
                    $this->log_error("Failed to generate XML for $filename");
                    unlink($dest);
                    $info->gp_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
                    $info->save();
                    return;
                }
            }
            $info->gp_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_SENT;
            $info->save();

            $hos_num = PatientIdentifier::model()->find('patient_id = :patient_id and patient_identifier_type_id = 1', array('patient_id' => $event->episode->patient->id));

            $this->logData(array(
                'hos_num' => $hos_num,
                'clinician_name' => $event->user->getFullName(),
                'letter_type' => "",
                'letter_finalised_date' => $event->last_modified_date,
                'letter_created_date' => $event->created_date,
                'last_significant_event_date' => $this->getLastSignificantEventDate($event),
                'letter_sent_date' => date('Y-m-d H:i:s'),
            ));
        } else {
            $this->log_error("Failed to copy $file to $dest");
            $info->gp_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }
    }

    private function sendToLA(\Event $event, Element_OphCoCvi_EventInfo $info, \OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics $demographics)
    {
        if (!Element_OphCoCvi_Consent::isLADeliveryEnabled()) {
            $this->log_info("Sending to LA disabled");
            return;
        }

        if ($info->la_delivery == 0 || $info->la_delivery_status == Element_OphCoCvi_EventInfo::DELIVERY_STATUS_SENT) {
            return;
        }

        $this->log_info("Sending to LA");
        $la_email = $demographics->la_email;
        if (is_null($la_email)) {
            $this->log_error("Local Authority email address has not been set in event");
            $info->la_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $file = $info->generated_document->getPath();
        if (!file_exists($file)) {
            $this->log_error("File not found: $file");
            $info->la_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        if (!$this->sendEmail($la_email, $file, $event->site_id)) {
            $this->log_error("Failed to send email to: $la_email");
            $info->la_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $info->la_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_SENT;
        $info->save();
    }

    private function sendToRCOP(\Event $event, Element_OphCoCvi_EventInfo $info)
    {
        if (!Element_OphCoCvi_Consent::isRCOPDeliveryEnabled()) {
            $this->log_info("Sending to RCOP disabled");
            return;
        }

        if ($info->rco_delivery == 0 || $info->rco_delivery_status == Element_OphCoCvi_EventInfo::DELIVERY_STATUS_SENT) {
            return;
        }

        $this->log_info("Sending to RCOP");
        if (( null !== SettingMetadata::model()->getSetting('cvidelivery_rcop_to_email'))) {
            $rco_email = SettingMetadata::model()->getSetting('cvidelivery_rcop_to_email');
        } else {
            $this->log_error("RCOP email address has not been set in configuration");
            $info->rco_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $file = $info->generated_document->getPath();
        if (!file_exists($file)) {
            $this->log_error("File not found: $file");
            $info->rco_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $subject = SettingMetadata::model()->getSetting('cvidelivery_rcop_subject');
        $body = SettingMetadata::model()->getSetting('cvidelivery_rcop_body');

        if (!$this->sendEmail($rco_email, $file, $event->site_id, $subject, $body)) {
            $this->log_error("Failed to send email to: $rco_email");
            $info->rco_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $info->rco_delivery_status = Element_OphCoCvi_EventInfo::DELIVERY_STATUS_SENT;
        $info->save();
    }

    /**
     * @throws Exception
     */
    private function sendEmail(string $to, string $filepath, int $site_id, $subject = null, $body = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('remote_id', \Yii::app()->params['institution_code']);

        $institution_id = Institution::model()->find($criteria)->id;
        $sender_address = SenderEmailAddresses::getSenderAddress($to, $institution_id, $site_id);

        if (!$subject) {
            $subject = \SettingMetadata::model()->getSetting('cvidelivery_la_subject');
        }
        if (!$body) {
            $body = \SettingMetadata::model()->getSetting('cvidelivery_la_body');
        }

        if ($sender_address) {
            try {
                $sender_address->prepareMailer();
                $attachment = array('filePath' => $filepath, 'fileName' => 'CVI.pdf');

                $result = \Yii::app()->mailer->mail(
                    [$to],
                    $subject,
                    $body,
                    [$sender_address->username => "OpenEyes CVI"],
                    $sender_address->reply_to_address,
                    $attachment,
                    'text/html'
                );

                // based on the output we try to figure out the if there is an error or not
                if (is_string($result)) {
                    OELog::log($result);
                    echo "\nError:" . $result . "\n";
                    return false;
                }

                return $result == 1;
            } catch (\Exception $exception) {
                OELog::log($exception->getMessage());
                echo "\nError:" . $exception->getMessage() . "\n";
            }
        } else {
            throw new \Exception("No email configuration found for {$to}. (Admin > Correspondence > Sender Email Addresses)");
        }
    }

    /**
     * Generating and XML file
     *
     * @param $filename
     * @param $document_output
     * @return bool
     * @throws CException
     */
    private function generateXMLOutput($filename, \Event $event)
    {
        $data = $this->getGeneralDataForTemplate($filename, $event);
        $extra_data = [
            'site_short_name' => $event->site->name,
            'site_name' => $event->site->short_name
        ];

        $xml = $this->renderFile($this->xml_template, ['data' => ($extra_data + $data)], true);
        return file_put_contents($this->path . "/" . $filename . ".xml", $this->cleanXML($xml)) !== false;
    }
}
