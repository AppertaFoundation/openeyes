<?php

class m120327_162132_correct_element_type_dropdown_defaults extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtroperationnote_vitrectomy','gauge_id','int(10) unsigned NOT NULL');
		$this->alterColumn('et_ophtroperationnote_tamponade','gas_type_id','int(10) unsigned NOT NULL');
		$this->alterColumn('et_ophtroperationnote_buckle','drainage_type_id','int(10) unsigned NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('et_ophtroperationnote_vitrectomy','gauge_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->alterColumn('et_ophtroperationnote_tamponade','gas_type_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->alterColumn('et_ophtroperationnote_buckle','drainage_type_id','int(10) unsigned NOT NULL DEFAULT 1');
	}
}
