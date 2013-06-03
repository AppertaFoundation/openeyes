<?php

class m130528_094310_nhs_choices_import_source extends CDbMigration
{
	public function up()
	{
		$this->insert('import_source',array('name'=>'NHS Choices'));
	}

	public function down()
	{
		$this->delete('import_source',"name='NHS Choices'");
	}
}
