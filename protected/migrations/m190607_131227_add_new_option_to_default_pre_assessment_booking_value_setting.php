<?php

class m190607_131227_add_new_option_to_default_pre_assessment_booking_value_setting extends CDbMigration
{
	public function up()
	{
	    $this->update("setting_metadata",
            ["data" => serialize(array("1" => "Yes", "0" => "No" , "2" => "Not set"))],
            "`key`=:key", [":key" =>"pre_assessment_booking_default_value"]);
	}

	public function down()
	{
        $this->update("setting_metadata",
            ["data" => serialize(array("1" => "Yes", "0" => "No"))],
            "`key`=:key", [":key" =>"pre_assessment_booking_default_value"]);
	}
}