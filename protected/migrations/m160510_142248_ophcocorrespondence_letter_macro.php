<?php

class m160510_142248_ophcocorrespondence_letter_macro extends CDbMigration
{
	public function up()
	{
            $this->addColumn('ophcocorrespondence_letter_macro','short_code', 'varchar(3)');
            $this->createIndex( 'short_code_UNIQUE', 'ophcocorrespondence_letter_macro', 'short_code', $unique = true );
	}

	public function down()
	{
		
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