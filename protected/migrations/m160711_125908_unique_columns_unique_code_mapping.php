<?php

class m160711_125908_unique_columns_unique_code_mapping extends OEMigration
{
    public function up()
    {
        $this->createIndex('unique_code_mapping_event_id_unique', 'unique_codes_mapping', 'event_id', true);
        $this->createIndex('unique_code_mapping_unique_code_id_unique', 'unique_codes_mapping', 'unique_code_id', true);
    }

    public function down()
    {
    }
}
