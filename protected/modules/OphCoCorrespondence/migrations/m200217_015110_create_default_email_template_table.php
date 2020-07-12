<?php

class m200217_015110_create_default_email_template_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophcocorrespondence_default_recipient_email_templates', array(
            'id' => 'pk',
            'recipient_type' => 'varchar(20) NOT NULL',
            'email_body' => 'text',
        ), true);

        $patientEmailBody = 'Dear Mr X,<br/>Attached to this email (as a PDF file) is a letter to you from [Hospital Name]. These letters tend to be copies of clinic letters after you have attended an appointment.<br/><br/>Please do not reply to this email, as the account is not monitored. If you have any questions about the validity of this message or wish to stop receiving copies of your letters via email, please contact us at:<br/>[email address]';

        $optometristEmailBody = 'Dear Mr X,<br/>Attached to this email (as a PDF) is a letter regarding one of your patients from [Hospital Name]. This email has been sent from an unmonitored NHSmail account. Please contact us on [contact number] if you have any questions or concerns.';

        $internalReferralEmailBody = 'Dear Mr X,<br/>Please see the attached referral letter.<br/>Please do not reply to this email - this email address is not monitored.<br/>In case of any queries, please contact the individual who has signed the attached referral letter.';

        $gpEmailBody = 'Please see the attached letter regarding [patient name], who is registered at your practice.<br/>THis letter is from [Hospital Name].<br/>Please do not reply to this email. Contact details are provided within the letter.';

        $drssEmailBody = 'Dear [Commissioning Body Name],<br/>Attached to this email is a letter from *institution name* to update you about the ophthalmic status of a patient. Please do not reply to this email address. Contact details are provided within the letter.';

        $this->insertMultiple('ophcocorrespondence_default_recipient_email_templates', [
            ['recipient_type' => 'PATIENT', 'email_body' => $patientEmailBody],
            ['recipient_type' => 'OPTOMETRIST', 'email_body' => $optometristEmailBody],
            ['recipient_type' => 'INTERNALREFERRAL', 'email_body' => $internalReferralEmailBody],
            ['recipient_type' => 'GP', 'email_body' => $gpEmailBody],
            ['recipient_type' => 'DRSS', 'email_body' => $drssEmailBody],
        ]);
    }

    public function safeDown()
    {
        $this->dropOETable('ophcocorrespondence_default_recipient_email_templates', true);
    }
}
