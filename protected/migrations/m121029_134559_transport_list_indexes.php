<?php

class m121029_134559_transport_list_indexes extends CDbMigration
{
	public function up()
	{
		$this->createIndex('transport_list_item_table_fk','transport_list','item_table');
		$this->createIndex('transport_list_item_id_fk','transport_list','item_id');
	}

	public function down()
	{
		$this->dropIndex('transport_list_item_table_fk','transport_list');
		$this->dropIndex('transport_list_item_id_fk','transport_list');
	}
}
