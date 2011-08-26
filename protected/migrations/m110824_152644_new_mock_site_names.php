<?php

class m110824_152644_new_mock_site_names extends CDbMigration
{
	public function up()
	{
		$this->update('site', array('name' => 'Hospital 1', 'short_name' => 'Hospital 1', 'address1' => '1 Road Street'), "id=1");

                $this->insert('site', array(
                        'name' => 'Hospital 2',
			'code' => 'A2',
			'short_name' => 'Hospital 2',
			'address1' => '1 Medical Mews',
			'address2' => 'Hospitalshire',
			'address3' => '',
			'postcode' => 'SW1A 1DG',
			'fax' => '020 7876 5432',
			'telephone' => '020 7234 5678'
                ));

                $this->insert('site', array(
                        'name' => 'Hospital 3',
                        'code' => 'A3',                   
                        'short_name' => 'Hospital 3',
                        'address1' => 'Hospital House',
                        'address2' => 'Hospital Building',
                        'address3' => '1 Hospital Street',
                        'postcode' => 'W1 1AA',
                        'fax' => '020 7765 4322',
                        'telephone' => '020 7345 6789'
                ));
        }

        public function down()
        {
                $this->delete('site', 'name=:name', array(':name' => 'Hospital 2'));
                $this->delete('site', 'name=:name', array(':name' => 'Hospital 3'));

                $this->update('site', array('name' => 'Example site long name', 'short_name' => 'Example site', 'address1' => '1 road street'), "id=1");
        }
}
