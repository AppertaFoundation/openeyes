<?php

class m120319_161629_fix_existing_element_operation_anaesthetic_type_values extends CDbMigration
{
	public function up()
	{
		$this->update('element_operation',array('anaesthetic_type'=>5),'anaesthetic_type=4');
		$this->update('element_operation',array('anaesthetic_type'=>4),'anaesthetic_type=3');
		$this->update('element_operation',array('anaesthetic_type'=>3),'anaesthetic_type=2');
		$this->update('element_operation',array('anaesthetic_type'=>2),'anaesthetic_type=1');
		$this->update('element_operation',array('anaesthetic_type'=>1),'anaesthetic_type=0');
	}

	public function down()
	{
		$this->update('element_operation',array('anaesthetic_type'=>0),'anaesthetic_type=1');
		$this->update('element_operation',array('anaesthetic_type'=>1),'anaesthetic_type=2');
		$this->update('element_operation',array('anaesthetic_type'=>2),'anaesthetic_type=3');
		$this->update('element_operation',array('anaesthetic_type'=>3),'anaesthetic_type=4');
		$this->update('element_operation',array('anaesthetic_type'=>4),'anaesthetic_type=5');
	}
}
