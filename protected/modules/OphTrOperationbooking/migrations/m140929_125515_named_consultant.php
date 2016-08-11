<?php

class m140929_125515_named_consultant extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'named_consultant', 'tinyint(1) unsigned not null default 1');
        $this->addColumn('et_ophtroperationbooking_operation', 'named_consultant_id', 'int(10) unsigned');

        $this->addColumn('et_ophtroperationbooking_operation_version', 'named_consultant', 'tinyint(1) unsigned');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'named_consultant_id', 'int(10) unsigned');

        $this->addForeignKey('named_consultant_fk', 'et_ophtroperationbooking_operation', 'named_consultant_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'named_consultant');
        $this->dropForeignKey('named_consultant_fk', 'et_ophtroperationbooking_operation');
        $this->dropColumn('et_ophtroperationbooking_operation', 'named_consultant_id');

        $this->dropColumn('et_ophtroperationbooking_operation_version', 'named_consultant');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'named_consultant_id');
    }
}
