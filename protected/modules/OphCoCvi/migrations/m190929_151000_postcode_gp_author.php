<?php

class m190929_151000_postcode_gp_author extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_demographics', 'gp_postcode', 'varchar(4) AFTER gp_name');
        $this->addColumn('et_ophcocvi_demographics_version', 'gp_postcode', 'varchar(4) AFTER gp_name');

        $this->addColumn('et_ophcocvi_demographics', 'gp_postcode_2nd', 'varchar(4) AFTER gp_postcode');
        $this->addColumn('et_ophcocvi_demographics_version', 'gp_postcode_2nd', 'varchar(4) AFTER gp_postcode');

        $this->addColumn('et_ophcocvi_demographics', 'la_postcode', 'varchar(4) AFTER la_name');
        $this->addColumn('et_ophcocvi_demographics_version', 'la_postcode', 'varchar(4) AFTER la_name');

        $this->addColumn('et_ophcocvi_demographics', 'la_postcode_2nd', 'varchar(4) AFTER la_postcode');
        $this->addColumn('et_ophcocvi_demographics_version', 'la_postcode_2nd', 'varchar(4) AFTER la_postcode');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_demographics', 'la_postcode_2nd');
        $this->dropColumn('et_ophcocvi_demographics_version', 'la_postcode_2nd');

        $this->dropColumn('et_ophcocvi_demographics', 'la_postcode');
        $this->dropColumn('et_ophcocvi_demographics_version', 'la_postcode');

        $this->dropColumn('et_ophcocvi_demographics', 'la_postcode_2nd');
        $this->dropColumn('et_ophcocvi_demographics_version', 'gp_postcode_2nd');

        $this->dropColumn('et_ophcocvi_demographics', 'gp_postcode');
        $this->dropColumn('et_ophcocvi_demographics_version', 'gp_postcode');
    }
}
