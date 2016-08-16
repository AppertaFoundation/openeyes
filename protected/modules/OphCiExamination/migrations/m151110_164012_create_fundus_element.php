<?php

class m151110_164012_create_fundus_element extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();

        $element_types = array(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Fundus' => array('name' => 'Fundus', 'display_order' => 75),
        );
        $this->insertOEElementType($element_types, $event_type_id);

        $this->createOETable('et_ophciexamination_fundus', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'left_eyedraw' => 'text',
            'left_description' => 'text',
            'right_eyedraw' => 'text',
            'right_description' => 'text',
            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
            'CONSTRAINT `et_ophciexamination_fundus_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            'CONSTRAINT `et_ophciexamination_fundus_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_fundus', true);
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();
        $this->delete('element_type', 'name = :name AND event_type_id = :eid', array(':name' => 'Fundus', ':eid' => $event_type_id));
    }
}
