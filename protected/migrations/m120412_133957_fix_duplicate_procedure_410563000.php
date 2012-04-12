<?php

class m120412_133957_fix_duplicate_procedure_410563000 extends CDbMigration
{
	public function up()
	{
		$proc1 = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_code = :snomed_code and short_format = :short_format',array(':snomed_code' => '410563000', 'short_format' => 'Peri Inj Steroid'))->queryRow();
		$proc2 = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_code = :snomed_code and short_format = :short_format',array(':snomed_code' => '410563000', 'short_format' => 'Periocular steroid'))->queryRow();

		$this->update('operation_procedure_assignment',array('proc_id' => $proc2['id']),'proc_id='.$proc1['id']);
		$this->update('proc_subspecialty_subsection_assignment',array('proc_id' => $proc2['id']),'proc_id='.$proc1['id']);
		$this->delete('proc','id='.$proc1['id']);
	}

	public function down()
	{
		$this->insert('proc',array('term' => 'Periocular steroid injection', 'short_format' => 'Peri Inj Steroid', 'default_duration' => 15, 'snomed_code' => '410563000', 'snomed_term' => 'Periocular steroid injection', 'last_modified_date' => '2000-01-01 00:00:00', 'created_date' => '2000-01-01 00:00:00'));
	}
}
