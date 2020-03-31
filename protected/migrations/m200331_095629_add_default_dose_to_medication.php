<?php

class m200331_095629_add_default_dose_to_medication extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('medication', 'default_dose', 'INT(10) DEFAULT NULL', true);
    }

    public function down()
    {
        $this->dropOEColumn('medication', 'default_dose', true);
    }
}
