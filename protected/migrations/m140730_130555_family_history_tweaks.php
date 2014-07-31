<?php

class m140730_130555_family_history_tweaks extends CDbMigration
{
	public function up()
	{
		$this->addColumn('family_history_relative', 'is_other', 'boolean DEFAULT false');
		$this->addColumn('family_history_relative_version', 'is_other', 'boolean DEFAULT false');
		// this does assume that other is at the bottom of the list
		$mx_do = $this->getDbConnection()->createCommand('select max(display_order) from family_history_relative')->queryScalar();
		$this->update('family_history_relative', array('is_other' => true, 'display_order' => $mx_do+2), 'name = "Other"');
		$this->insert('family_history_relative', array('name' => 'Daughter', 'display_order' => $mx_do));
		$this->insert('family_history_relative', array('name' => 'Son', 'display_order' => $mx_do+1));
		$this->addColumn('family_history_condition', 'is_other', 'boolean DEFAULT false');
		$this->addColumn('family_history_condition_version', 'is_other', 'boolean DEFAULT false');
		$this->update('family_history_condition', array('is_other' => true), 'name = "Other"');
		$this->addColumn('family_history', 'other_relative', 'string');
		$this->addColumn('family_history_version', 'other_relative', 'string');
		$this->addColumn('family_history', 'other_condition', 'string');
		$this->addColumn('family_history_version', 'other_condition', 'string');


	}

	public function down()
	{
		$this->dropColumn('family_history_version', 'other_condition');
		$this->dropColumn('family_history', 'other_condition');
		$this->dropColumn('family_history_version', 'other_relative');
		$this->dropColumn('family_history', 'other_relative');
		$this->dropColumn('family_history_condition_version', 'is_other');
		$this->dropColumn('family_history_condition', 'is_other');
		$this->dropColumn('family_history_relative_version', 'is_other');
		$this->dropColumn('family_history_relative', 'is_other');

		$this->delete('family_history_relative','name = "Son"');
		$this->delete('family_history_relative','name = "Daughter"');
		$mx_do = $this->getDbConnection()->createCommand('select max(display_order) from family_history_relative')->queryScalar();
		$this->update('family_history_relative', array('display_order' => $mx_do-2), 'name ="Other"');
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