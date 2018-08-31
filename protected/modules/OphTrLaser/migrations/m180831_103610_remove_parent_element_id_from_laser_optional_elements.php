<?php

class m180831_103610_remove_parent_element_id_from_laser_optional_elements extends CDbMigration
{
	public function up()
	{
	    $this->update('element_type', ['parent_element_type_id' => null] , 'class_name = "Element_OphTrLaser_AnteriorSegment"');
	    $this->update('element_type', ['parent_element_type_id' => null] , 'class_name = "Element_OphTrLaser_PosteriorPole"');
	    $this->update('element_type', ['parent_element_type_id' => null] , 'class_name = "Element_OphTrLaser_Fundus"');
	}

	public function down()
	{
		echo "m180831_103610_remove_parent_element_id_from_laser_optional_elements does not support migration down.\n";
		return false;
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