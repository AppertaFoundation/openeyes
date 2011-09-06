<?php

class m110905_125508_update_cancelled_operation_table extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('operation_1','cancelled_operation');
		$this->dropForeignKey('operation_2','cancelled_operation');
		$this->truncateTable('cancelled_operation');
		$this->dropTable('cancelled_operation');

		$this->createTable('cancelled_operation', array(
			'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'cancelled_date' => 'datetime',
			'user_id' => 'int(10) unsigned NOT NULL',
			'cancelled_reason_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY (`cancelled_reason_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->addForeignKey('operation_1','cancelled_operation','cancelled_reason_id','cancellation_reason','id');
		$this->addForeignKey('operation_2','cancelled_operation','element_operation_id','element_operation','id');
	}

	public function down()
	{
		$this->dropForeignKey('operation_1','cancelled_operation');
		$this->dropForeignKey('operation_2','cancelled_operation');
		$this->truncateTable('cancelled_operation');
		$this->dropTable('cancelled_operation');

		$this->createTable('cancelled_operation', array(
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'cancelled_date' => 'datetime',
			'user_id' => 'int(10) unsigned NOT NULL',
			'cancelled_reason_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`element_operation_id`)',
			'KEY (`cancelled_reason_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->addForeignKey('operation_1','cancelled_operation','cancelled_reason_id','cancellation_reason','id');
		$this->addForeignKey('operation_2','cancelled_operation','element_operation_id','element_operation','id');
	}
}