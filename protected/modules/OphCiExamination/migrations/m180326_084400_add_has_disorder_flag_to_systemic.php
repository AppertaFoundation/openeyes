<?php

class m180326_084400_add_has_disorder_flag_to_systemic extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_systemic_diagnoses_diagnosis', 'has_disorder', 'tinyint(1) NOT NULL DEFAULT -9');
        $this->addColumn('ophciexamination_systemic_diagnoses_diagnosis_version', 'has_disorder', 'tinyint(1) NOT NULL DEFAULT -9');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_systemic_diagnoses_diagnosis', 'has_disorder');
        $this->dropColumn('ophciexamination_systemic_diagnoses_diagnosis_version', 'has_disorder');
    }
}
