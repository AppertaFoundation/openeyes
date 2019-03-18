<?php

class m181011_114200_rename_op_note_event extends CDbMigration
{
	public function up()
	{
        $this->update('event_type', ['name' => 'Operation note'],    'name = "Operation Note"');

	}

	public function down()
	{
        $this->update('event_type', ['name' => 'Operation Note'],    'name = "Operation note"');
	}
}
