<?php

class m210330_091516_add_defaults_fix_portalexamscommand extends OEMigration
{
    private $nva_columns = ['left_unable_to_assess', 'right_unable_to_assess', 'left_eye_missing', 'right_eye_missing'];
    public function safeUp()
    {
        foreach ($this->nva_columns as $nva_column) {
            $this->alterOEColumn('et_ophciexamination_nearvisualacuity', $nva_column, 'tinyint(1)', true);
        }

        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'left_notes', 'TEXT', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'right_notes', 'TEXT', true);
        $this->alterOEColumn('et_ophciexamination_refraction', 'left_notes', 'VARCHAR(4096)', true);
        $this->alterOEColumn('et_ophciexamination_refraction', 'right_notes', 'VARCHAR(4096)', true);
    }

    public function safeDown()
    {
        foreach ($this->nva_columns as $nva_column) {
            $this->alterOEColumn('et_ophciexamination_nearvisualacuity', $nva_column, 'tinyint(1) NOT NULL', true);
        }

        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'left_notes', 'TEXT NOT NULL', true);
        $this->alterOEColumn('et_ophciexamination_nearvisualacuity', 'right_notes', 'TEXT NOT NULL', true);
        $this->alterOEColumn('et_ophciexamination_refraction', 'left_notes', 'VARCHAR(4096) NOT NULL', true);
        $this->alterOEColumn('et_ophciexamination_refraction', 'right_notes', 'VARCHAR(4096) NOT NULL', true);
    }
}
