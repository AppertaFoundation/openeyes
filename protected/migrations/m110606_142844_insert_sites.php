<?php

class m110606_142844_insert_sites extends CDbMigration
{
	public $sites = array(
		'Northwick Park',
		'Potters Bar',
		'Watford',
		'Bedford',
		'Ealing',
		'St Georges\'',
		'Bridge Lane',
		'QMHR',
		'Teddington',
		'Moorfields East',
		'St Ann\'s'
	);

	public function up()
	{
		foreach ($this->sites as $site) {
			$this->insert('site', array(
				'name' => $site
			));
		}
	}

	public function down()
	{
		foreach ($this->sites as $site) {
			$this->delete('site', 'name=:name',
				array(':name' => $site));
		}
	}
}