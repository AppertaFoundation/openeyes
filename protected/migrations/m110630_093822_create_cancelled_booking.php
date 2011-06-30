<?php

class m110630_093822_create_cancelled_booking extends CDbMigration
{
	public function up()
	{
		$this->createTable('cancelled_booking', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'date' => 'DATE NOT NULL',
			'start_time' => 'TIME NOT NULL',
			'end_time' => 'TIME NOT NULL',
			'theatre_id' => 'int(10) unsigned NOT NULL',
			'cancelled_date' => 'datetime',
			'user_id' => 'int(10) unsigned NOT NULL',
			'cancelled_reason_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY (`element_operation_id`)',
			'KEY (`cancelled_reason_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->addColumn('element_operation', 'status', 'int(10) unsigned NOT NULL');
		
		$this->createTable('cancelled_operation', array(
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'cancelled_date' => 'datetime',
			'user_id' => 'int(10) unsigned NOT NULL',
			'cancelled_reason_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`element_operation_id`)',
			'KEY (`cancelled_reason_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('cancelled_reason', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'text' => "varchar(255) NOT NULL DEFAULT ''",
			'parent_id' => 'int(10) unsigned DEFAULT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->addForeignKey('booking_1','cancelled_booking','cancelled_reason_id','cancelled_reason','id');
		$this->addForeignKey('operation_1','cancelled_operation','cancelled_reason_id','cancelled_reason','id');
		$this->addForeignKey('operation_2','cancelled_operation','element_operation_id','element_operation','id');
	}

	public function down()
	{
		$this->dropForeignKey('operation_1','cancelled_operation');
		$this->dropForeignKey('operation_2','cancelled_operation');
		$this->dropForeignKey('booking_1','cancelled_booking');
		
		$this->dropColumn('element_operation', 'status');
		
		$this->dropTable('cancelled_reason');
		$this->dropTable('cancelled_operation');
		$this->dropTable('cancelled_booking');
	}
}