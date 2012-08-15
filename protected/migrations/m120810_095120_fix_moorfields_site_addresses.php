<?php

class m120810_095120_fix_moorfields_site_addresses extends CDbMigration
{
	public function up()
	{
		$this->update('site',array(
			'address1' => '162 City Road',
		), 'id=1');
		$this->update('site',array(
			'address1' => 'Uxbridge Road',
			'address2' => 'Southall',
			'address3' => 'Middlesex',
			'postcode' => 'UB1 3HW',
		), 'id=3');
		$this->update('site',array(
			'address2' => 'Watford Road',
		), 'id=4');
		$this->update('site',array(
			'address1' => 'Roehampton Lane',
			'address2' => 'Roehampton',
			'address3' => 'London',
			'postcode' => 'SW15 5PN',
		), 'id=8');
		$this->update('site',array(
			'address1' => '20 Bridge Lane',
			'address3' => 'London',
			'postcode' => 'SW11 3AD',
		), 'id=10');
		$this->update('site',array(
			'address1' => 'Boots Opticians',
			'address2' => '201 Harlequin Shopping Centre',
			'address3' => 'Watford',
			'postcode' => 'WD17 2UB',
		), 'id=11');
		$this->update('site',array(
			'address1' => '417 Ilford Lane',
			'address2' => 'Ilford',
			'address3' => 'Essex',
			'postcode' => 'IG1 2SN',
		), 'id=12');
		$this->update('site',array(
			'address1' => '',
			'address2' => '',
			'address3' => '',
			'postcode' => '',
		), 'id=13');
		$this->update('site',array(
			'address1' => 'Teddington Memorial Hospital',
			'address2' => 'Hampton Road',
			'address3' => 'Teddington',
			'postcode' => 'TW11 0JL',
		), 'id=14');
		$this->update('site',array(
			'address1' => '',
			'address2' => '',
			'address3' => '',
			'postcode' => '',
		), 'id=15');
		$this->update('site',array(
			'address1' => '62-64 High Street',
			'address2' => 'Wealdstone',
			'address3' => 'Harrow',
			'postcode' => 'HA3 7AF',
		), 'id=16');
		$this->update('site',array(
			'address1' => 'Croydon University Hospital',
		), 'id=17');
		$this->update('site',array(
			'address1' => 'Homerton Row',
			'address2' => 'Hackney',
			'address3' => 'London',
			'postcode' => 'E9 6SR',
		), 'id=18');
		$this->update('site',array(
			'address1' => 'Hamstel Road',
			'address2' => 'Harlow',
			'address3' => 'Essex',
			'postcode' => 'CM20 1QX',
		), 'id=19');
	}

	public function down()
	{
	}
}
