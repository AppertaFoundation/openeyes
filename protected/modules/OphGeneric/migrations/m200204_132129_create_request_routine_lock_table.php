<?php

class m200204_132129_create_request_routine_lock_table extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('request_routine_lock', [
            'routine_lock' => 'VARCHAR(200) NOT NULL PRIMARY KEY'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');
	}

	public function safeDown()
	{
        $this->dropTable('request_routine_lock');
	}
}