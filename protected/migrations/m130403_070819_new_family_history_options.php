<?php

class m130403_070819_new_family_history_options extends CDbMigration
{
	public function up()
	{
		$this->delete('family_history_side');

		$this->insert('family_history_side',array('id'=>1,'name'=>'N/A','display_order'=>1));
		$this->insert('family_history_side',array('id'=>2,'name'=>'Maternal','display_order'=>2));
		$this->insert('family_history_side',array('id'=>3,'name'=>'Paternal','display_order'=>3));
		$this->insert('family_history_side',array('id'=>4,'name'=>'Unknown','display_order'=>4));
	}

	public function down()
	{
	}
}
