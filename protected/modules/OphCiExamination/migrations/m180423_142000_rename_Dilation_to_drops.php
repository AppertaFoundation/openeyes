<?php

class m180423_142000_rename_Dilation_to_drops extends CDbMigration
{
	public function up()
	{
	    $this->execute("UPDATE element_type SET `name` = 'Drops / Meds administered' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_Dilation'");
	}

	public function down()
	{
        $this->execute("UPDATE element_type SET `name` = 'Dilation' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_Dilation'");
	}

}
