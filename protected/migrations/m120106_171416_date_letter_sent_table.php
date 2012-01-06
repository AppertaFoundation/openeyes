<?php

class m120106_171416_date_letter_sent_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('date_letter_sent',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'date_invitation_letter_sent' => 'datetime NULL',
			'date_1st_reminder_letter_sent' => 'datetime NULL',
			'date_2nd_reminder_letter_sent' => 'datetime NULL',
			'date_gp_letter_sent' => 'datetime NULL',
			'date_scheduling_letter_sent' => 'datetime NULL',
			'PRIMARY KEY (`id`)',
			'KEY (`element_operation_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->addForeignKey('date_letter_sent_element_operation_fk','date_letter_sent','element_operation_id','element_operation','id');
	}

	public function down()
	{
		$this->dropForeignKey('date_letter_sent_element_operation_fk','date_letter_sent');
		$this->dropTable('date_letter_sent');
	}
}
