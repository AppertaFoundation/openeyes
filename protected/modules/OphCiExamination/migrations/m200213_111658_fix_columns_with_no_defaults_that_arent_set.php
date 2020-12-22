<?php

class m200213_111658_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_unable_to_assess', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_unable_to_assess', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_eye_missing', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_eye_missing', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', 'TEXT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', 'TEXT NULL');
    }

    public function down()
    {
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_unable_to_assess', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_unable_to_assess', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_eye_missing', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_eye_missing', 'TINYINT(1) UNSIGNED NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', 'TEXT NOT NULL');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', 'TEXT NOT NULL');
    }
}
