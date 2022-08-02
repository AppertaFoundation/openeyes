<?php

class m220728_150800_remove_time_to_diagnoses_version extends OEMigration
{
    public function up()
    {
        if ($this->verifyColumnExists('ophciexamination_diagnosis_version', 'time')) {
            $this->dropColumn('ophciexamination_diagnosis_version', 'time');
        }
    }

    public function down()
    {
        if (!$this->verifyColumnExists('ophciexamination_diagnosis_version', 'time')) {
            $this->addColumn('ophciexamination_diagnosis_version', 'time', 'time DEFAULT NULL');
        }
    }
}
