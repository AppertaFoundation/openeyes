<?php

class m191120_075239_add_setting_eclo_email_address extends CDbMigration
{
	public function up()
	{
	    $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "eclo_email",
            "name" => "ECLO email address",
            "default_value" => "moorfields.cityroadeclo@nhs.net",
            "field_type_id" => 4,
        ));
	}

	public function down()
	{
		$this->execute("DELETE FROM setting_metadata WHERE `key` = 'eclo_email'");
	}
}