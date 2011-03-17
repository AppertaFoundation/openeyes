<?php

class m110317_105251_create_address_tables extends CDbMigration
{
    public function up()
    {
		$this->createTable('address', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'address1' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'address2' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'city' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'postcode' => 'varchar(10) COLLATE utf8_bin DEFAULT NULL',
			'county' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'country_id' => 'int(10) unsigned NOT NULL',
			'email' => 'varchar(60) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->addColumn('contact', 'address_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('contact', 'primary_phone', 'varchar(20) DEFAULT NULL');

		$this->addColumn('patient', 'address_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('patient', 'primary_phone', 'varchar(20) DEFAULT NULL');
    }

    public function down()
    {
		$this->dropColumn('patient', 'address_id');
		$this->dropColumn('patient', 'primary_phone');

		$this->dropColumn('contact', 'address_id');
		$this->dropColumn('contact', 'primary_phone');
		
		$this->dropTable('address');
    }
}