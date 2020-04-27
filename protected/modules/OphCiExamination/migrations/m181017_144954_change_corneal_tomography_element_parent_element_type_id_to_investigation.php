<?php

class m181017_144954_change_corneal_tomography_element_parent_element_type_id_to_investigation extends CDbMigration
{
    public function up()
    {
        $command = $this->getDbConnection()->createCommand('SELECT element_group_id FROM element_type WHERE name = ?');
        $investigation_group_id = $command->queryScalar(array('Investigation'));
        $this->update('element_type', ['element_group_id' => $investigation_group_id], "name = 'Corneal Tomography'");
    }

    public function down()
    {
        $command = $this->getDbConnection()->createCommand('SELECT element_group_id FROM element_type WHERE name = ?');
        $keratoconus_monitoring_id = $command->queryScalar(array('Keratoconus Monitoring'));
        $this->update('element_type', ['element_group_id' => $keratoconus_monitoring_id], "name = 'Corneal Tomography'");
    }
}
