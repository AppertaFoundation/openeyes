<?php

class m180926_034534_create_patient_identifier_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('patient_identifier',
            array(
                'id' => 'pk',
                'patient_id' => 'int(10) unsigned NOT NULL',
                'code' => 'varchar(50) NOT NULL',
                'value' => 'varchar(255)',
            ),
            true);
    }

    public function safeDown()
    {
        $this->dropOETable('patient_identifier', true);
    }
}