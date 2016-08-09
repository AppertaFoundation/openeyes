<?php

class m140124_095039_default_anaesthetic_id_to_null extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophtroperationbooking_operation', 'anaesthetic_type_id', 'INT(10) UNSIGNED NOT NULL');
    }

    public function down()
    {
        $this->alterColumn('et_ophtroperationbooking_operation', 'anaesthetic_type_id', 'INT(10) UNSIGNED NULL DEFAULT 1');
    }
}
