<?php

class m210803_033548_add_ecds_code_and_term_columns extends OEMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		// Add ECDS code and term columns for disorder
		$this->addOEColumn('disorder', 'ecds_code', 'varchar(20) AFTER aliases', true);
		$this->addOEColumn('disorder', 'ecds_term', 'varchar(255) AFTER ecds_code', true);

		// Add ECDS code and term columns for proc
		$this->addOEColumn('proc', 'ecds_code', 'varchar(20)', true);
		$this->addOEColumn('proc', 'ecds_term', 'varchar(255)', true);

		$this->execute('CREATE TEMPORARY TABLE IF NOT EXISTS `temp_oe_pas_diagnosis` (
			`oe_diagnosis` varchar(255) DEFAULT NULL,
			`snomed_code` varchar(32) DEFAULT NULL,
			`pas_ecds_code` varchar(32) DEFAULT NULL,
			`pas_description` varchar(255) DEFAULT NULL
		)');

		$this->execute('CREATE TEMPORARY TABLE IF NOT EXISTS `temp_oe_pas_investigation` (
			`oe_investigation` varchar(255) DEFAULT NULL,
			`snomed_code` varchar(32) DEFAULT NULL,
			`snomed_term` varchar(255) DEFAULT NULL,
			`pas_ecds_code` varchar(32) DEFAULT NULL
		)');

		$this->initialiseData(dirname(__FILE__));

		$this->execute('UPDATE `disorder`, `temp_oe_pas_diagnosis`
			SET `ecds_code` = `pas_ecds_code`,
				`ecds_term` = `oe_diagnosis`
			WHERE `term` = `oe_diagnosis` OR `term` = `pas_description` OR `id` = `snomed_code`');

		$this->execute("UPDATE `proc` `p`, `temp_oe_pas_investigation` `topi`
			SET `ecds_code` = `pas_ecds_code`,
				`ecds_term` = `oe_investigation`
			WHERE `p`.`snomed_code` = `topi`.`snomed_code` AND `topi`.`snomed_term` <> ''");

		$this->execute("UPDATE `proc` `p`, `temp_oe_pas_investigation` `topi`
			SET `ecds_code` = `pas_ecds_code`,
				`ecds_term` = `oe_investigation`
			WHERE `p`.`snomed_code` = `topi`.`pas_ecds_code` AND `topi`.`snomed_term` = ''");

		$this->execute('DROP TEMPORARY TABLE `temp_oe_pas_diagnosis`,  `temp_oe_pas_investigation`');
	}

	public function safeDown()
	{
		// Remove ECDS code and term columns for proc
		$this->dropOEColumn('proc', 'ecds_term', true);
		$this->dropOEColumn('proc', 'ecds_code', true);

		// Remove ECDS code and term columns for disorder
		$this->dropOEColumn('disorder', 'ecds_term', true);
		$this->dropOEColumn('disorder', 'ecds_code', true);
	}
}
