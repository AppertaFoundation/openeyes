<?php

class m161021_102624_genetic_patient_relations extends OEMigration
{

    public function up()
    {
        $this->dropForeignKey('patient_mother_id_fk', 'patient');
        $this->dropIndex('patient_mother_id_fk', 'patient');
        $this->dropColumn('patient', 'mother_id');

        $this->dropForeignKey('patient_father_id_fk', 'patient');
        $this->dropIndex('patient_father_id_fk', 'patient');
        $this->dropColumn('patient', 'father_id');

        $this->dropColumn('patient', 'yob');
    }

    public function down()
    {
        $this->addColumn('patient', 'father_id', 'int(10) unsigned NULL');
        $this->createIndex('patient_father_id_fk', 'patient', 'father_id');
        $this->addForeignKey('patient_father_id_fk', 'patient', 'father_id', 'patient', 'id');

        $this->addColumn('patient', 'mother_id', 'int(10) unsigned NULL');
        $this->createIndex('patient_mother_id_fk', 'patient', 'mother_id');
        $this->addForeignKey('patient_mother_id_fk', 'patient', 'mother_id', 'patient', 'id');

        $this->addColumn('patient', 'yob', 'int(2) unsigned NULL');
    }
}
