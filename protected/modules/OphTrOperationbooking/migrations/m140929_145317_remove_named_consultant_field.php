<?php

class m140929_145317_remove_named_consultant_field extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'named_consultant');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'named_consultant');
    }

    public function down()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'named_consultant', 'tinyint(1) unsigned not null default 1');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'named_consultant', 'tinyint(1) unsigned');
    }
}
