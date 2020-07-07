<?php

class m200505_032351_create_theatreadmission_procedurelist_procedure_assignment extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'ophcitheatreadmission_procedurelist_procedure_assignment',
            array(
                'id' => 'pk',
                'procedurelist_id' => 'int(11)',
                'proc_id' => 'int(10) unsigned NOT NULL',
                'display_order' => 'tinyint(3) unsigned DEFAULT 10',
            ),
            true
        );

        // Add Foreign Key
        $this->addForeignKey(
            'ophcitheatreadmission_plpa_pl_fk',
            'ophcitheatreadmission_procedurelist_procedure_assignment',
            'procedurelist_id',
            'et_ophcitheatreadmission_procedurelist',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_plpa_proc_fk',
            'ophcitheatreadmission_procedurelist_procedure_assignment',
            'proc_id',
            'proc',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('ophcitheatreadmission_procedurelist_procedure_assignment', true);
    }
}
