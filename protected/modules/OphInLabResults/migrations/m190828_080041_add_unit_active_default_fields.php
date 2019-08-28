<?php

class m190828_080041_add_unit_active_default_fields extends CDbMigration
{
	public function up()
    {
        $this->addColumn('ophinlabresults_type', 'show_units', 'TINYINT(1) DEFAULT 1');
        $this->addColumn('ophinlabresults_type', 'allow_unit_change', 'TINYINT(1) DEFAULT 1');
        $this->addColumn('ophinlabresults_type_version', 'show_units', 'TINYINT(1) DEFAULT 1');
        $this->addColumn('ophinlabresults_type_version', 'allow_unit_change', 'TINYINT(1) DEFAULT 1');
	}

	public function down()
	{
        $this->dropColumn('ophinlabresults_type', 'show_units');
        $this->dropColumn('ophinlabresults_type', 'allow_unit_change');
        $this->dropColumn('ophinlabresults_type_version', 'show_units');
        $this->dropColumn('ophinlabresults_type_version', 'allow_unit_change');
	}
}
