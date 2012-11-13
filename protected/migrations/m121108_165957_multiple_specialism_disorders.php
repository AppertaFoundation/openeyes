<?php

class m121108_165957_multiple_specialism_disorders extends CDbMigration
{
	public function up()
	{
		$this->addColumn("disorder", "specialty_id", "int(10) unsigned NULL");
		$this->addForeignKey("disorder_specialty_fk", "disorder", "specialty_id", "specialty", "id");
		
		$ophthmalogy = $this->dbConnection->createCommand()->select('specialty.id')->from('specialty')->where('name=:name', array('name' => 'Ophthalmology'))->queryRow();
		$this->update("disorder", array("specialty_id"=>$ophthmalogy['id']), "systemic=:systemic", array("systemic" => 0));
		$this->dropColumn("disorder", "systemic");	
				
	}

	public function down()
	{
		$this->addColumn("disorder", "systemic", "tinyint(1) unsigned DEFAULT '0'");
		$this->update("disorder", array("systemic"=> 1), "specialty_id is NULL");
		$this->dropForeignKey("disorder_specialty_fk", "disorder");
		$this->dropColumn("disorder", "specialty_id");
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