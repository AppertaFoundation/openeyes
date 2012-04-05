<?php

class m120405_130850_default_to_topical_anaesthetic_delivery extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtroperationnote_anaesthetic','anaesthetic_delivery_id',"int(10) unsigned NOT NULL DEFAULT '5'");
	}

	public function down()
	{
		$this->alterColumn('et_ophtroperationnote_anaesthetic','anaesthetic_delivery_id',"int(10) unsigned NOT NULL DEFAULT '1'");
	}
}
