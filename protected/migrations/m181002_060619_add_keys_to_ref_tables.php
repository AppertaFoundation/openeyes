<?php

class m181002_060619_add_keys_to_ref_tables extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE `openeyes`.`ref_medication_form` ADD KEY (`term`);
                    ALTER TABLE `openeyes`.`ref_medication` ADD KEY (`vmp_code`);
                    ALTER TABLE `openeyes`.`ref_medication` ADD KEY (`vtm_code`);
                    ALTER TABLE `openeyes`.`ref_medication` ADD KEY (`amp_code`);
                    ALTER TABLE `openeyes`.`ref_medication_route` ADD KEY (`term`);
                    
                    ALTER TABLE `openeyes`.`ref_medication` CHANGE `source_type` `source_type` VARCHAR(10) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `source_subtype` `source_subtype` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `preferred_term` `preferred_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `preferred_code` `preferred_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `vtm_term` `vtm_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `vtm_code` `vtm_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `vmp_term` `vmp_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `vmp_code` `vmp_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `amp_term` `amp_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `amp_code` `amp_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `short_term` `short_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, COLLATE=utf8_general_ci;
                    ALTER TABLE `openeyes`.`ref_medication_form` CHANGE `term` `term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `code` `code` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `unit_term` `unit_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `default_dose_unit_term` `default_dose_unit_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `source_type` `source_type` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `source_subtype` `source_subtype` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL;
                    ALTER TABLE `openeyes`.`ref_medication_form` COLLATE=utf8_general_ci;");
	}

	public function down()
	{
		echo "m181002_060619_add_keys_to_ref_tables does not support migration down.\n";
		return false;
	}
}