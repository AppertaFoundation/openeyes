<?php

class m161123_102417_fix_patient_pedigree extends OEMigration
{
    public function up()
    {
        $this->renameTable('patient_pedigree', 'genetics_patient_pedigree');
        $this->dropForeignKey('patient_pedigree_patient_id_fk', 'genetics_patient_pedigree');
        $this->alterColumn('genetics_patient_pedigree', 'patient_id', 'int(11)');
        $this->addForeignKey('patient_pedigree_patient_id_fk', 'genetics_patient_pedigree', 'patient_id', 'genetics_patient', 'id');
        $this->alterColumn('genetics_patient_pedigree', 'status_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->alterColumn('genetics_patient_pedigree', 'status_id', 'int(10) unsigned not null');
        $this->dropForeignKey('patient_pedigree_patient_id_fk', 'genetics_patient_pedigree');
        $this->alterColumn('genetics_patient_pedigree', 'patient_id', 'int(10) unsigned');
        $this->addForeignKey('patient_pedigree_patient_id_fk', 'genetics_patient_pedigree', 'patient_id', 'patient', 'id');
        $this->renameTable('genetics_patient_pedigree', 'patient_pedigree');
    }
}
