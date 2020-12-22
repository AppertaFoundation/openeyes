<?php

class m191125_142953_change_oph_generic_assessment_integers_to_floats extends OEMigration
{
    public function safeUp()
    {
        foreach (['crt', 'avg_thickness', 'total_vol', 'avg_rnfl', 'cct', 'cd_ratio'] as $column) {
            $this->alterColumn('ophgeneric_assessment_entry', $column, 'float DEFAULT NULL');
        }

        $this->addOEColumn('ophgeneric_assessment_entry', 'abac_json', 'LONGTEXT NULL', true);
    }

    public function safeDown()
    {
        foreach (['crt', 'avg_thickness', 'total_vol', 'avg_rnfl', 'cct', 'cd_ratio'] as $column) {
            $this->alterColumn('ophgeneric_assessment_entry', $column, 'INT(10) DEFAULT NULL');
        }

        $this->dropOEColumn('ophgeneric_assessment_entry', 'abac_json');
    }
}
