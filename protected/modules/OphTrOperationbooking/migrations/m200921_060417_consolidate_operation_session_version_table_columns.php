<?php

class m200921_060417_consolidate_operation_session_version_table_columns extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('ophtroperationbooking_operation_session_version', 'sequence_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->alterColumn('ophtroperationbooking_operation_session_version', 'sequence_id', 'int(10) unsigned NOT NULL');
    }
}
