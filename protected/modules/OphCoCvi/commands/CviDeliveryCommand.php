<?php

use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo_V1;

class CviDeliveryCommand extends BaseDocmanDeliveryCommand
{
    /** @var \OEModule\OphCoCvi\components\OphCoCvi_API */
    private $api;

    protected $with_internal_referral = false;

    public function __construct()
    {
        Yii::import('application.modules.OphCoCvi.components.OphCoCvi_API');
        $this->api = new \OEModule\OphCoCvi\components\OphCoCvi_API();

        $app = Yii::app();

        foreach (SettingMetadata::model()->findAll() as $metadata) {
            if (!$metadata->element_type) {
                if (!isset(Yii::app()->params[$metadata->key])) {
                    Yii::app()->params[$metadata->key] = $metadata->getSetting($metadata->key);
                }
            }
        }

        parent::__construct();
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
            $this->log_info("Number of events in this batch: ".count($pending));

            foreach ($pending as $event) {
                $this->log_info("Processing Event ID ".$event->id);
                $info = $this->api->getManager()->getEventInfoElementForEvent($event);
                $demographics = $this->api->getManager()->getDemographicsElementForEvent($event);
                $this->sendToGP($event, $info);
                $this->sendToLA($event, $info, $demographics);
                $this->sendToRCOP($event, $info);
            }
        } catch (Exception $e) {
            $this->log_error("Early exiting due to error in file ".$e->getFile()." in line ".$e->getLine());
            $this->log_error("Message: ".$e->getMessage());
            $this->log_error("Trace: ".$e->getTraceAsString());
            exit(1);
        }

        $this->log_info("Performing normal shutdown");
        exit(0);
    }

    private function sendToGP(\Event $event, Element_OphCoCvi_EventInfo_V1 $info)
    {
        if (!Element_OphCoCvi_PatientSignature::isDocmanEnabled()) {
            $this->log_info("Sending to GP disabled");
            return;
        }

        if ($info->gp_delivery == 0 || $info->gp_delivery_status == Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT) {
            return;
        }

        $this->log_info("Sending to GP via Docman");
        $file = $info->generated_document->getPath();
        if (!file_exists($file)) {
            $this->log_error("File not found: $file");
            $info->gp_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
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
                    $info->gp_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
                    $info->save();
                    return;
                }
            }
            $info->gp_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT;
            $info->save();

            $this->logData(array(
                'hos_num' => $event->episode->patient->hos_num,
                'clinician_name' => $event->user->getFullName(),
                'letter_type' => "",
                'letter_finalised_date' => $event->last_modified_date,
                'letter_created_date' => $event->created_date,
                'last_significant_event_date' => $this->getLastSignificantEventDate($event),
                'letter_sent_date' => date('Y-m-d H:i:s'),
            ));
        } else {
            $this->log_error("Failed to copy $file to $dest");
            $info->gp_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }
    }

    private function sendToLA(\Event $event, Element_OphCoCvi_EventInfo_V1 $info, \OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics_V1 $demographics)
    {
        if (!Element_OphCoCvi_PatientSignature::isLADeliveryEnabled()) {
            $this->log_info("Sending to LA disabled");
            return;
        }

        if ($info->la_delivery == 0 || $info->la_delivery_status == Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT) {
            return;
        }

        $this->log_info("Sending to LA");
        $la_email = $demographics->la_email;
        if (is_null($la_email)) {
            $this->log_error("Local Authority email address has not been set in event");
            $info->la_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $file = $info->generated_document->getPath();
        if (!file_exists($file)) {
            $this->log_error("File not found: $file");
            $info->la_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $from_email = Yii::app()->params['cvidelivery_la_sender_email'];
        $from_name = Yii::app()->params['cvidelivery_la_sender_name'];
        $subject = Yii::app()->params['cvidelivery_la_subject'];
        $body = Yii::app()->params['cvidelivery_la_body'];

        if (!$this->sendEmail($la_email, $file, $from_email, $from_name, $subject, $body)) {
            $this->log_error("Failed to send email to: $la_email");
            $info->la_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $info->la_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT;
        $info->save();
    }

    private function sendToRCOP(\Event $event, Element_OphCoCvi_EventInfo_V1 $info)
    {
        if (!Element_OphCoCvi_PatientSignature::isRCOPDeliveryEnabled()) {
            $this->log_info("Sending to RCOP disabled");
            return;
        }

        if ($info->rco_delivery == 0 || $info->rco_delivery_status == Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT) {
            return;
        }

        $this->log_info("Sending to RCOP");
        if (isset(Yii::app()->params['cvidelivery_rcop_to_email'])) {
            $rco_email = Yii::app()->params['cvidelivery_rcop_to_email'];
        } else {
            $this->log_error("RCOP email address has not been set in configuration");
            $info->rco_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $file = $info->generated_document->getPath();
        if (!file_exists($file)) {
            $this->log_error("File not found: $file");
            $info->rco_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $from_email = Yii::app()->params['cvidelivery_rcop_sender_email'];
        $from_name = Yii::app()->params['cvidelivery_rcop_sender_name'];
        $subject = Yii::app()->params['cvidelivery_rcop_subject'];
        $body = Yii::app()->params['cvidelivery_rcop_body'];

        if (!$this->sendEmail($rco_email, $file, $from_email, $from_name, $subject, $body)) {
            $this->log_error("Failed to send email to: $rco_email");
            $info->rco_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR;
            $info->save();
            return;
        }

        $info->rco_delivery_status = Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT;
        $info->save();
    }

    private function sendEmail($to, $filepath, $from_email, $from_name, $subject, $body)
    {
        $message = Yii::app()->mailer->newMessage();
        $message->setFrom($from_email, $from_name);
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setBody($body);
        $message->attach(Swift_Attachment::newInstance(file_get_contents($filepath), 'CVI.pdf', 'application/pdf'));

        return Yii::app()->mailer->sendMessage($message);
    }
}
