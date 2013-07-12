<?php

class m130513_132400_nullable_drug_keys extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('drug', 'default_duration_id', 'int(10) UNSIGNED');
		$this->alterColumn('drug', 'default_route_id', 'int(10) UNSIGNED');
		$this->alterColumn('drug', 'default_frequency_id', 'int(10) UNSIGNED');
	}

	public function down()
	{
	}

}
