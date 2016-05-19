<?php

class m160519_102017_automatic_examination_event_log extends OEMigration
{
	public function up()
	{
            $this->createOETable('automatic_examination_event_log', array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned NOT NULL',
                'unique_code' => 'varchar(6) NOT NULL',
                'examination_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
            ),true);
            $this->addForeignKey('automatic_examination_event_log_event_id_fk','automatic_examination_event_log','event_id','event','id');
	}

	public function down()
	{
		$this->dropTable('automatic_examination_event_log');
	}
}