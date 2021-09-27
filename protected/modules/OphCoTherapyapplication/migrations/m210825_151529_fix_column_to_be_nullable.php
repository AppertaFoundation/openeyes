<?php

class m210825_151529_fix_column_to_be_nullable extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophcotherapya_exceptional', 'left_standard_intervention_exists', 'tinyint(1) unsigned NULL', true);
    }

    public function down()
    {
        $this->alterOEColumn('et_ophcotherapya_exceptional', 'left_standard_intervention_exists', 'tinyint(1) unsigned NOT NULL', true);
    }
}
