<?php

class m160504_102700_unique_code_mapping extends OEMigration
{
    public function up()
    {
        $this->createOETable('unique_codes_mapping', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'unique_code_id' => 'int(11) NOT NULL',
        ), true);
        $this->addForeignKey('unique_code_mapping_event_id_fk', 'unique_codes_mapping', 'event_id', 'event', 'id');
        $this->addForeignKey('unique_code_mapping_uniquecode_id_fk', 'unique_codes_mapping', 'unique_code_id', 'unique_codes', 'id');
    }

    public function down()
    {
        $this->dropOETable('unique_codes_mapping', true);
    }
}
