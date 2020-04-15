<?php

class m170630_135359_add_surgical_lids_element extends OEMigration
{
    public function up()
    {
        $this->createElementType('OphCiExamination', 'Lids Surgical', array(
            'class_name' => 'OEModule\OphCiExamination\models\SurgicalLids',
            'display_order' => 10,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AdnexalComorbidity'
        ));

        // Update display order for Lids Medical
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\MedicalLids');
        $this->update('element_type', array('display_order' => 20), 'id = :id', array(':id' => $id));

        $this->createOETable('et_ophciexamination_surgical_lids', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'left_eyedraw' => 'text',
            'left_ed_report' => 'text',
            'left_comments' => 'text',
            'right_eyedraw' => 'text',
            'right_ed_report' => 'text',
            'right_comments' => 'text',
            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
            'CONSTRAINT `et_ophciexamination_surgical_lids_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            'CONSTRAINT `et_ophciexamination_surgical_lids_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_surgical_lids', true);
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\SurgicalLids');
        $this->delete('element_type', 'id = ?', array($id));
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
