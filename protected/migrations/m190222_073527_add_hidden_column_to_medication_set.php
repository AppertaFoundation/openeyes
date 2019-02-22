<?php

class m190222_073527_add_hidden_column_to_medication_set extends CDbMigration
{
	public function up()
	{
		$this->addColumn('medication_set', 'hidden', 'BOOLEAN NOT NULL DEFAULT 0');
		$this->addColumn('medication_set_version', 'hidden', 'BOOLEAN NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('medication_set', 'hidden');
		$this->dropColumn('medication_set_version', 'hidden');
	}
}