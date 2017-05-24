<?php

class m140917_125406_event_type_operation_suffix_migration extends  OEMigration
{
    public function up()
    {
        $this->setEventTypeRBACSuffix('OphInGenetictest', 'GeneticTest');
    }

    public function down()
    {
        $this->setEventTypeRBACSuffix('OphInGenetictest', null);
    }
}
