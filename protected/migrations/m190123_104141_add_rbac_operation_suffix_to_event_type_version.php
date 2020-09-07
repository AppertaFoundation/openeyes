<?php

class m190123_104141_add_rbac_operation_suffix_to_event_type_version extends CDbMigration
{
    public function up()
    {
        $this->addColumn('event_type_version', 'rbac_operation_suffix', 'varchar(100) COLLATE utf8_bin AFTER parent_id');
    }

    public function down()
    {
        $this->dropColumn('event_type_version', 'rbac_operation_suffix');
    }
}
