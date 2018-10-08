<?php

class m180810_042256_change_van_herick_parent extends OEMigration
{
    public function safeUp()
    {
        $anteriorSegmentId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name',
                array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment'))
            ->queryScalar();

        $this->update('element_type', array(
            'parent_element_type_id' => $anteriorSegmentId,
            'display_order' => 20, // Place between Anterior SEgment and Gonioscopy
        ), 'class_name = :class_name', array(':class_name' => 'OEModule\OphCiExamination\models\VanHerick')
        );
    }

    public function safeDown()
    {
        $this->update('element_type', array(
            'parent_element_type_id' => null,
            'display_order' => 37,
        ), 'class_name = :class_name', array(':class_name' => 'OEModule\OphCiExamination\models\VanHerick')
        );
    }
}
