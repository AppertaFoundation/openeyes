<?php

class m140923_073104_priorities_should_soft_delete extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_operation_priority', 'active', 'tinyint(1) unsigned not null default 1');
        $this->addColumn('ophtroperationbooking_operation_priority_version', 'active', 'tinyint(1) unsigned not null default 1');

        $this->alterColumn('et_ophtroperationbooking_operation', 'priority_id', 'int(10) unsigned not null');
        $this->alterColumn('et_ophtroperationbooking_operation_version', 'priority_id', 'int(10) unsigned not null');

        $this->addColumn('ophtroperationbooking_operation_priority', 'default', 'tinyint(1) unsigned not null');
        $this->addColumn('ophtroperationbooking_operation_priority_version', 'default', 'tinyint(1) unsigned not null');

        $this->update('ophtroperationbooking_operation_priority', array('default' => 1), 'id = 1');
        $this->update('ophtroperationbooking_operation_priority_version', array('default' => 1), 'id = 1');
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_operation_priority', 'active');
        $this->dropColumn('ophtroperationbooking_operation_priority_version', 'active');

        $this->alterColumn('et_ophtroperationbooking_operation', 'priority_id', 'int(10) unsigned not null default 1');
        $this->alterColumn('et_ophtroperationbooking_operation_version', 'priority_id', 'int(10) unsigned not null default 1');

        $this->dropColumn('ophtroperationbooking_operation_priority', 'default');
        $this->dropColumn('ophtroperationbooking_operation_priority_version', 'default');
    }
}
