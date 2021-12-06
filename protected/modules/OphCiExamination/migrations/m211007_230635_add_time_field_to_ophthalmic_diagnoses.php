<?php

class m211007_230635_add_time_field_to_ophthalmic_diagnoses extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addOEColumn('ophciexamination_diagnosis', 'time', 'time', true);
        $this->execute('UPDATE ophciexamination_diagnosis SET time = DATE_FORMAT(created_date,\'%H:%i:%s\')');
        $this->execute('UPDATE ophciexamination_diagnosis_version SET time = DATE_FORMAT(created_date,\'%H:%i:%s\')');

        // This was deferred to allow updating the existing records before enforcing the NOT NULL constraint.
        $this->alterOEColumn('ophciexamination_diagnosis', 'time', 'time NOT NULL', true);
    }

    public function down()
    {
        $this->dropOEColumn('ophciexamination_diagnosis', 'time', true);
    }
}
