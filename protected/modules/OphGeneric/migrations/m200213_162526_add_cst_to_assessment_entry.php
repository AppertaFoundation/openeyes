<?php

class m200213_162526_add_cst_to_assessment_entry extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophgeneric_assessment_entry', 'cst', 'float DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropOEColumn('ophgeneric_assessment_entry', 'cst');
    }

}
