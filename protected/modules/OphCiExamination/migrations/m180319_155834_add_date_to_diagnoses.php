<?php

class m180319_155834_add_date_to_diagnoses extends OEMigration
{
	public function up()
	{
	    $this->addColumn('ophciexamination_diagnosis', 'date', 'VARCHAR(10)');
	    $this->addColumn('ophciexamination_diagnosis_version', 'date', 'VARCHAR(10)');
	}

	public function down()
	{
		$this->dropColumn('ophciexamination_diagnosis', 'date');
		$this->dropColumn('ophciexamination_diagnosis_version', 'date');
	}
}