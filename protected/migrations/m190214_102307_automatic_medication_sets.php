<?php

class m190214_102307_automatic_medication_sets extends OEMigration
{
	public function up()
	{
		$this->createOETable('medication_set_auto_rule', array(
			'id' => 'pk',
			'medication_set_id' => 'INT(11) NOT NULL',
			'name' => 'string NOT NULL',
			'hidden' => 'TINYINT DEFAULT 0 NOT NULL'
		), true);

		$this->addForeignKey('fk_msar_medication', 'medication_set_auto_rule', 'medication_set_id', 'medication_set', 'id');

		$this->createOETable('medication_set_auto_rule_attribute', array(
			'id' => 'pk',
			'medication_set_auto_rule_id' => 'INT(11) NOT NULL',
			'medication_attribute_option_id' => 'INT(11) NOT NULL'
		), true);

		$this->addForeignKey('fk_msara_msarid', 'medication_set_auto_rule_attribute', 'medication_set_auto_rule_id', 'medication_set_auto_rule', 'id');
		$this->addForeignKey('fk_msara_mattroptid', 'medication_set_auto_rule_attribute', 'medication_attribute_option_id', 'medication_attribute_option', 'id');

		$this->createOETable('medication_set_auto_rule_set_membership', array(
			'id' => 'pk',
			'medication_set_auto_rule_id' => 'INT(11) NOT NULL',
			'medication_set_id' => 'INT(11) NOT NULL'
		), true);

		$this->addForeignKey('fk_msarsm_msarid', 'medication_set_auto_rule_set_membership', 'medication_set_auto_rule_id', 'medication_set_auto_rule', 'id');
		$this->addForeignKey('fk_msarsm_msid', 'medication_set_auto_rule_set_membership', 'medication_set_id', 'medication_set', 'id');

		$this->createOETable('medication_set_auto_rule_medication', array(
			'id' => 'pk',
			'medication_set_auto_rule_id' => 'INT(11) NOT NULL',
			'medication_id' => 'INT(11) NOT NULL',
			'include_parent' => 'TINYINT DEFAULT 0 NOT NULL',
			'include_children' => 'TINYINT DEFAULT 0 NOT NULL'
		), true);

		$this->addForeignKey('fk_msarm_msarid', 'medication_set_auto_rule_medication', 'medication_set_auto_rule_id', 'medication_set_auto_rule', 'id');
		$this->addForeignKey('fk_msarm_mid', 'medication_set_auto_rule_medication', 'medication_id', 'medication', 'id');

	}

	public function down()
	{
		$this->dropOETable('medication_set_auto_rule_medication', true);
		$this->dropOETable('medication_set_auto_rule_set_membership', true);
		$this->dropOETable('medication_set_auto_rule_attribute', true);
		$this->dropOETable('medication_set_auto_rule', true);
	}
}