<?php

class m140328_082747_add_rtt extends CDbMigration
{
	public function up()
	{
		$this->createTable('rtt', array(
						'id' => 'pk',
						'clock_start' => 'date NOT NULL',
						'clock_end' => 'date',
						'breach' => 'date NOT NULL',
						'referral_id' => 'int(10) unsigned NOT NULL',
						'active' => 'boolean',
						'comments' => 'text',
						'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
						'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
						'created_user_id' => 'int(10) unsigned DEFAULT 1',
						'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'"
				), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		$this->addForeignKey('rtt_lmui_fk',
				'rtt',
				'last_modified_user_id', 'user', 'id');
		$this->addForeignKey('rtt_cui_fk',
				'rtt',
				'created_user_id', 'user', 'id');
		$this->addForeignKey('rtt_refui_fk',
				'rtt',
				'referral_id', 'referral', 'id');
		$this->dropColumn('referral', 'clock_start');
	}

	public function down()
	{
		$this->dropTable('rtt');
		$this->addColumn('referral','clock_start', 'datetime');
	}
}