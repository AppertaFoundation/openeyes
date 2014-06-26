<?php

class m140626_143836_child_events extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_type','parent_id','int(10) unsigned NULL');
		$this->createIndex('event_type_parent_id_fk','event_type','parent_id');
		$this->addForeignKey('event_type_parent_id_fk','event_type','parent_id','event_type','id');
	}

	public function down()
	{
		$this->dropForeignKey('event_type_parent_id_fk','event_type');
		$this->dropIndex('event_type_parent_id_fk','event_type');
		$this->dropColumn('event_type','parent_id');
	}


}