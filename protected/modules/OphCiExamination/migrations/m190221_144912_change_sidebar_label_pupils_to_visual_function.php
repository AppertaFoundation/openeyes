<?php

class m190221_144912_change_sidebar_label_pupils_to_visual_function extends CDbMigration
{
	public function safeUp()
	{
		$event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', [':class_name' => 'OphCiExamination'])
            ->queryScalar();

        $this->update('element_group', array('name' => 'Visual Function', 'display_order' => '30'), "name = 'Pupils' AND event_type_id =".$event_type_id);

        $this->update('element_group', array('display_order' => '20'), "name = 'Observations' AND event_type_id =".$event_type_id);
	}

	public function safeDown()
	{
		echo "m190221_144912_change_sidebar_label_pupils_to_visual_function does not support migration down.\n";
	}
}