<?php

class m200501_142350_delete_pas_key_from_patient extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->dropOEColumn('patient', 'pas_key', true);
    }

    public function safeDown()
    {
        $this->addOEColumn('patient', 'pas_key', 'int(10) unsigned DEFAULT NULL', true);
    }
}
