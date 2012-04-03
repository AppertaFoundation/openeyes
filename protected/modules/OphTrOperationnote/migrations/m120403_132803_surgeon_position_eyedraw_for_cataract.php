<?php

class m120403_132803_surgeon_position_eyedraw_for_cataract extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationnote_cataract','eyedraw2','varchar(4096) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationnote_cataract','eyedraw2');
	}
}
