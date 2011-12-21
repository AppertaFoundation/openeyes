<?php

class m111221_120222_urgent_routine_boolean_flag_on_element_operation extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_operation','urgent','boolean not null default false');
	}

	public function down()
	{
		$this->dropColumn('element_operation','urgent');
	}
}
