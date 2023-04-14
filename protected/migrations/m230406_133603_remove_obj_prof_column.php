<?php

class m230406_133603_remove_obj_prof_column extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->dropOEColumn('gp', 'obj_prof', true);
    }

    public function safeDown()
    {
        $this->addOEColumn('gp', 'obj_prof', 'varchar(20) AFTER id NOT NULL DEFAULT NULL', true);
    }
}
