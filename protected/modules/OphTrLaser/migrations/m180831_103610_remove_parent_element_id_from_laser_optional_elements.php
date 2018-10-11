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
        $this->update('element_type', ['parent_element_type_id' => 382] , 'class_name = "Element_OphTrLaser_AnteriorSegment"');
        $this->update('element_type', ['parent_element_type_id' => 382] , 'class_name = "Element_OphTrLaser_PosteriorPole"');
        $this->update('element_type', ['parent_element_type_id' => 382] , 'class_name = "Element_OphTrLaser_Fundus"');
	}
}