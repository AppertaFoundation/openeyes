<?php

class m180810_042256_change_van_herick_parent extends OEMigration
{
    public function safeUp()
    {
        $anteriorSegmentId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_group')
            ->where(
                'name = :name',
                array(':name' => 'Anterior Segment')
            )
            ->queryScalar();

        $this->update('element_type', array(
            'element_group_id' => $anteriorSegmentId,
            'display_order' => 20, // Place between Anterior Segment and Gonioscopy
        ), 'class_name = :class_name', array(':class_name' => 'OEModule\OphCiExamination\models\VanHerick'));
    }

    public function safeDown()
    {
        $this->update('element_type', array(
            'element_group_id' => null,
            'display_order' => 37,
        ), 'class_name = :class_name', array(':class_name' => 'OEModule\OphCiExamination\models\VanHerick'));
    }
}
