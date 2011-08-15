<?php

class m110815_135108_correct_site_table extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('site', 'telephopne');
		$this->addColumn('site', 'telephone', 'VARCHAR(255) NOT NULL');

		$this->update('site', array(
			'address1' => '1 road street',
			'postcode' => 'A1 2BC',
			'telephone' => '020 7123 4567',
			'fax' => '020 7987 6543'
		), 'id = :id', array(':id' => 1));
	}

	public function down()
	{
                $this->dropColumn('site', 'telephone');
                $this->addColumn('site', 'telephopne', 'VARCHAR(255) NOT NULL');
	}
}
