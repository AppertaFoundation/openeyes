<?php

class m121008_135637_audit_event_group extends CDbMigration
{
	public function up()
	{
		$au = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('code=:code', array(':code' => 'Au'))->queryAll();
		if (count($au) > 0) {
			echo "**WARNING** Event group with code 'Au' found, skipping Audit creation ...\n";
		} else {
			$this->insert('event_group',array('id'=>9,'name'=>'Audit','code'=>'Au'));
		}
		$nu = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('code=:code', array(':code' => 'Nu'))->queryAll();
		if (count($nu) > 0) {
			echo "**WARNING** Event group with code 'Nu' found, skippung Nursing event group creation ...\n";
		} else {
			$this->insert('event_group',array('id'=>10,'name'=>'Nursing','code'=>'Nu'));
		}
	}

	public function down()
	{

		// ensure db consistency before stripping out the groups created by this migration
		$au = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('event_group_id=:id', array(':id' => 9))->queryAll();

		if (count($au) > 0) {
			echo count($au) . " Audit event types exists, cannot migrate down";
			return false;
		}
		$nu = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('event_group_id=:id', array(':id' => 10))->queryAll();
		if (count($nu) > 0) {
			echo $nu . " Nursing event types exists, cannot migrate down";
			return false;
		}

		$this->delete('event_group',"code='Au'");
		$this->delete('event_group',"code='Nu'");
		return true;
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
