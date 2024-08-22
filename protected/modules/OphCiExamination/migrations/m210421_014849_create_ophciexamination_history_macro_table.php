<?php

class m210421_014849_create_ophciexamination_history_macro_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophciexamination_history_macro', [
            'id' => 'pk',
            'name' => 'varchar(64) NOT NULL',
            'body' => 'text',
            'display_order' => 'tinyint(3) unsigned',
            'active' => 'tinyint(1) unsigned NOT NULL'
        ], true);
        $this->createOETable('ophciexamination_history_macro_subspecialty', [
            'id' => 'pk',
            'history_macro_id' => 'int(11) NOT NULL',
            'subspecialty_id' => 'int(10) unsigned NOT NULL'
        ], true);
        $this->addForeignKey(
            'ophciexamination_history_macro_hm_fk',
            'ophciexamination_history_macro_subspecialty',
            'history_macro_id',
            'ophciexamination_history_macro',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_macro_ss_fk',
            'ophciexamination_history_macro_subspecialty',
            'subspecialty_id',
            'subspecialty',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('ophciexamination_history_macro_hm_fk', 'ophciexamination_history_macro_subspecialty');
        $this->dropForeignKey('ophciexamination_history_macro_ss_fk', 'ophciexamination_history_macro_subspecialty');
        $this->dropOETable('ophciexamination_history_macro_subspecialty', true);
        $this->dropOETable('ophciexamination_history_macro', true);
    }
}
