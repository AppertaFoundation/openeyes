<?php

class m160728_104209_add_columns_generic_type_lookups extends CDbMigration
{
	public function up()
	{
            $this->addColumn('ophcocvi_clericinfo_contact_urgency', 'code', 'VARCHAR(20) AFTER `name` ');
            $this->addColumn('ophcocvi_clericinfo_contact_urgency', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
            $this->addColumn('ophcocvi_clericinfo_contact_urgency_version', 'code', 'VARCHAR(20) AFTER `name` ');
            $this->addColumn('ophcocvi_clericinfo_contact_urgency_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
            
            $this->addColumn('ophcocvi_clinicinfo_field_of_vision', 'code', 'VARCHAR(20) AFTER `name` ');
            $this->addColumn('ophcocvi_clinicinfo_field_of_vision', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
            $this->addColumn('ophcocvi_clinicinfo_field_of_vision_version', 'code', 'VARCHAR(20) AFTER `name` ');
            $this->addColumn('ophcocvi_clinicinfo_field_of_vision_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
            
            $this->addColumn('ophcocvi_clinicinfo_low_vision_status', 'code', 'VARCHAR(20) AFTER `name` ');
            $this->addColumn('ophcocvi_clinicinfo_low_vision_status', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
            $this->addColumn('ophcocvi_clinicinfo_low_vision_status_version', 'code', 'VARCHAR(20) AFTER `name` ');
            $this->addColumn('ophcocvi_clinicinfo_low_vision_status_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
            
	}

	public function down()
	{
            $this->dropColumn('ophcocvi_clericinfo_contact_urgency', 'code');
            $this->dropColumn('ophcocvi_clericinfo_contact_urgency', 'active');
            $this->dropColumn('ophcocvi_clericinfo_contact_urgency_version', 'code');
            $this->dropColumn('ophcocvi_clericinfo_contact_urgency_version', 'active');
            
            $this->dropColumn('ophcocvi_clinicinfo_field_of_vision', 'code');
            $this->dropColumn('ophcocvi_clinicinfo_field_of_vision', 'active');
            $this->dropColumn('ophcocvi_clinicinfo_field_of_vision_version', 'code');
            $this->dropColumn('ophcocvi_clinicinfo_field_of_vision_version', 'active');
            
            $this->dropColumn('ophcocvi_clinicinfo_low_vision_status', 'code');
            $this->dropColumn('ophcocvi_clinicinfo_low_vision_status', 'active');
            $this->dropColumn('ophcocvi_clinicinfo_low_vision_status_version', 'code');
            $this->dropColumn('ophcocvi_clinicinfo_low_vision_status_version', 'active');
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