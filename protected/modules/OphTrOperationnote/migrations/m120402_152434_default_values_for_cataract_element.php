<?php

class m120402_152434_default_values_for_cataract_element extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtroperationnote_cataract','vision_blue',"tinyint(1) unsigned NOT NULL DEFAULT '1'");
		$this->alterColumn('et_ophtroperationnote_cataract','report',"varchar(4096) COLLATE utf8_bin NOT NULL DEFAULT 'Continuous Circular Capsulorrhexis
Hydrodissection
Phakoemulsification of lens nucleus
Aspiration of soft lens matter'");
	}

	public function down()
	{
		$this->alterColumn('et_ophtroperationnote_cataract','vision_blue',"tinyint(1) unsigned NOT NULL DEFAULT '0'");
		$this->alterColumn('et_ophtroperationnote_cataract','report',"varchar(4096) COLLATE utf8_bin NOT NULL");
	}
}
