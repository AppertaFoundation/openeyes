<?php

class m120319_151821_adjust_anaesthetic_type_table extends CDbMigration
{
	public function up()
	{
		//$this->dropColumn('anaesthetic_type','code');
		$this->update('anaesthetic_type',array('name'=>'LAC'),'id=2');
		$this->update('anaesthetic_type',array('name'=>'LA'),'id=3');
		$this->update('anaesthetic_type',array('name'=>'LAS'),'id=4');
		$this->update('anaesthetic_type',array('name'=>'GA'),'id=5');
	}

	public function down()
	{
	}
}
