<?php

class m200203_140101_remove_no_fluids_from_assessment extends OEMigration
{
    public function safeUp()
    {
        $this->dropOEColumn('ophgeneric_assessment_entry', 'no_fluid');
    }

    public function safeDown()
    {
        $this->addOEColumn('ophgeneric_assessment_entry', 'no_fluid', 'INT(10) DEFAULT NULL');
    }

}
