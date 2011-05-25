<?php

class m110524_095728_alter_sequence_table extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('sequence', 'frequency', 'repeat_interval');
		
		$this->addColumn('sequence', 'weekday', 'tinyint(1)');
		
		$this->addColumn('sequence', 'week_selection', 'tinyint(1)');
	}

	public function down()
	{
		$this->dropColumn('sequence', 'week_selection');
		
		$this->dropColumn('sequence', 'weekday');
		
		$this->renameColumn('sequence', 'repeat_interval', 'frequency');
	}
}