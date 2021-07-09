<?php

class m191003_065001_remove_legacy_consent_signature extends CDbMigration
{
	public function up()
	{
	    $this->execute("DELETE FROM element_type
            WHERE class_name = :class_name
            AND event_type_id = (SELECT id FROM event_type WHERE `name` = 'CVI')
            AND version = 1",
            array(
                ":class_name" => "OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsentSignature"
            ));
	}

	public function down()
	{
		$this->execute("INSERT INTO openeyes.element_type 
		    (name, class_name, last_modified_user_id, last_modified_date, created_user_id, created_date, event_type_id, display_order, `default`, parent_element_type_id, required, version)
		    VALUES ('Consent Signature', 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsentSignature', 1, '2019-10-03 08:48:04', 1, '2019-10-03 08:48:04', 23, 20, 1, null, 1, 1);");
	}
}