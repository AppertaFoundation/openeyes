<?php

class m140620_122600_visualacuity_change extends OEMigration
{
    private function visualAcuityId()
    {
        return
            $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity');
    }
    private function colourVisionId()
    {
        return
            $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision');
    }

    public function up()
    {
        $this->update(
            'element_type',
            array('name' => 'Visual Acuity'),
            'id= :id',
            array(':id' => $this->visualAcuityId())
        );
        $event_type_id = $this->insertOEEventType('Examination', 'OphCiExamination', 'Ci');
        $this->insertOEElementType(array('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction' => array(
                'name' => 'Visual Function',
                //'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
                'display_order' => 10,
                'required' => false,
            )), $event_type_id);

        $this->createOETable('et_ophciexamination_visualfunction', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',

            'left_comments' => 'text',
            'right_comments' => 'text',
            'left_rapd' => 'tinyint(1) unsigned not null',
            'right_rapd' => 'tinyint(1) unsigned not null',

            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',
            'KEY `et_ophciexam_visualfunction_ev_fk` (`event_id`)',
            'KEY `et_ophciexam_visualfunction_lrapd_id_fk` (`left_rapd`)',
            'KEY `et_ophciexam_visualfunction_rrapd_id_fk` (`right_rapd`)',
            'CONSTRAINT `et_ophciexam_visualfunction_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            'CONSTRAINT `et_ophciexam_visualfunction_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
        ), true);
        $this->dropColumn('et_ophciexamination_visualacuity', 'left_rapd');
        $this->dropColumn('et_ophciexamination_visualacuity', 'right_rapd');
        $this->dropColumn('et_ophciexamination_visualacuity', 'left_comments');
        $this->dropColumn('et_ophciexamination_visualacuity', 'right_comments');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'left_rapd');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'right_rapd');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'left_comments');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'right_comments');

        $visualFunctionId =
            $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction');

        $this->update(
            'element_type',
            array('parent_element_type_id' => $visualFunctionId),
            'id= :id',
            array(':id' => $this->visualAcuityId())
        );
        $this->update(
            'element_type',
            array('parent_element_type_id' => $visualFunctionId),
            'id= :id',
            array(':id' => $this->colourVisionId())
        );
    }

    public function down()
    {
        $visualFunctionId =
            $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction');

        $this->update(
            'element_type',
            array('name' => 'Visual Function'),
            'id= :id',
            array(':id' => $this->visualAcuityId())
        );

        $this->update(
            'element_type',
            array('parent_element_type_id' => null),
            'id= :id',
            array(':id' => $this->visualAcuityId())
        );
        $this->update(
            'element_type',
            array('parent_element_type_id' => $this->visualAcuityId()),
            'id= :id',
            array(':id' => $this->colourVisionId())
        );

        $this->dropTable('et_ophciexamination_visualfunction');
        $this->dropTable('et_ophciexamination_visualfunction_version');
        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction'));
        $this->addColumn('et_ophciexamination_visualacuity', 'left_rapd', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity', 'right_rapd', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_visualacuity', 'right_comments', 'text');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'left_rapd', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'right_rapd', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'right_comments', 'text');
    }
}
