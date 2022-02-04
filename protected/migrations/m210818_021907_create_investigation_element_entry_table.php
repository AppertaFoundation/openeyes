<?php

class m210818_021907_create_investigation_element_entry_table extends OEMigration
{

    public function safeUp()
    {
        $this->createOETable('et_ophciexamination_investigation_entry', array(
            'id' => 'pk',
            'element_id' => 'int(10) unsigned NOT NULL',
//            This needs to be the entry from the investigations table, can't be varchar
            'investigation_code' => 'int(11) NOT NULL',
            'comments' => 'varchar(4096)',
            'time' => 'time NOT NULL',
            'date' => 'date NOT NULL'
        ), true);
        $this->addForeignKey('et_ophciexamination_investigation_element_id_fk', 'et_ophciexamination_investigation_entry', 'element_id', 'et_ophciexamination_investigation', 'id');
        $this->addForeignKey('et_ophciexamination_investigation_code_fk', 'et_ophciexamination_investigation_entry', 'investigation_code', 'et_ophciexamination_investigation_codes', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophciexamination_investigation_code_fk', 'et_ophciexamination_investigation_entry');
        $this->dropForeignKey('et_ophciexamination_investigation_element_id_fk', 'et_ophciexamination_investigation_entry');
        $this->dropOETable('et_ophciexamination_investigation_entry', true);
    }
}
