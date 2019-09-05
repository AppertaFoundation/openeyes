<?php

class m181127_150004_examination_surgical_history_rename_surgical_history_set_id extends CDbMigration
{
    public function safeUp()
    {
        $this->dropForeignKey('surgical_history_set_entry_surgical_history', 'ophciexamination_surgical_history_set_entry');

        $this->renameColumn('ophciexamination_surgical_history_set_entry', 'surgical_history_set_id', 'set_id');

        $this->addForeignKey('surgical_history_set_entry_surgical_history', 'ophciexamination_surgical_history_set_entry', 'set_id', 'ophciexamination_surgical_history_set', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('surgical_history_set_entry_surgical_history', 'ophciexamination_surgical_history_set_entry');

        $this->renameColumn('ophciexamination_surgical_history_set_entry', 'set_id', 'surgical_history_set_id');

        $this->addForeignKey('surgical_history_set_entry_surgical_history', 'ophciexamination_surgical_history_set_entry', 'surgical_history_set_id', 'ophciexamination_surgical_history_set', 'id');
    }

}
