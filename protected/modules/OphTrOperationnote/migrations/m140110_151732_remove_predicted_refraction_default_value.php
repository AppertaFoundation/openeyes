<?php

class m140110_151732_remove_predicted_refraction_default_value extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtroperationnote_cataract','predicted_refraction','decimal(4,2) NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('et_ophtroperationnote_cataract','predicted_refraction',"decimal(4,2) NOT NULL DEFAULT '0.00'");
	}
}
