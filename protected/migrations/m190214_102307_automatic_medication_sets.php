<?php

class m190214_102307_automatic_medication_sets extends OEMigration
{
	public function up()
	{
		$this->addColumn('medication_set', 'automatic', 'BOOLEAN NOT NULL DEFAULT 0');
		$this->addColumn('medication_set_version', 'automatic', 'BOOLEAN NOT NULL DEFAULT 0');

		$this->createOETable('medication_set_auto_rule_attribute', array(
			'id' => 'pk',
			'medication_set_id' => 'INT(11) NOT NULL',
			'medication_attribute_option_id' => 'INT(11) NOT NULL'
		), true);

		$this->addForeignKey('fk_msara_msid', 'medication_set_auto_rule_attribute', 'medication_set_id', 'medication_set', 'id');
		$this->addForeignKey('fk_msara_mattroptid', 'medication_set_auto_rule_attribute', 'medication_attribute_option_id', 'medication_attribute_option', 'id');

		$this->createOETable('medication_set_auto_rule_set_membership', array(
			'id' => 'pk',
			'target_medication_set_id' => 'INT(11) NOT NULL',
			'source_medication_set_id' => 'INT(11) NOT NULL'
		), true);

		$this->addForeignKey('fk_msarsm_tmsid', 'medication_set_auto_rule_set_membership', 'target_medication_set_id', 'medication_set', 'id');
		$this->addForeignKey('fk_msarsm_smsid', 'medication_set_auto_rule_set_membership', 'source_medication_set_id', 'medication_set', 'id');

		$this->createOETable('medication_set_auto_rule_medication', array(
			'id' => 'pk',
			'medication_set_id' => 'INT(11) NOT NULL',
			'medication_id' => 'INT(11) NOT NULL',
			'include_parent' => 'TINYINT DEFAULT 0 NOT NULL',
			'include_children' => 'TINYINT DEFAULT 0 NOT NULL'
		), true);

		$this->addForeignKey('fk_msarm_msid', 'medication_set_auto_rule_medication', 'medication_set_id', 'medication_set', 'id');
		$this->addForeignKey('fk_msarm_mid', 'medication_set_auto_rule_medication', 'medication_id', 'medication', 'id');

	}

	public function down()
	{
		$this->dropOETable('medication_set_auto_rule_medication', true);
		$this->dropOETable('medication_set_auto_rule_set_membership', true);
		$this->dropOETable('medication_set_auto_rule_attribute', true);
	}
}