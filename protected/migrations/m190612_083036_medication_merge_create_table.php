<?php

class m190612_083036_medication_merge_create_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('medication_merge', array(
            'id' => 'pk',
            'entry_timestamp' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'source_drug_id' => 'int(10)',
            'source_medication_id' => 'int',
            'source_code' => 'varchar(255)',
            'source_name' => 'varchar(255)',
            'target_id' => 'int',
            'target_code' => 'varchar(255)',
            'target_name' => 'varchar(255)',
            'status' => 'int(1) default 1',
            'merge_date' => 'TIMESTAMP' ));
    }

    public function down()
    {
        $this->dropTable('medication_merge');
    }
}
