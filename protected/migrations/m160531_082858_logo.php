<?php

class m160531_082858_logo extends OEMigration
{
	public function up()
	{
            
              
            $this->createOETable('logo', array(
                'header_logo' => 'varchar(255) NOT NULL',
                'secondary_logo' => 'varchar(255) NOT NULL',
                
            ),true);
            
            
	}

	public function down()
	{
		$this->dropTable('logo');
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