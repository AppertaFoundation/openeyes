<?php

class m120810_103523_upney_lane_site_address extends CDbMigration
{
	public function up()
	{
		$this->update('site',array(
			'address1' => 'Upney Lane',
			'address2' => 'Barking',
			'address3' => 'Essex',
			'postcode' => 'IG11 9LX',
		), 'id=15');
	}

	public function down()
	{
	}
}
