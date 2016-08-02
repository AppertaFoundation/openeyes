<?php

class m160728_111722_add_columns_ophcocvi_clericinfo_preferred_info_fmt extends CDbMigration
{
	public function up()
	{
            $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'require_email', 'tinyint(1) unsigned NOT NULL DEFAULT 1 AFTER `name` ');
            $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'active', 'tinyint(1) unsigned not null default 1 AFTER `require_email` ');
            $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'require_email', 'tinyint(1) unsigned NOT NULL DEFAULT 1 AFTER `name` ');
            $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `require_email` ');
           
	}

	public function down()
	{
            $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'require_email');
            $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'active');
            $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'require_email');
            $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'active');
           
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