<?php

class m160728_100343_drop_default_column_ophcocvi_clinicinfo_disorder extends CDbMigration
{
	public function up()
	{
	}

	public function down()
	{
            $this->dropColumn('ophcocvi_clinicinfo_disorder', 'default');
            $this->dropColumn('ophcocvi_clinicinfo_disorder_version', 'default');
                
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