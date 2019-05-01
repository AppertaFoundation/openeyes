<?php

class m190430_151251_add_unit_column_for_lab_results extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('et_ophinlabresults_result_timed_numeric', 'unit', 'varchar(10)');
	    $this->addColumn('et_ophinlabresults_result_timed_numeric_version', 'unit', 'varchar(10)');
	}

	public function down()
	{
	    $this->dropColumn('et_ophinlabresults_result_timed_numeric', 'unit');
	    $this->dropColumn('et_ophinlabresults_result_timed_numeric_version', 'unit');
	}
}