<?php

class m120319_152650_update_element_type_anaesthetic_type extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_type_anaesthetic_type','display_order','int(10) unsigned NOT NULL DEFAULT 1');

		$this->update('element_type_anaesthetic_type',array('display_order'=>1),'id=1');
		$this->update('element_type_anaesthetic_type',array('display_order'=>2),'id=3');
		$this->update('element_type_anaesthetic_type',array('display_order'=>3),'id=2');
		$this->update('element_type_anaesthetic_type',array('display_order'=>4),'id=4');
		$this->update('element_type_anaesthetic_type',array('display_order'=>5),'id=5');
	}

	public function down()
	{
	}
}
