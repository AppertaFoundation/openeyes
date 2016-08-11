<?php

class m140917_131610_use_roles_for_schedule_access extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_operation_priority_version', 'schedule_authitem', 'varchar(64) not null');

        $this->update('ophtroperationbooking_operation_priority', array('schedule_authitem' => 'Schedule operation'), "schedule_authitem = 'OprnScheduleOperation'");
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_operation_priority_version', 'schedule_authitem');

        $this->update('ophtroperationbooking_operation_priority', array('schedule_authitem' => 'OprnScheduleOperation'), "schedule_authitem = 'Schedule operation'");
    }
}
