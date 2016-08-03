<?php

class m140610_142527_current_management_data_fix extends CDbMigration
{
    public function up()
    {
        $this->update('ophciexamination_managementrelproblem', array('display_order' => 2), 'id=1');
        $this->update('ophciexamination_managementrelproblem', array('display_order' => 3), 'id=2');
        $this->insert('ophciexamination_managementrelproblem', array('id' => 3, 'name' => 'None', 'display_order' => 1));
    }

    public function down()
    {
        $this->delete('ophciexamination_managementrelproblem', 'id=3');
        $this->update('ophciexamination_managementrelproblem', array('display_order' => 1), 'id=1');
        $this->update('ophciexamination_managementrelproblem', array('display_order' => 2), 'id=2');
    }
}
