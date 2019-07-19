<?php

class m180831_103610_remove_parent_element_id_from_laser_optional_elements extends CDbMigration
{
    public function up()
    {
        $element_type = Yii::app()->db->schema->getTable('element_type');
        if (isset($element_type->columns['element_group_id'])) {
            $this->update('element_type', ['element_group_id' => null], 'class_name = "Element_OphTrLaser_AnteriorSegment"');
            $this->update('element_type', ['element_group_id' => null], 'class_name = "Element_OphTrLaser_PosteriorPole"');
            $this->update('element_type', ['element_group_id' => null], 'class_name = "Element_OphTrLaser_Fundus"');
        }
    }

    public function down()
    {
        $treatmentId = $this->dbConnection->createCommand()
            ->selecT('id')
            ->from('element_group')
            ->where('name = "Treatment"')
            ->queryScalar();

        $element_type = Yii::app()->db->schema->getTable('element_type');
        if (isset($element_type->columns['element_group_id'])) {
            $this->update('element_type', ['element_group_id' => $treatmentId], 'class_name = "Element_OphTrLaser_AnteriorSegment"');
            $this->update('element_type', ['element_group_id' => $treatmentId], 'class_name = "Element_OphTrLaser_PosteriorPole"');
            $this->update('element_type', ['element_group_id' => $treatmentId], 'class_name = "Element_OphTrLaser_Fundus"');
        }
    }
}