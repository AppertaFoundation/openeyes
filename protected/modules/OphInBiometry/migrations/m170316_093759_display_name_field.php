<?php

class m170316_093759_display_name_field extends CDbMigration
{
	public function up()
	{
        $this->addColumn('ophinbiometry_lenstype_lens', 'display_name', 'varchar(255) NOT NULL');
        $this->addColumn('ophinbiometry_lenstype_lens_version', 'display_name', 'varchar(255) NOT NULL');
	}

	public function down()
	{
        $this->dropColumn('ophinbiometry_lenstype_lens', 'display_name');
        $this->dropColumn('ophinbiometry_lenstype_lens_version', 'display_name');
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