<?php

class m160606_123210_fix_import_status extends CDbMigration
{
    public function up()
    {
        $this->update('import_status', array('status_value' => 'Duplicate Event'), 'status_value = "Duplicate/Unfound Event"');
        $this->insert('import_status', array('status_value' => 'Unfound Event'));
        $this->insert('import_status', array('status_value' => 'Dismissed Event'));
    }

    public function down()
    {
        $this->delete('import_status', 'status_value = "Unfound Event"');
        $this->delete('import_status', 'status_value = "Dismissed Event"');
        $this->update('import_status', array('status_value' => 'Duplicate/Unfound Event'), 'status_value = "Duplicate Event"');
    }

}