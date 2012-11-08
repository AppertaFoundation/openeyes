<?php

class m121108_165957_multiple_specialism_disorders extends CDbMigration
{
	public function up()
	{
		$this->addColumn("disorder", "specialty", "int(10) unsigned NULL");
		$this->addForeignKey("disorder_specialty_fk", "disorder", "specialty", "specialty", "id");
		
		$ophthmalogy = $this->dbConnection->createCommand()->select('specialty.id')->from('specialty')->where('name=:name', array('name' => 'Ophthalmology'))->queryRow();
		$this->update("disorder", array("specialty"=>$ophthmalogy['id']), "systemic=:systemic", array("systemic" => 0));
		$this->dropColumn("disorder", "systemic");	
				
	}

	public function down()
	{
		$this->addColumn("disorder", "systemic", "tinyint(1) unsigned DEFAULT '0'");
		$this->update("disorder", array("systemic"=> 1), "specialty is NULL");
		$this->dropForeignKey("disorder_specialty_fk", "disorder");
		$this->dropColumn("disorder", "specialty");
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