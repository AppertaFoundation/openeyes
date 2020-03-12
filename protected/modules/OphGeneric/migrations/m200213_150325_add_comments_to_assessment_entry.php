<?php

class m200213_150325_add_comments_to_assessment_entry extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophgeneric_assessment_entry', 'comments', 'text');
    }

    public function safeDown()
    {
        $this->dropOEColumn('ophgeneric_assessment_entry', 'comments');
    }
}
