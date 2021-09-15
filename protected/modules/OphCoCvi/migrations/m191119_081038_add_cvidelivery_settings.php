<?php

class m191119_081038_add_cvidelivery_settings extends OEMigration
{

    public function up()
    {
        $institution = $this->getDbConnection()->createCommand()
            ->select('*')
            ->from('institution')
            ->join('contact c', 'c.id = institution.contact_id')
            ->join('address a', 'a.contact_id = c.id')
            ->where('remote_id = :institution_code')
            ->bindValues([':institution_code' => \Yii::app()->params['institution_code']])
            ->queryRow();

        $address = $institution['address1'] . ", " . ($institution['address2'] ? "{$institution['address2']}, " : "") . ", {$institution['city']}, {$institution['postcode']}";


        $this->alterColumn("setting_metadata", "default_value", "TEXT");
        $this->alterColumn("setting_metadata_version", "default_value", "TEXT");

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_sender_email",
            "name" => "CVI delivery to LA: Sender email address",
            "default_value" => $institution['email'],
            "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_sender_name",
            "name" => "CVI delivery to LA: Sender name",
            "default_value" => "{$institution['name']} Certificate of Vision Impairment team",
            "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_subject",
            "name" => "CVI delivery to LA: Subject",
            "default_value" => "New Referral (CVI) for blind register",
            "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_body",
            "name" => "CVI delivery to LA: Body",
            "field_type_id" => $this->getSettingFieldIdByName('Textarea'),
            "default_value" =>
        <<<EOS
Dear Team,

Please find attached a Certificate of Visual Impairment for your resident.

Please contact the resident and gain their consent to be added to the blind register and offer an Visual Impairment assessment as required.

If you can't read the attachment, contact {$institution['name']} {$institution['primary_phone']}.

Please do not reply to this email as it is not monitored.

Certificate of Vision Impairment team.
{$institution['name']}
{$address}
Phone: {$institution['primary_phone']}
EOS
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_sender_email",
            "name" => "CVI delivery to RCOP: Sender email address",
            "default_value" => "",
            "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_sender_name",
            "name" => "CVI delivery to RCOP: Sender name",
            "default_value" => "Certificate of Vision Impairment team",
            "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_subject",
            "name" => "CVI delivery to RCOP: Subject",
            "default_value" => "CVI from {$institution['name']}",
            "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
        ));

         $this->insert("setting_metadata", array(
             "element_type_id" => null,
             "key" => "cvidelivery_rcop_to_email",
             "name" => "CVI delivery to RCOP: To email address",
             "default_value" => "",
             "field_type_id" => $this->getSettingFieldIdByName('Text Field'),
         ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_body",
            "name" => "CVI delivery to RCOP: Body",
            "field_type_id" => $this->getSettingFieldIdByName('Textarea'),
            "default_value" =>
        <<<EOS
Dear team,

Please find attached CVI for research.

If you can't read the attachment, contact {$institution['name']} {$institution['primary_phone']}.

Please do not reply to this email as it is not monitored.

Certificate of Vision Impairment team.
{$institution['name']}
{$address}
Phone: {$institution['primary_phone']}
EOS
        ));
    }

    public function down()
    {
        $this->execute("DELETE FROM setting_metadata WHERE `key` IN (
                                'cvidelivery_la_sender_email',
                                'cvidelivery_la_sender_name',
                                'cvidelivery_la_subject',
                                'cvidelivery_la_body',
                                'cvidelivery_rcop_sender_email',
                                'cvidelivery_rcop_sender_name',
                                'cvidelivery_rcop_subject',
                                'cvidelivery_rcop_body'
                        )");

        $this->alterColumn("setting_metadata", "default_value", "VARCHAR(64)");
        $this->alterColumn("setting_metadata_version", "default_value", "VARCHAR(64)");
    }
}
