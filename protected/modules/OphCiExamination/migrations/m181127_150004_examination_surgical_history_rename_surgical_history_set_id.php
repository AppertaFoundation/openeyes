<?php

class m181127_150004_examination_surgical_history_rename_surgical_history_set_id extends CDbMigration
{
	public function up()
	{
	    $this->renameColumn('ophciexamination_surgical_history_set_entry' , 'surgical_history_set_id' , 'set_id');
	}

	public function down()
	{
        $this->renameColumn('ophciexamination_surgical_history_set_entry' , 'set_id' , 'surgical_history_set_id');
	}

}