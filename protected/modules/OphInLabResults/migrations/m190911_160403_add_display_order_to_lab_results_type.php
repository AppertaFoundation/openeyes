<?php

class m190911_160403_add_display_order_to_lab_results_type extends CDbMigration
{
	public function safeUp()
	{
        $this->addColumn('ophinlabresults_type', 'display_order', 'INT(10)');
        $this->addColumn('ophinlabresults_type_version', 'display_order', 'INT(10)');
	}

	public function safeDown()
	{
        $this->dropColumn('ophinlabresults_type', 'display_order');
        $this->dropColumn('ophinlabresults_type_version', 'display_order');
	}
}