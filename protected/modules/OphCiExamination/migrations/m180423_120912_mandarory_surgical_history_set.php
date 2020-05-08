<?php

class m180423_120912_mandarory_surgical_history_set extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophciexamination_surgical_history_set',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NULL',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' =>  'int(10) unsigned',
            ),
            true
        );

        $this->addForeignKey('surgical_history_set_subspecialty', 'ophciexamination_surgical_history_set', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('surgical_history_set_firm', 'ophciexamination_surgical_history_set', 'firm_id', 'firm', 'id');

        $this->createOETable(
            'ophciexamination_surgical_history_set_entry',
            array(
                'id' => 'pk',
                'surgical_history_set_id' => 'int(11)',
                'operation' => 'varchar(1024)',
                'gender' => 'varchar(1) NULL',
                'age_min' => 'int(3) unsigned',
                'age_max' => 'int(3) unsigned',

            ),
            true
        );

        $this->addForeignKey('surgical_history_set_entry_surgical_history', 'ophciexamination_surgical_history_set_entry', 'surgical_history_set_id', 'ophciexamination_surgical_history_set', 'id');

    }

    public function down()
    {
        $this->dropForeignKey('surgical_history_set_entry_surgical_history', 'ophciexamination_surgical_history_set_entry');
        $this->dropOETable('ophciexamination_surgical_history_set_entry', true);
        $this->dropForeignKey('surgical_history_set_subspecialty', 'ophciexamination_surgical_history_set');
        $this->dropForeignKey('surgical_history_set_firm', 'ophciexamination_surgical_history_set');
        $this->dropOETable('ophciexamination_surgical_history_set', true);
    }
}
