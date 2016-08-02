<?php

class m160728_085205_add_new_columns_ophcocvi_clericinfo_employment_status extends CDbMigration
{
	public function up()
	{
            $this->addColumn('ophcocvi_clericinfo_employment_status', 'active', 'tinyint(1) unsigned not null default 1 AFTER `social_history_occupation_id` ');
            $this->addColumn('ophcocvi_clericinfo_employment_status', 'social_history_occupation_id', 'int(12) NULL AFTER `child_default`  ');
            $this->addColumn('ophcocvi_clericinfo_employment_status', 'child_default','tinyint(1) unsigned NOT NULL DEFAULT 1 AFTER `name` ');
            
            $this->addColumn('ophcocvi_clericinfo_employment_status_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `social_history_occupation_id`');
            $this->addColumn('ophcocvi_clericinfo_employment_status_version', 'social_history_occupation_id', 'int(12) NULL AFTER `child_default`');
            $this->addColumn('ophcocvi_clericinfo_employment_status_version', 'child_default','tinyint(1) unsigned NOT NULL DEFAULT 1 AFTER `name`');
            
	}

	public function down()
	{
            $this->dropColumn('ophcocvi_clericinfo_employment_status', 'active');
            $this->dropColumn('ophcocvi_clericinfo_employment_status', 'social_history_occupation_id');
            $this->dropColumn('ophcocvi_clericinfo_employment_status', 'child_default');
            $this->dropColumn('ophcocvi_clericinfo_employment_status_version', 'active');
            $this->dropColumn('ophcocvi_clericinfo_employment_status_version', 'social_history_occupation_id');
            $this->dropColumn('ophcocvi_clericinfo_employment_status_version', 'child_default');
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