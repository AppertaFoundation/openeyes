<?php

class m191213_150305_update_patient_ticket_priority_colors extends CDbMigration
{
    public function up()
    {
        $this->update('patientticketing_priority', ['colour'=>'red', 'name'=>'HIGH'], 'id=:id', [':id'=>1]);
        $this->update('patientticketing_priority', ['colour'=>'orange', 'name'=>'MEDIUM'], 'id=:id', [':id'=>2]);
        $this->update('patientticketing_priority', ['colour'=>'green', 'name'=>'LOW'], 'id=:id', [':id'=>3]);
    }

    public function down()
    {
        $this->update('patientticketing_priority', ['colour'=>'#c00', 'name'=>'red'], 'id=:id', [':id'=>1]);
        $this->update('patientticketing_priority', ['colour'=>'#f90', 'name'=>'amber'], 'id=:id', [':id'=>2]);
        $this->update('patientticketing_priority', ['colour'=>'#0c0', 'name'=>'green'], 'id=:id', [':id'=>3]);
    }
}
