<?php

class m220406_222305_create_internal_referral_site_firm_mapping_table extends OEMigration
{
	public function safeUp()
	{
		$this->createOETable('ophcocorrespondence_internal_referral_site_firm_mapping', [
			'id' => 'pk',
			'site_id' => 'int(10) unsigned NOT NULL',
			'firm_id' => 'int(10) unsigned NOT NULL'
		]);

		// One to one mappings
		$this->execute('INSERT INTO ophcocorrespondence_internal_referral_site_firm_mapping (site_id, firm_id) ' .
					   'SELECT DISTINCT to_location_id, to_firm_id FROM et_ophcocorrespondence_letter ' .
					   'WHERE to_location_id IS NOT NULL AND to_firm_id IS NOT NULL');

		// Where to_location_id is null, assign to all sites
		$this->execute('INSERT INTO ophcocorrespondence_internal_referral_site_firm_mapping (site_id, firm_id) ' .
					   'SELECT DISTINCT site.id, to_firm_id FROM et_ophcocorrespondence_letter, site ' .
					   'WHERE to_location_id IS NULL AND to_firm_id IS NOT NULL');
	}

	public function safeDown()
	{
		$this->dropOETable('ophcocorrespondence_internal_referral_site_firm_mapping');
	}
}
