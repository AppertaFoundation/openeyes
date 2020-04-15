<?php

class m161201_101348_genetic_subject_diagnosis extends OEMigration
{
    public function up()
    {
        $this->createOETable('genetics_patient_diagnosis', array(
            'id' => 'pk',
            'disorder_id' => 'BIGINT unsigned',
            'patient_id' => 'int(11)',
        ));

        $this->addForeignKey('genetics_patient_diagnosis_disorder', 'genetics_patient_diagnosis', 'disorder_id', 'disorder', 'id');
        $this->addForeignKey('genetics_patient_diagnosis_patient', 'genetics_patient_diagnosis', 'patient_id', 'genetics_patient', 'id');
    }

    public function down()
    {
        $this->dropOETable('genetics_patient_diagnosis');
    }
}
