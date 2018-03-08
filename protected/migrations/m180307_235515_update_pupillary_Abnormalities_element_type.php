<?php

class m180307_235515_update_pupillary_Abnormalities_element_type extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	    $this->update('element_type', array('name'=>'Pupils', 'parent_element_type_id'=>412), "id=361");
	}

	public function safeDown()
	{
        $this->update('element_type', array('name'=>'Pupillary Abnormalities', 'parent_element_type_id'=>null), "id=361");
	}

}