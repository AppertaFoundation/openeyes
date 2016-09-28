<?php

class m140917_080329_emergency_priority extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_operation_priority', 'schedule_authitem', 'varchar(64) not null');

        $this->insert('ophtroperationbooking_operation_priority', array('id' => 3, 'name' => 'Emergency', 'display_order' => 3));

        $this->update('ophtroperationbooking_operation_priority', array('schedule_authitem' => 'OprnScheduleOperation'));
    }

    public function down()
    {
        $this->delete('ophtroperationbooking_operation_priority', "name='Emergency'");

        $this->dropColumn('ophtroperationbooking_operation_priority', 'schedule_authitem');
    }
}
