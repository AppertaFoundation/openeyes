<?php

class m110525_123817_add_index_to_sessions extends CDbMigration
{
	public function up()
	{
		$this->createIndex('session_idx1', 'session', 'sequence_id,date,start_time,end_time', true);
	}

	public function down()
	{
		$this->dropIndex('session_idx1', 'session');
	}
}