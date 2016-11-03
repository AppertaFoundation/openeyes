<?php

class m140917_121605_event_type_operation_suffix_migration extends OEMigration
{
    public function up()
    {
        $this->setEventTypeRBACSuffix('OphInDnaextraction', 'DnaExtraction');
    }

    public function down()
    {
        $this->setEventTypeRBACSuffix('OphInDnaextraction', null);
    }
}
