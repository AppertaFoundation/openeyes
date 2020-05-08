<?php

class m190325_111457_add_ophtr_operationnote_generic_procedure_comments_default_text extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationnote_generic_procedure_data', [
            'id' => 'pk',
            'proc_id' => 'int(10) unsigned NOT NULL UNIQUE',
            'default_text' => 'varchar(4096)  NULL',
        ]);

        $this->addForeignKey(
            'generic_procedure_default_text_fk_proc_id',
            'ophtroperationnote_generic_procedure_data',
            'proc_id',
            'proc',
            'id'
        );

        $this->execute("INSERT INTO ophtroperationnote_generic_procedure_data (proc_id)
                             SELECT prc.id 
                             FROM proc prc
                             WHERE id NOT IN
                                (SELECT procedure_id FROM ophtroperationnote_procedure_element)");
    }

    public function down()
    {
        $this->dropForeignKey('generic_procedure_data_fk_proc_id', 'ophtroperationnote_generic_procedure_data');
        $this->dropOETable('ophtroperationnote_generic_procedure_data');
    }
}
