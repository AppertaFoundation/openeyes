<?php

class m170410_191928_medical_lids extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphCiExamination')
        )->queryScalar();

        $element_types = array(
            'OEModule\OphCiExamination\models\MedicalLids' => array(
                'name' => 'Medical Lids',
                'display_order' => 55,
            ),
        );
        $this->insertOEElementType($element_types, $event_type_id);

        $this->createOETable('et_ophciexamination_medical_lids', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'left_eyedraw' => 'text',
            'left_ed_report' => 'text',
            'left_comments' => 'text',
            'left_stfb' => 'boolean',
            'right_eyedraw' => 'text',
            'right_ed_report' => 'text',
            'right_comments' => 'text',
            'right_stfb' => 'boolean',
            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
            'CONSTRAINT `et_ophciexamination_medical_lids_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            'CONSTRAINT `et_ophciexamination_medical_lids_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
        ), true);
    }


    public function down()
    {
        $this->dropOETable('et_ophciexamination_medical_lids', true);
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();
        $this->delete('element_type', 'name = :name AND event_type_id = :eid', array(':name' => 'Medical Lids', ':eid' => $event_type_id));
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
