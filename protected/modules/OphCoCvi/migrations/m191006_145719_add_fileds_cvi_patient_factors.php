<?php

class m191006_145719_add_fileds_cvi_patient_factors extends OEMigration
{
	public function up()
	{
		$this->addColumn('ophcocvi_clericinfo_patient_factor','comments_only',"tinyint(1) unsigned NOT NULL DEFAULT '0'");
		$this->addColumn('ophcocvi_clericinfo_patient_factor_version','comments_only',"tinyint(1) unsigned NOT NULL DEFAULT '0'");
		$this->addColumn('ophcocvi_clericinfo_patient_factor','yes_no_only',"tinyint(1) unsigned NOT NULL DEFAULT '0'");
		$this->addColumn('ophcocvi_clericinfo_patient_factor_version','yes_no_only',"tinyint(1) unsigned NOT NULL DEFAULT '0'");
		$this->addColumn('ophcocvi_clericinfo_patient_factor','event_type_version',"int(4) unsigned NOT NULL DEFAULT '0'");
		$this->addColumn('ophcocvi_clericinfo_patient_factor_version','event_type_version',"int(4) unsigned NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		$this->dropColumn('ophcocvi_clericinfo_patient_factor','comments_only');
		$this->dropColumn('ophcocvi_clericinfo_patient_factor_version','comments_only');
		$this->dropColumn('ophcocvi_clericinfo_patient_factor','yes_no_only');
		$this->dropColumn('ophcocvi_clericinfo_patient_factor_version','yes_no_only');
		$this->dropColumn('ophcocvi_clericinfo_patient_factor','event_type_version');
		$this->dropColumn('ophcocvi_clericinfo_patient_factor_version','event_type_version');
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