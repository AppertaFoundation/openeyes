<?php

class m190821_141458_add_IOP_zero extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_intraocularpressure_reading', array('id' => 1, 'name' => '0', 'value' => 0,'display_order' => 1, 'last_modified_user_id' => 1, 'last_modified_date' => '1900-01-01 00:00:00', 'created_user_id' => 1, 'created_date' => '1900-01-01 00:00:00'));
    }

    public function down()
    {
        $this->delete('ophciexamination_intraocularpressure_reading', "id = 1");
    }
}
