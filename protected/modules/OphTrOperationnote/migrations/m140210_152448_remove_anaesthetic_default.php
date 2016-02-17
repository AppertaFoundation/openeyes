<?php

class m140210_152448_remove_anaesthetic_default extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtroperationnote_anaesthetic','anaesthetic_type_id','int(10) unsigned not null');
	}

	public function down()
	{
		$this->alterColumn('et_ophtroperationnote_anaesthetic','anaesthetic_type_id','int(10) unsigned not null default 1');
	}
}
