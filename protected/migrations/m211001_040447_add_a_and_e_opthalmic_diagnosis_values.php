<?php

class m211001_040447_add_a_and_e_opthalmic_diagnosis_values extends OEMigration
{
	public function up()
	{
		$this->execute('CREATE TEMPORARY TABLE IF NOT EXISTS `temp_oe_diagnosis_extra` (
			`oe_diagnosis` varchar(255) DEFAULT NULL,
			`snomed_code` varchar(32) DEFAULT NULL,
			`pas_ecds_code` varchar(32) DEFAULT NULL,
			`pas_description` varchar(255) DEFAULT NULL
		)');

		$this->initialiseData(dirname(__FILE__));

		$this->execute('UPDATE `disorder`, `temp_oe_diagnosis_extra`, `specialty`
			SET `ecds_code` = `pas_ecds_code`,
				`ecds_term` = `oe_diagnosis`,
                `specialty_id` = `specialty`.`id`
			WHERE `specialty`.`name` = "Ophthalmology" AND (`term` = `oe_diagnosis` OR `term` = `pas_description` OR `disorder`.`id` = `snomed_code`)');

		$this->execute('DROP TEMPORARY TABLE `temp_oe_diagnosis_extra`');
	}

	public function down()
	{
		echo "m211001_040447_add_a_and_e_opthalmic_diagnosis_values does not support migration down.\n";
		return false;
	}
}
