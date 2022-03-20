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

class CorrespondenceEmailManager
{
    protected $contactTypes = array('INTERNALREFERRAL', 'GP', 'OPTOMETRIST', 'PATIENT', 'DRSS', 'OTHER');
    protected $emailStatuses = array('PENDING_RETRY', 'COMPLETE', 'FAILED', 'PENDING', 'SENDING');
    protected $filePath;
    public $isConsole = false;

    public function actionSendEmail($event_id){
        if (isset($event_id)) {
            $recipients = $this->getEmailRecipients($event_id);
            $this->actionSendEmailToRecipients($recipients);
        }
    }

    public function getEmailRecipients($eventId) {
        try {
            $recipients = Yii::app()->db->createCommand()
                ->select('do.id')
                ->from('document_instance di')
                ->join('document_target dt', 'dt.document_instance_id = di.id')
                ->join('document_output do', 'do.document_target_id = dt.id')
                ->where('di.correspondence_event_id = :id and lower(do.output_type) = lower(:output_type)', array(':id' => $eventId, ':output_type' => 'Email'))
                ->andWhere(['in', 'do.output_status', [$this->emailStatuses[0], $this->emailStatuses[2], $this->emailStatuses[4]]])
                ->andWhere(['in', 'dt.contact_type', $this->contactTypes])
                ->queryAll();

            return $recipients;

        } catch (Exception $e) {
            OELog::logException($e);
        }
    }

    public function getDelayedEmailRecipients() {
        try {
            $recipients = Yii::app()->db->createCommand()
                ->select('do.id')
                ->from('et_ophcocorrespondence_letter eol')
                ->join('document_instance di','eol.event_id = di.correspondence_event_id')
                ->join('document_target dt', 'dt.document_instance_id = di.id')
                ->join('document_output do', 'do.document_target_id = dt.id')
                ->where('lower(do.output_type) = lower(:output_type) and lower(do.output_status) = lower(:output_status)', array(':output_type' => 'Email (Delayed)', ':output_status' => $this->emailStatuses[3]))
                ->andWhere(['in', 'dt.contact_type', $this->contactTypes])
                ->andWhere('eol.last_modified_date <= DATE_ADD(NOW() , INTERVAL - ' . SettingMetadata::model()->getSetting('correspondence_delayed_email_processing') . ' MINUTE)')
                ->order('di.correspondence_event_id asc')
                ->queryAll();

            return $recipients;

        } catch (Exception $e) {
            OELog::logException($e);
        }
    }

    public function getRecipientsDataDocumentOutputId($recipientsDocumentOutputId) {
        try {
            $recipients = Yii::app()->db->createCommand()
                ->select('di.correspondence_event_id, dt.id document_target_id, dt.contact_type, dt.contact_id, dt.email, do.id document_output_id, do.output_type, do.output_status')
                ->from('document_instance di')
                ->join('document_target dt', 'dt.document_instance_id = di.id')
                ->join('document_output do', 'do.document_target_id = dt.id')
                ->where(['in', 'do.id', $recipientsDocumentOutputId])
                ->queryAll();

            return $recipients;

        } catch (Exception $e) {
            OELog::logException($e);
        }
    }

    public function actionSendEmailToRecipients($recipients)
    {
        if ($recipients) {
            $recipientsData = $this->getRecipientsDataDocumentOutputId(array_column($recipients, 'id'));
            // Get the Institution Id
            $institutionId = Institution::model()->find('remote_id = "'.  Yii::app()->params['institution_code'] . '"')->id;

            foreach ($recipientsData as $recipient) {
                $shouldAppendSubject = false;
                $eventId = $recipient['correspondence_event_id'];
                $outputStatus = $recipient['output_status'];
                $contactType = $recipient['contact_type'];
                $contactId = $recipient['contact_id'];
                $recipientEmail = $recipient['email'];
                $documentOutputId = $recipient['document_output_id'];
                $documentTargetId = $recipient['document_target_id'];

                $properties['event_id'] = $eventId;
                $event = Event::model()->findByPk($eventId);
                $properties['episode_id'] = $event->episode_id;
                $properties['patient_id'] = $event->episode->patient_id;

                $this->trace('Processing event ' . $eventId . PHP_EOL);

                if($outputStatus === 'PENDING_RETRY') {
                    $shouldAppendSubject = true;
                }

                if ($contactType === $this->contactTypes[0]) {
                    $elementLetter = ElementLetter::model()->find('event_id = '. $eventId);
                    $email = $elementLetter->getInternalReferralEmail();
                } else {
                    $email = $recipientEmail;
                }

                if(isset($email)) {
                    $this->filePath = "";
                    $this->savePdfFile($eventId, $documentTargetId);

                    $siteId = ElementLetter::model()->find('event_id = '. $eventId)->site_id;
                    $this->getEmailTemplateAndSendEmail($contactType, $email, $institutionId, $siteId, $eventId, $documentOutputId, $shouldAppendSubject, $properties);
                } else {
                    $msg = "Email not found in the system.";
                    $this->log($msg, $this->emailStatuses[2], $documentOutputId, $properties);
                }
            }
        }
    }

    public function savePdfFile($eventId, $documentTargetId) {
        $event = Event::model()->findByPk($eventId);
        $this->checkPath($event->getImageDirectory());

        $filePath = $event->getImageDirectory() . '/event_email_' . $documentTargetId . '_' . $eventId . '.pdf';

        $login_page = Yii::app()->params['docman_login_url'];
        $username = Yii::app()->params['docman_user'];
        $password = Yii::app()->params['docman_password'];
        $print_url = Yii::app()->params['docman_print_url'];
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
//                'LoginForm[YII_CSRF_TOKEN]' => $token[0],
//                'YII_CSRF_TOKEN' => $token[0],
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        curl_exec($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $print_url . $eventId . '?document_target_id=' . $documentTargetId);
        $content = curl_exec($ch);
        curl_close($ch);

        if (substr($content, 0, 4) !== "%PDF") {
            $this->trace('File is not a PDF for event id: '. $eventId . PHP_EOL);
            return false;
        }

        $f = fopen($filePath, 'w');
        $fwrite = fwrite($f,$content);
        fclose($f);

        if (!$fwrite) {
            $this->trace('Error Generating Pdf ' . $filePath . ' failed' . PHP_EOL);
            return false;
        }

        $this->filePath = $filePath;
    }

    /**
     * Create directory if not exist
     * @param $path
     */
    private function checkPath($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            $this->trace("ALERT! Directory " . $path . " has been created!". PHP_EOL);
        }
    }

    private function getEmailTemplateAndSendEmail($contactType, $email, $institutionId, $siteId, $eventId, $documentOutputId, $shouldAppendSubject, $properties) {
        if (isset($email)) {
            $emailTemplate = $this->getEmailTemplateForRecipient($contactType, $institutionId, $siteId);
            if ( isset($emailTemplate) ) {
                $senderAddress = $this->getSenderAddress($email, $institutionId, $siteId);

                if ( isset($senderAddress) ) {
                    $this->sendEmail($senderAddress, $email, $emailTemplate['subject'], $emailTemplate['body'], $eventId, $documentOutputId, $shouldAppendSubject, $properties);
                } else {
                    $msg = "Email is not configured";
                    $this->log($msg, $this->emailStatuses[2], $documentOutputId, $properties);
                }
            } else {
                // if email template is not found in the system, then update the document output status to FAILED and add an entry to Audit.
                $msg = "Email Template for " . $contactType . " does not exist in the system.";
                $this->log($msg, $this->emailStatuses[2], $documentOutputId, $properties);
            }
        }
    }

    /**
     * @param $email
     * @param $institutionId
     * @param $siteId
     * @return mixed|null
     * @throws CException
     */
    private function getSenderAddress($email, $institutionId, $siteId) {
        $command = Yii::app()->db->createCommand();
        // Algorithm for matching

        // get domain from email.
        $emailDomain = substr($email, strpos($email, '@'));

        // Get the sender email address for the same institution and site id
        $query = $command
            ->select('host, username, password, reply_to_address, port, security')
            ->from('ophcocorrespondence_sender_email_addresses osea')
            ->where('osea.institution_id = :institution_id and osea.site_id = :site_id and osea.domain = :domain',
                array('institution_id' => $institutionId, 'site_id' => $siteId, 'domain' => $emailDomain))
            ->queryRow();
        if (!(empty($query))) {
            return $query;
        } else {
            $command->reset();
            $query = $command
                ->select('host, username, password, reply_to_address, port, security')
                ->from('ophcocorrespondence_sender_email_addresses osea')
                ->where('osea.institution_id = :institution_id and osea.site_id = :site_id and osea.domain = :domain',
                    array('institution_id' => $institutionId, 'site_id' => $siteId, 'domain' => '*'))
                ->queryRow();
            if (!(empty($query))) {
                return $query;
            } else {
                $command->reset();
                $query = $command
                    ->select('host, username, password, reply_to_address, port, security')
                    ->from('ophcocorrespondence_sender_email_addresses osea')
                    ->where('osea.institution_id IS NULL and osea.site_id = :site_id and osea.domain = :domain',
                        array('site_id' => $siteId, 'domain' => $emailDomain))
                    ->queryRow();
                if (!(empty($query))) {
                    return $query;
                } else {
                    $command->reset();
                    $query = $command
                        ->select('host, username, password, reply_to_address, port, security')
                        ->from('ophcocorrespondence_sender_email_addresses osea')
                        ->where('osea.institution_id IS NULL and osea.site_id = :site_id and osea.domain = :domain',
                            array('site_id' => $siteId, 'domain' => '*'))
                        ->queryRow();
                    if (!(empty($query))) {
                        return $query;
                    } else {
                        $command->reset();
                        $query = $command
                            ->select('host, username, password, reply_to_address, port, security')
                            ->from('ophcocorrespondence_sender_email_addresses osea')
                            ->where('osea.institution_id = :institution_id and osea.site_id IS NULL and osea.domain = :domain',
                                array('institution_id' => $institutionId, 'domain' => $emailDomain))
                            ->queryRow();
                        if (!(empty($query))) {
                            return $query;
                        } else {
                            $command->reset();
                            $query = $command
                                ->select('host, username, password, reply_to_address, port, security')
                                ->from('ophcocorrespondence_sender_email_addresses osea')
                                ->where('osea.institution_id = :institution_id and osea.site_id IS NULL and osea.domain = :domain',
                                    array('institution_id' => $institutionId, 'domain' => '*'))
                                ->queryRow();
                            if (!(empty($query))) {
                                return $query;
                            } else {
                                $command->reset();
                                $query = $command
                                    ->select('host, username, password, reply_to_address, port, security')
                                    ->from('ophcocorrespondence_sender_email_addresses osea')
                                    ->where('osea.institution_id IS NULL and osea.site_id IS NULL and osea.domain = :domain',
                                        array('domain' => $emailDomain))
                                    ->queryRow();
                                if (!(empty($query))) {
                                    return $query;
                                } else {
                                    $command->reset();
                                    $query = $command
                                        ->select('host, username, password, reply_to_address, port, security')
                                        ->from('ophcocorrespondence_sender_email_addresses osea')
                                        ->where('osea.institution_id IS NULL and osea.site_id IS NULL and osea.domain = :domain',
                                            array('domain' => '*'))
                                        ->queryRow();
                                    if (!(empty($query))) {
                                        return $query;
                                    } else {
                                        return null;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function getEmailTemplateForRecipient($recipient, $institutionId, $siteId) {
        $command = Yii::app()->db->createCommand();

        // Check if there is any template that exists for the institution, the site and the recipient type.
        $query = $command
            ->select('*')
            ->from('ophcocorrespondence_email_template oet')
            ->where('oet.institution_id = :institution_id and oet.site_id = :site_id and oet.recipient_type = :recipient_type',
                array('institution_id' => $institutionId, 'site_id' => $siteId, 'recipient_type' => $recipient))
            ->queryRow();

        if (!(empty($query))) {
            return array('subject' => $query['subject'], 'body' => $query['body']);
        } else {
            $command->reset();
            // if not, then check the email template for the institution and the recipient type.
            $query = $command
                ->select('*')
                ->from('ophcocorrespondence_email_template oet')
                ->where('oet.institution_id = :institution_id and oet.site_id IS NULL and oet.recipient_type = :recipient_type',
                    array('institution_id' => $institutionId, 'recipient_type' => $recipient))
                ->queryRow();

            if (!(empty($query))) {
                return array('subject' => $query['subject'], 'body' => $query['body']);
            } else {
                $command->reset();
                // if not, then check the email template for the site and the recipient type.
                $query = $command->select('*')
                    ->from('ophcocorrespondence_email_template oet')
                    ->where('oet.institution_id IS NULL and oet.site_id = :site_id and oet.recipient_type = :recipient_type',
                        array('site_id' => $siteId, 'recipient_type' => $recipient))
                    ->queryRow();

                if (!(empty($query))) {
                    return array('subject' => $query['subject'], 'body' => $query['body']);
                } else {
                    $command->reset();
                    // if everything else fails, then get the default email template for the recipient (i.e. institution and site both are NULL)
                    $query = $command->select('*')
                        ->from('ophcocorrespondence_email_template oet')
                        ->where('oet.institution_id IS NULL and oet.site_id IS NULL and oet.recipient_type = :recipient_type',
                            array('recipient_type' => $recipient))
                        ->queryRow();

                    if (!(empty($query))) {
                        return array('subject' => $query['subject'], 'body' => $query['body']);
                    } else {
                        return null;
                    }
                }
            }
        }
    }

    private function sendEmail($senderAddress, $to, $subject, $body, $eventId, $documentOutputId, $shouldAppendSubject, $properties) {
        // Setting up the SMTP properties
        Yii::app()->mailer->setSmtpHost($senderAddress['host']);
        Yii::app()->mailer->setSmtpUsername($senderAddress['username']);
        // decrypt the password
        $encryptionDecryptionHelper = new EncryptionDecryptionHelper();
        $password = $encryptionDecryptionHelper->decryptData($senderAddress['password']);
        Yii::app()->mailer->setSmtpPassword($password);

        Yii::app()->mailer->setSmtpPort($senderAddress['port']);
        Yii::app()->mailer->setSmtpSecurity($senderAddress['security']);

        $replyToAddress = $senderAddress['reply_to_address'];

        $this->trace('Sending email to: ' . $to . PHP_EOL);

        $patientId = Event::model()->findByPk($eventId)->episode->patient_id;
        $patient = Patient::model()->findByPk($patientId);

        $body = OphCoCorrespondence_Substitution::replace($body, $patient);
        $subject = OphCoCorrespondence_Substitution::replace($subject, $patient);

        $attachment = array('filePath' => $this->filePath, 'fileName' => 'Letter.pdf');
        // Append update at the end of the subject if the email is already sent successfully to this recipient.
        $subject = $shouldAppendSubject ? $subject . " (Update)" : $subject;

        $result = Yii::app()->mailer->mail(array($to), $subject, $body, array($senderAddress['username'] =>'OpenEyes Reports'), $replyToAddress, $attachment, 'text/html');

        if(getType($result) == 'integer') {
            if($result == '1') {
                // success
                // if the email is sent successfully, then change the status of the email document output to COMPLETE.
                $msg = "Email Sent Successfully." . PHP_EOL . "From: ". $senderAddress['username'] . PHP_EOL . "To: ". $to;
                $this->log($msg, $this->emailStatuses[1], $documentOutputId, $properties);
            }
        } else {
            // Some error occurred.
            $msg = $result . PHP_EOL . "From: ". $senderAddress['username'] . PHP_EOL . "To: ". $to;
            $this->log($msg, $this->emailStatuses[2], $documentOutputId, $properties);
        }
    }

    private function trace($message) {
        $echo = $this->isConsole;
        if ($echo)
            echo $message . PHP_EOL;
    }

    private function log($msg, $emailStatus, $documentOutputId, $properties) {
        $this->trace($msg);
        // if the email is being sent via the cron job then set the user_id for the auditing
        // as the docman_user.
        if ($this->isConsole) {
            $user = User::model()->find('username = "docman_user"');
            $properties['user_id'] = $user->id;
        }
        $audit = Audit::add('event', 'email', str_replace(PHP_EOL, "<br/>", $msg), $msg, $properties);
        if ($audit) {
            $eventId = $properties['event_id'];
            if (isset($eventId)) {
                $event = Event::model()->findByPk($eventId);
                $audit->site_id = ElementLetter::model()->find('event_id =' . $eventId)->site_id;
                $audit->firm_id = $event->firm_id;
                $audit->save();
            }
        }
        $documentOutput = DocumentOutput::model()->findByPk($documentOutputId);
        $documentOutput->updateStatus($emailStatus, false, true);
        $documentOutput->save();
    }
}
