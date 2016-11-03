<?php

class m140917_125247_event_type_operation_suffix_migration extends OEMigration
{
    public function up()
    {
        $this->setEventTypeRBACSuffix('OphInBloodsample', 'BloodSample');
    }

    public function down()
    {
        $this->setEventTypeRBACSuffix('OphInBloodsample', null);
    }
}
