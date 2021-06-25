<?php

class m210531_090800_add_language_to_patient_table extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn(
            'language',
            'pas_term',
            'varchar(4)',
            true
        );
    }

    public function safeDown()
    {
        $this->dropOEColumn('language', 'pas_term', true);
    }
}
