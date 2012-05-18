<?php

class m120518_135103_site_insitution_mapping extends CDbMigration
{
	public function up()
	{
		$this->delete('institution_consultant_assignment');
		$this->delete('institution');
		$this->delete('address',"parent_class = 'Institution'");

		$this->insert('institution',array(
			'id' => 1,
			'name' => 'MOORFIELDS EYE HOSPITAL NHS FOUNDATION TRUST',
			'code' => 'RP6',
		));

		$this->insert('address',array(
			'address1' => '162 CITY ROAD',
			'address2' => '',
			'city' => 'LONDON',
			'county' => 'GREATER LONDON',
			'postcode' => 'EC1V 2PD',
			'country_id' => 1,
			'parent_class' => 'Institution',
			'parent_id' => 1,
		));

		$this->addColumn('site','institution_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('site_institution_id_fk','site','institution_id');
		$this->addForeignKey('site_institution_id_fk','site','institution_id','institution','id');
	}

	public function down()
	{
		$this->dropForeignKey('site_institution_id_fk','site');
		$this->dropIndex('site_institution_id_fk','site');
		$this->dropColumn('site','institution_id');
	}
}
