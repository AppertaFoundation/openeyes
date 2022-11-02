<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphCoTherapyapplication_Processor
{
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_REOPENED = 're-opened';

    /**
     *  A list of snomed codes that will be used to determine if a consent form has been created for a relevant injection procedure
     *
     * @var array
     */
    protected $snomed_injection_codes = array(
        '231755001', //Intravitreal injection
        '257891001', //PDT
        '525991000000108', // Lucentis injection
        '1004045004', // Intravitreal injection of anti-vascular endothelial growth factor (procedure)
    );

    private $event;

    /**
     * @param Event $event Must be an OphCoTherapyapplication event
     *
     * @throws Exception
     */
    public function __construct(Event $event)
    {
        $event_type = $event->eventType->class_name;
        if ($event_type != 'OphCoTherapyapplication') {
            throw new Exception("Passed an event of type '$event_type'");
        }

        $this->event = $event;
    }

    /**
     * Returns status of applicant: pending, sent, re-opened.
     *
     * @return string
     */
    public function getApplicationStatus()
    {
        return OphCoTherapyapplication_Email::model()->getStatusForEvent($this->event);
    }

    /**
     * Get any relevant warnings.
     *
     * @return array
     */
    public function getProcessWarnings()
    {
        $warnings = array();

        $el_diag = $this->getElement('Element_OphCoTherapyapplication_Therapydiagnosis');

        $sides = array();
        if ($el_diag->hasLeft()) {
            $sides[] = 'left';
        }
        if ($el_diag->hasRight()) {
            $sides[] = 'right';
        }

        if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
            $missing_sides = array();

            foreach ($sides as $side) {
                if (
                    !$api->getInjectionManagementComplexInEpisodeForDisorder(
                        $this->event->episode->patient,
                        true,
                        $side,
                        $el_diag->{$side . '_diagnosis1_id'},
                        $el_diag->{$side . '_diagnosis2_id'}
                    )
                ) {
                    $missing_sides[] = $side;
                }
            }

            foreach ($missing_sides as $missing) {
                $warnings[] = 'No Injection Management has been created for ' . $missing . ' diagnosis.';
            }

            // if the application doesn't have a given side, the VA value can be NR (e.g. eye missing etc)
            // but if it does, then we need an actual VA value.
            if (!$api->getSnellenVisualAcuityForLeft($this->event->episode->patient, !$el_diag->hasLeft(), $this->event->event_date, false)) {
                $warnings[] = 'Visual acuity not found for left eye.';
            }

            if (!$api->getSnellenVisualAcuityForRight($this->event->episode->patient, !$el_diag->hasRight(), $this->event->event_date, false)) {
                $warnings[] = 'Visual acuity not found for right eye.';
            }
        }

        if ($api = Yii::app()->moduleAPI->get('OphTrConsent')) {
            // note that cannot use params here, as it messes up the quoting on the list in the 'IN' block after the implode
            $procedures = Procedure::model()->findAll(
                array('condition' => 'snomed_code IN (' . implode(", ", $this->snomed_injection_codes) . ') ',
                )
            );
                $proc_ids = array_map(function ($proc) {
                    return $proc->id;
                }, $procedures);
            foreach ($sides as $side) {
                if (!$api->hasConsentForProcedure($this->event->episode, $proc_ids, $side)) {
                    $warnings[] = 'Consent form is required for ' . $side . ' eye.';
                }
            }
        }

        return $warnings;
    }

    /**
     * return boolean to indicate whether the given event is non compliant or not.
     *
     * @return bool
     *
     * @see Element_OphCoTherapyapplication_PatientSuitability::isNonCompliant()
     */
    public function isEventNonCompliant()
    {
        return $this->getElement('Element_OphCoTherapyapplication_PatientSuitability')->isNonCompliant();
    }

    /**
     * @return OphCoTherapyapplication_Email[]
     */
    public function getLeftSentEmails()
    {
        return OphCoTherapyapplication_Email::model()->forEvent($this->event)->leftEye()->findAll();
    }

    /**
     * @return OphCoTherapyapplication_Email[]
     */
    public function getRightSentEmails()
    {
        return OphCoTherapyapplication_Email::model()->forEvent($this->event)->rightEye()->findAll();
    }

    /**
     * Generate PDFs in a wrapper for preview purposes.
     *
     * Note that this is currently only used for non-compliant applications.
     *
     * @param CController $controller
     *
     * @return OETCPDF
     */
    public function generatePreviewPdf($controller)
    {
        Yii::app()->puppeteer->leftMargin = '10mm';
        Yii::app()->puppeteer->rightMargin = '10mm';

        $this->event->lock();

        if (!$this->event->hasPDF('therapy_application') || @$_GET['html']) {
            $wk = Yii::app()->puppeteer;

            $wk->setDocuments(1);
            $wk->setDocRef($this->event->docref);
            $wk->setPatient($this->event->episode->patient);
            $wk->setBarcode($this->event->barcodeSVG);

            $wk->savePageToPDF(
                $this->event->imageDirectory,
                'event',
                'therapy_application',
                'http://localhost/OphCoTherapyapplication/default/renderPreviewPdf?event_id=' . $this->event->id,
                false
            );
        }

        $this->event->unlock();

        if (@$_GET['html']) {
            return Yii::app()->end();
        }

        $pdf = $this->event->getPDF('therapy_application');

        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($pdf));

        readfile($pdf);
    }

    public function getPDFContentForSide($controller, $template_data, $side)
    {
        if ($template_data['suitability']->{$side . '_nice_compliance'}) {
            $file = $this->getViewPath() . DIRECTORY_SEPARATOR . 'pdf_compliant';
        } else {
            $file = $this->getViewPath() . DIRECTORY_SEPARATOR . 'pdf_noncompliant';
        }

        $template_code = $template_data['treatment']->template_code;

        if ($template_code) {
            $specific = $file . '_' . $template_code . '.php';
            if (file_exists($specific)) {
                $file = $specific;
            } else {
                $file .= '.php';
            }
        } else {
            $file .= '.php';
        }

        if (file_exists($file)) {
            return $controller->renderInternal($file, $template_data, true);
        }

        return;
    }

    /**
     * processes the application for the event with id $event_id returns a boolean to indicate whether this was successful
     * or not.
     *
     * @param CController $controller
     * @param User $notify_user
     *
     * @throws Exception
     *
     * @return bool
     */
    public function processEvent(CController $controller, User $notify_user = null)
    {
        if ($this->getApplicationStatus() == self::STATUS_SENT || $this->getProcessWarnings()) {
            return false;
        }

        $success = true;

        $template_data = $this->getTemplateData();

        $diag = $this->getElement('Element_OphCoTherapyapplication_Therapydiagnosis');

        if ($diag->hasLeft() && !$this->processEventForEye($controller, $template_data, Eye::LEFT, $notify_user)) {
            $success = false;
        }

        if ($diag->hasRight() && !$this->processEventForEye($controller, $template_data, Eye::RIGHT, $notify_user)) {
            $success = false;
        }

        return $success;
    }

    /**
     * Get an element object by class name.
     *
     * We could potentially add a caching layer here if performance becomes a problem, but would have to watch out for stale data
     *
     * @todo This should really be available as a public method on Event in the core
     *
     * @param string $class_name
     *
     * @return BaseEventTypeElement|null
     */
    protected function getElement($class_name)
    {
        return $class_name::model()->findByAttributes(array('event_id' => $this->event->id));
    }

    /**
     * @return string
     */
    private function getViewPath()
    {
        return Yii::app()->getModule('OphCoTherapyapplication')->getViewPath() . DIRECTORY_SEPARATOR . 'email';
    }

    public function renderPdfForSide(CController $controller, $side)
    {
        $template_data = $this->getTemplateData();
        $template_data += $this->getSideSpecificTemplateData($side);

        $html = null;
        if ($html = $this->getPDFContentForSide($controller, $template_data, $side)) {
            $html = '<link rel="stylesheet" type="text/css" href="' . $controller->assetPath . '/css/print.css" />' . "\n" . $html;
        }

        return $html;
    }

    public function renderPreviewPdf(CController $controller)
    {
        $ec = $this->getElement('Element_OphCoTherapyapplication_ExceptionalCircumstances');
        if (!$ec) {
            throw new Exception("Exceptional circumstances not found for event ID {$this->event->id}");
        }

        $template_data = $this->getTemplateData();

        $html = '<link rel="stylesheet" type="text/css" href="' . $controller->assetPath . '/css/print.css" />';

        if ($ec->hasLeft()) {
            $left_template_data = $template_data + $this->getSideSpecificTemplateData('left');
            $html .= $this->getPDFContentForSide($controller, $left_template_data, 'left');
        }

        if ($ec->hasRight()) {
            $right_template_data = $template_data + $this->getSideSpecificTemplateData('right');
            $html .= $this->getPDFContentForSide($controller, $right_template_data, 'right');
        }

        return $html;
    }

    /**
     * create the PDF file as a ProtectedFile for the given side.
     *
     * @param CController $controller
     * @param array $template_data
     * @param string $side
     *
     * @throws Exception
     *
     * @return ProtectedFile|null
     */
    protected function createAndSavePdfForSide(CController $controller, array $template_data, $side)
    {
        if (!is_null($this->renderPdfForSide($controller, $side))) {
            $this->event->lock();

            if (!$this->event->hasPDF('therapy_application')) {
                $wk = Yii::app()->puppeteer;

                $wk->setDocuments(1);
                $wk->setDocRef($this->event->docref);
                $wk->setPatient($this->event->episode->patient);
                $wk->setBarcode($this->event->barcodeSVG);

                $wk->savePageToPDF(
                    $this->event->imageDirectory,
                    'event',
                    'therapy_application',
                    'http://localhost/OphCoTherapyapplication/default/renderPdfForSide?event_id=' . $this->event->id . '&side='.$side,
                    false
                );
            }

            $this->event->unlock();

            if (@$_GET['html']) {
                return Yii::app()->end();
            }

            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                'LOCAL',
                $this->event->episode->patient->id,
                $this->event->institution_id,
                $this->event->site_id
            );

            $pfile = ProtectedFile::createForWriting('ECForm - ' . $side . ' - ' .
                PatientIdentifierHelper::getIdentifierValue($primary_identifier) . '.pdf');

            if (!@copy($this->event->getPDF('therapy_application'), $pfile->getPath())) {
                throw new Exception('Unable to write to file: ' . $pfile->getPath());
            }

            if (!$pfile->save()) {
                throw new Exception('Unable to save file: ' . print_r($pfile->errors, true));
            }

            return $pfile;
        }

        return;
    }

    /**
     * generate the email text for the given side.
     *
     * @param CController $controller
     * @param array $template_data
     * @param string $side
     *
     * @return string
     */
    protected function generateEmailForSide(CController $controller, array $template_data, $side)
    {
        if ($template_data['compliant']) {
            $file = 'email_compliant.php';
        } else {
            $file = 'email_noncompliant.php';
        }

        $view = $this->getViewPath() . DIRECTORY_SEPARATOR . $file;

        return $controller->renderInternal($view, $template_data, true);
    }

    /**
     * @return array
     */
    private function getTemplateData()
    {
        // at the moment we are using a fixed type of commissioning body, but it's possible that in the future this
        // might need to be determined in a more complex fashion, so we pass the type through to the templates
        $cbody_type = CommissioningBodyType::model()->findByPk(1);

        return array(
            'event' => $this->event,
            'patient' => $this->event->episode->patient,
            'cbody_type' => $cbody_type,
            'diagnosis' => $this->getElement('Element_OphCoTherapyapplication_Therapydiagnosis'),
            'suitability' => $this->getElement('Element_OphCoTherapyapplication_PatientSuitability'),
            'service_info' => $this->getElement('Element_OphCoTherapyapplication_MrServiceInformation'),
            'exceptional' => $this->getElement('Element_OphCoTherapyapplication_ExceptionalCircumstances'),
        );
    }

    /**
     * @param string $side
     *
     * @return array
     */
    private function getSideSpecificTemplateData($side)
    {
        $suitability = $this->getElement('Element_OphCoTherapyapplication_PatientSuitability');

        return array(
            'side' => $side,
            'treatment' => $suitability->{"${side}_treatment"},
            'compliant' => $suitability->{"${side}_nice_compliance"},
        );
    }

    /**
     * @param CController $controller
     * @param array $template_data
     * @param int $eye_id
     * @param User $notify_user
     *
     * @throws Exception
     *
     * @return bool
     */
    private function processEventForEye(CController $controller, array $template_data, $eye_id, User $notify_user = null)
    {
        $eye_name = $this->getEyeNameById($eye_id);
        $template_data += $this->getSideSpecificTemplateData($eye_name);

        $attachments = array();
        $attach_size = 0;

        if (($app_file = $this->createAndSavePdfForSide($controller, $template_data, $eye_name))) {
            $attachments[] = $app_file;
            $attach_size += $app_file->size;
        }

        if (($ec = $this->getElement('Element_OphCoTherapyapplication_ExceptionalCircumstances'))) {
            foreach ($ec->{"${eye_name}_filecollections"} as $fc) {
                $attachments[] = $fc->getZipFile();
                $attach_size += $fc->getZipFile()->size;
            }
        }

        $service_info = $this->getServiceInfo();

        $link_to_attachments = ($attach_size > Helper::convertToBytes(SettingMetadata::model()->getSetting('OphCoTherapyapplication_email_size_limit')));

        $template_data['link_to_attachments'] = $link_to_attachments;
        $email_text = $this->generateEmailForSide($controller, $template_data, $eye_name);

        $message = Yii::app()->mailer->newMessage();
        if ($template_data['compliant']) {
            $recipient_type = 'Compliant';
            $message->setSubject(SettingMetadata::model()->getSetting('OphCoTherapyapplication_compliant_email_subject'));
        } else {
            $recipient_type = 'Non-compliant';
            $message->setSubject(SettingMetadata::model()->getSetting('OphCoTherapyapplication_noncompliant_email_subject'));
        }

        $recipient_type = $template_data['compliant'] ? 'Compliant' : 'Non-compliant';

        try {
            $recipients = $this->getEmailRecipients($service_info, $recipient_type);
        } catch (Exception $e) {
            Yii::app()->user->setFlash('error', $e->getMessage());
            $controller->redirect('/OphCoTherapyapplication/default/view/' . $this->event->id);
        }

        $email_recipients = array();

        foreach ($recipients as $recipient) {
            if (!$recipient->isAllowed()) {
                throw new Exception("Recipient email address $recipient->recipient_email is not in the list of allowed domains");
            }
            $email_recipients[$recipient->recipient_email] = $recipient->recipient_name;
        }

        $message->setFrom(SettingMetadata::model()->getSetting('OphCoTherapyapplication_sender_email'));
        $message->setTo($email_recipients);

        if ($notify_user && $notify_user->email) {
            $cc = true;
            if (SettingMetadata::model()->getSetting('restrict_email_domains')) {
                $domain = preg_replace('/^.*?@/', '', $notify_user->email);
                if (!in_array($domain, SettingMetadata::model()->getSetting('restrict_email_domains'))) {
                    Yii::app()->user->setFlash('warning.warning', 'You will not receive a copy of the submission because your email address ' . $notify_user->email . ' is not on a secure domain');
                    $cc = false;
                }
            }
            if ($cc) {
                $message->setCc($notify_user->email);
            }
        }

        $message->setBody($email_text);

        if (!$link_to_attachments) {
            foreach ($attachments as $att) {
                $message->attach(Swift_Attachment::fromPath($att->getPath())->setFilename($att->name));
            }
        }

        $sender_address = SenderEmailAddresses::getSenderAddress(SettingMetadata::model()->getSetting('OphCoTherapyapplication_sender_email'), $this->event->institution_id, $this->event->site_id);
        $sender_address->prepareMailer();

        if (Yii::app()->mailer->sendMessage($message)) {
            $email = new OphCoTherapyapplication_Email();
            $email->event_id = $this->event->id;
            $email->eye_id = $eye_id;
            $email->email_text = $email_text;

            if (!$email->save()) {
                throw new Exception('Unable to save email: ' . print_r($email->getErrors(), true));
            }

            $email->addAttachments($attachments);

            $this->event->audit('therapy-application', 'submit');

            $this->event->info = self::STATUS_SENT;

            if (!$this->event->save()) {
                throw new Exception('Unable to save event: ' . print_r($this->event->getErrors(), true));
            }

            return true;
        } else {
            OELog::log("Failed to send email for therapy application event_id '{$this->event->id}', eye_id '{$eye_id}'");

            // clean up
            if ($app_file) {
                $app_file->delete();
            }

            return false;
        }
    }

    /**
     * @param $eye_id
     *
     * @return string
     *
     * @throws Exception
     */
    public function getEyeNameById($eye_id)
    {
        switch ($eye_id) {
            case Eye::LEFT:
                $eye_name = 'left';
                break;
            case Eye::RIGHT:
                $eye_name = 'right';
                break;
            default:
                throw new Exception("Invalid eye ID: '$eye_id'");
        }

        return $eye_name;
    }

    private function getServiceInfo()
    {
        if (!$service_info = $this->getElement('Element_OphCoTherapyapplication_MrServiceInformation')) {
            throw new Exception('MrServiceInformation element is missing');
        }

        return $service_info;
    }

    private function getEmailRecipients($service_info, $recipient_type)
    {
        if (!$recipients = OphCoTherapyapplication_Email_Recipient::model()->with('type')->findAll('(site_id = ? or site_id is null) and (type.id is null or type.name = ?)', array($service_info->site_id, $recipient_type))) {
            throw new Exception('No email recipient defined for site ' . $service_info->site->name . ", $recipient_type");
        }

        return $recipients;
    }

    public function hasEmailRecipients()
    {
        try {
            $recipient_type = (!$this->isEventNonCompliant()) ? 'Compliant' : 'Non-compliant';
            $this->getEmailRecipients($this->getServiceInfo(), $recipient_type);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSiteName()
    {
        $service_info = $this->getServiceInfo();

        return $service_info->site->name;
    }
}
