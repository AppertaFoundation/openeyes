<?php

class m200402_215836_add_generated_column_to_patient_identifier_type_display_order extends OEMigration
{
	public function safeUp()
	{
        $this->execute("ALTER TABLE `patient_identifier_type_display_order` ADD `unique_row_str` varchar(255) GENERATED ALWAYS AS (CONCAT(patient_identifier_type_id,'-',institution_id,'-',COALESCE(site_id,'0'))) COMMENT 'Used for checking row is unique in table';");
        $this->execute("ALTER TABLE `patient_identifier_type_display_order_version` ADD `unique_row_str` varchar(255);");
        $this->createIndex('uk_unique_row_str', 'patient_identifier_type_display_order', ['unique_row_str'], true);
	}

	public function safeDown()
	{
        $this->dropIndex('uk_unique_row_str', 'patient_identifier_type_display_order');
        $this->dropOEColumn('patient_identifier_type_display_order', 'unique_row_str', true);
	}
}