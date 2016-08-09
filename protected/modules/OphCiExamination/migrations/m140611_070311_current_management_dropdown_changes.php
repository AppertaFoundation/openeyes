<?php

class m140611_070311_current_management_dropdown_changes extends CDbMigration
{
    public function up()
    {
        $this->update('ophciexamination_managementdrops', array('display_order' => 1), 'id=2');
        $this->update('ophciexamination_managementdrops', array('display_order' => 2), 'id=1');
        $this->update('ophciexamination_managementdrops', array('name' => 'Changes'), 'id=3');
        $this->insert('ophciexamination_managementdrops', array('name' => 'Stop', 'display_order' => 4));
        $this->insert('ophciexamination_managementdrops', array('name' => 'None', 'display_order' => 5));
    }

    public function down()
    {
        $this->delete('ophciexamination_managementdrops', 'id in (4,5)');
        $this->update('ophciexamination_managementdrops', array('name' => 'Change'), 'id=3');
        $this->update('ophciexamination_managementdrops', array('display_order' => 2), 'id=2');
        $this->update('ophciexamination_managementdrops', array('display_order' => 1), 'id=1');
    }
}
