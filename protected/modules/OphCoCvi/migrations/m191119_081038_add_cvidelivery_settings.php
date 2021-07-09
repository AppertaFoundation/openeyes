<?php

class m191119_081038_add_cvidelivery_settings extends CDbMigration
{
	public function up()
	{
	    $this->alterColumn("setting_metadata", "default_value", "TEXT");
	    $this->alterColumn("setting_metadata_version", "default_value", "TEXT");

	    $this->insert("setting_metadata", array(
	        "element_type_id" => null,
            "key" => "cvidelivery_la_sender_email",
            "name" => "CVI delivery to LA: Sender email address",
            "default_value" => "moorfields.noreplycvi@nhs.net",
            "field_type_id" => 4,
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_sender_name",
            "name" => "CVI delivery to LA: Sender name",
            "default_value" => "Moorfields Certificate of Vision Impairment team",
            "field_type_id" => 4,
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_subject",
            "name" => "CVI delivery to LA: Subject",
            "default_value" => "New Referral (CVI) for blind register",
            "field_type_id" => 4,
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_la_body",
            "name" => "CVI delivery to LA: Body",
            "field_type_id" => 5,
            "default_value" =>
<<<EOS
Dear Team,

Please find attached a Certificate of Visual Impairment for your resident.

Please contact the resident and gain their consent to be added to the blind register and offer an Visual Impairment assessment as required.

If you can't read the attachment, contact Moorfields 0207 566 2355.

Please do not reply to this email as it is not monitored.

Moorfields Certificate of Vision Impairment team.
Moorfields Eye Hospital NHS Foundation Trust
City Road, London, EC1V 2PD
Phone: 0207 566 2355
EOS
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_sender_email",
            "name" => "CVI delivery to RCOP: Sender email address",
            "default_value" => "moorfields.noreplycvi@nhs.net",
            "field_type_id" => 4,
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_sender_name",
            "name" => "CVI delivery to RCOP: Sender name",
            "default_value" => "Moorfields Certificate of Vision Impairment team",
            "field_type_id" => 4,
        ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_subject",
            "name" => "CVI delivery to RCOP: Subject",
            "default_value" => "CVI from Moorfields",
            "field_type_id" => 4,
        ));

         $this->insert("setting_metadata", array(
             "element_type_id" => null,
             "key" => "cvidelivery_rcop_to_email",
             "name" => "CVI delivery to RCOP: To email address",
             "default_value" => "meh-tr.CVI@nhs.net",
             "field_type_id" => 4,
         ));

        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "cvidelivery_rcop_body",
            "name" => "CVI delivery to RCOP: Body",
            "field_type_id" => 5,
            "default_value" =>
<<<EOS
Dear team,

Please find attached CVI for research.

If you can't read the attachment, contact Moorfields 0207 566 2355.

Please do not reply to this email as it is not monitored.

Moorfields Certificate of Vision Impairment team.
Moorfields Eye Hospital NHS Foundation Trust
City Road, London, EC1V 2PD
Phone: 0207 566 2355
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