<?php

class m120606_172600_add_long_name_to_drug_frequency extends CDbMigration
{
	public function up()
	{
		$this->addColumn('drug_frequency','long_name','varchar(40) NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('drug_frequency','long_name');
	}

}
