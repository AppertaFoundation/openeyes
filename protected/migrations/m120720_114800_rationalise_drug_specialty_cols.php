<?php

class m120720_114800_rationalise_drug_specialty_cols extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('site_subspecialty_drug', 'display_order');
		$this->dropColumn('site_subspecialty_drug', 'default');
	}

	public function down()
	{
		$this->addColumn('site_subspecialty_drug', 'display_order', 'tinyint(3)');
		$this->addColumn('site_subspecialty_drug', 'default', 'tinyint(1)');
	}

}
