<?php

class m120717_154100_rationalise_drug_name_cols extends CDbMigration
{
	public function up()
	{
		$this->addColumn('drug', 'aliases', 'text');
		$this->dropColumn('drug', 'description');
		$this->dropColumn('drug', 'term');
	}

	public function down()
	{
		$this->dropColumn('drug', 'aliases');
		$this->addColumn('drug', 'code', 'varchar(40)');
		$this->addColumn('drug', 'term', 'varchar(255)');
	}

}
