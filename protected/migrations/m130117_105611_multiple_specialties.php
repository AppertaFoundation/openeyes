<?php

class m130117_105611_multiple_specialties extends CDbMigration
{
	public function up()
	{
		$this->addColumn('specialty', 'medical', 'boolean not null default true');
		$this->insert('specialty', array('name' => 'Support Services', 'code' => 'SUP', 'medical' => false));
	}

	public function down()
	{

		$sup = $this->dbConnection->createCommand()->select('id')->from('specialty')->where('code=:code',array(':code'=>"SUP"))->queryRow();
		$this->delete('subspecialty', 'specialty_id = :sup_id', array(':sup_id' => $sup['id']));

		$this->delete('specialty', 'code = :code', array(':code' => 'SUP'));
		$this->dropColumn('specialty', 'medical');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
