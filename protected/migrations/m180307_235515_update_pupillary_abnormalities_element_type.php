<?php

class m180307_235515_update_pupillary_abnormalities_element_type extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	    $parent_id = $this->getDbConnection()->createCommand("select id from element_type where class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_VisualFunction'")->queryRow();
	    if (isset($parent_id['id']) && $parent_id['id']) {
            $this->update('element_type', array('name'=>'Pupils', 'parent_element_type_id'=>$parent_id['id']),
                "class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_PupillaryAbnormalities'");
        }
	}

	public function safeDown()
	{
        $this->update('element_type', array('name'=>'Pupillary Abnormalities', 'parent_element_type_id'=>null),
            "class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_PupillaryAbnormalities'");
	}

}