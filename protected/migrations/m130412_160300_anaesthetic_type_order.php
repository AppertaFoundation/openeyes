<?php

class m130412_160300_anaesthetic_type_order extends CDbMigration
{
	public function up()
	{
		$this->update('element_type_anaesthetic_type', array('display_order' => 3), "anaesthetic_type_id = 2");
		$this->update('element_type_anaesthetic_type', array('display_order' => 2), "anaesthetic_type_id = 3");
	}

	public function down()
	{
	}
}
