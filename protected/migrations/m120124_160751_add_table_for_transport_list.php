<?php

class m120124_160751_add_table_for_transport_list extends CDbMigration
{
	public function up()
	{
		$this->createTable('transport_list', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'item_table' => 'varchar(40) CHARACTER SET utf8 NOT NULL',
			'item_id' => 'int(10) unsigned NOT NULL',
			'status' => 'int(1) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
		),'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('transport_list');
	}
}
