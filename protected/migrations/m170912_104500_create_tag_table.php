<?php

class m170912_104500_create_tag_table extends OEMigration
{
	public function up()
	{
        $this->createOETable('tag', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'active' => 'TINYINT NOT NULL DEFAULT 1'
        ), true);

        $this->createIndex('idx_tag_name', 'tag', 'name', true);
	}

	public function down()
	{
        $this->dropOETable('tag', true);
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}