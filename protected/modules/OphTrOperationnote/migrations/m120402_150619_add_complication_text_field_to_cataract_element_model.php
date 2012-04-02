<?php

class m120402_150619_add_complication_text_field_to_cataract_element_model extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationnote_cataract','complication_notes','varchar(4096) COLLATE utf8_bin NULL');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationnote_cataract','complication_notes');
	}
}
