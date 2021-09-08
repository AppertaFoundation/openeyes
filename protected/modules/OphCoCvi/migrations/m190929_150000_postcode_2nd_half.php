<?php

class m190929_150000_postcode_2nd_half extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_demographics', 'postcode_2nd', 'varchar(4) AFTER postcode');
        $this->addColumn('et_ophcocvi_demographics_version', 'postcode_2nd', 'varchar(4) AFTER postcode');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_demographics', 'postcode_2nd');
        $this->dropColumn('et_ophcocvi_demographics_version', 'postcode_2nd');
    }
}
