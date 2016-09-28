<?php

class m160623_112050_alter_sequence_id extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('ophtroperationbooking_operation_session', 'sequence_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->alterColumn('ophtroperationbooking_operation_session', 'sequence_id', 'int(10) unsigned NOT NULL');
    }
}
