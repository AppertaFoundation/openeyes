<?php

class m200505_032352_create_operationchecklists_procedurelist_procedure_assignment_table extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'ophtroperationchecklists_proclist_proc_assignment',
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
            'ophtroperationchecklists_plpa_pl_fk',
            'ophtroperationchecklists_proclist_proc_assignment',
            'procedurelist_id',
            'et_ophtroperationchecklists_procedurelist',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_plpa_proc_fk',
            'ophtroperationchecklists_proclist_proc_assignment',
            'proc_id',
            'proc',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('ophtroperationchecklists_proclist_proc_assignment', true);
    }
}
