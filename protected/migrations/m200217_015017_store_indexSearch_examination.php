<?php
//This migration adds a column to store indexSearch contents for respective event types - Done as part of OE-9558
class m200217_015017_store_indexSearch_examination extends CDbMigration
{
	public function safeUp()
	{
        $this->addColumn('event_type', 'index_search_content', 'mediumtext');
	}

	public function safeDown()
	{
        $this->dropColumn('event_type', 'index_search_content');
	}
}