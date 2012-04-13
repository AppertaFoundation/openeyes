<?php

class m120413_121631_adjust_anaesthetic_delivery extends CDbMigration
{
	public function up()
	{
		$this->update('anaesthetic_delivery',array('name'=>'Topical and intracameral'),"name='Intracameral'");
	}

	public function down()
	{
		$this->update('anaesthetic_delivery',array('name'=>'Intracameral'),"name='Topical and intracameral'");
	}
}
