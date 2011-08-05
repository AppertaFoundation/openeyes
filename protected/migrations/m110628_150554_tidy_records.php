<?php

class m110628_150554_tidy_records extends CDbMigration
{
	public function up()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('theatre');
		$this->truncateTable('site');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();

		$this->addColumn('site', 'code', 'char(2) NOT NULL');
		$this->addColumn('site', 'short_name', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'address1', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'address2', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'address3', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'postcode', 'varchar(10) NOT NULL');
		$this->addColumn('site', 'telephopne', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'fax', 'varchar(255) NOT NULL');

		$this->insert('site', array(
			'name' => 'Example site long name',
			'short_name' => 'Example site',
			'code' => 'A1'
		));

		$this->addColumn('theatre', 'code', 'varchar(4) NOT NULL');

		$this->insert('theatre', array(
			'name' => 'Example theatre',
			'code' => 'ABCD',
			'site_id' => 1
		));
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('theatre');
		$this->truncateTable('site');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();

		$this->dropColumn('site', 'code');
		$this->dropColumn('site', 'short_name');
		$this->dropColumn('site', 'address1');
		$this->dropColumn('site', 'address2');
		$this->dropColumn('site', 'address3');
		$this->dropColumn('site', 'postcode');
		$this->dropColumn('site', 'telephopne');
		$this->dropColumn('site', 'fax');

		$this->insert('site', array(
			'name' => 'Example site long name',
		));

		$this->dropColumn('theatre', 'code');

		$this->insert('theatre', array(
			'name' => 'Example theatre',
			'site_id' => 1
		));
	}
}
