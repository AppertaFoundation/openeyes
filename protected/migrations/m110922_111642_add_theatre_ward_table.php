<?php

class m110922_111642_add_theatre_ward_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('theatre_ward_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'theatre_id' => 'int(10) unsigned NOT NULL',
			'ward_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `theatre_id` (`theatre_id`)',
			'KEY `ward_id` (`ward_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->addForeignKey('theatre_ward_assignment_1','theatre_ward_assignment','theatre_id','theatre','id');
		$this->addForeignKey('theatre_ward_assignment_2','theatre_ward_assignment','ward_id','ward','id');	
	}

	public function down()
	{
		$this->dropForeignKey('theatre_ward_assignment_1','theatre_ward_assignment');
		$this->dropForeignKey('theatre_ward_assignment_2','theatre_ward_assignment');
		
		$this->dropTable('theatre_ward_assignment');
	}
}