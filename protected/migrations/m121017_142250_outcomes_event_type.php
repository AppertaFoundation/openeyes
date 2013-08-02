<?php

class m121017_142250_outcomes_event_type extends CDbMigration
{
	public function up()
	{
		$au = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('code=:code', array(':code' => 'Au'))->queryRow();
		if (count($au)) {
			$this->update('event_group', array('name'=>'Outcomes', 'code'=>'Ou'), 'id='.$au['id']);
		} else {
			$this->insert('event_group',array('id'=>9,'name'=>'Outcomes','code'=>'Ou'));
		}
	}

	public function down()
	{
		$this->update('event_group', array('name' => 'Audit', 'code'=>'Au'), 'id=9');
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
