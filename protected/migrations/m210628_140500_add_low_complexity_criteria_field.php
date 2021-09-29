<?php

class m210628_140500_add_low_complexity_criteria_field extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('proc', 'low_complexity_criteria', 'text', true);
    }

    public function down()
    {
        $this->dropOEColumn('proc', 'low_complexity_criteria', true);
    }
}
