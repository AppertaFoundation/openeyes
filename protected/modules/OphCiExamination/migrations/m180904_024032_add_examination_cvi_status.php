<?php

class m180904_024032_add_examination_cvi_status extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        //insert new element cvi status in examination event, set History as parent element
        $risk_display_order = $this->dbConnection->createCommand()->select('display_order')->from('element_type')->where('name = ?', array('Risk'))->queryScalar();
        $family_display_order = $this->dbConnection->createCommand()->select('display_order')->from('element_type')->where('name = ?', array('Family History'))->queryScalar();
        $display_order = ceil(($risk_display_order+$family_display_order)/2);
        $this->createElementType('OphCiExamination', 'CVI status', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CVI_Status',
            'group_name' => 'History',
            'display_order' => $display_order
        ));

        //create new table to store the examination history cvi status
        $this->createOETable('et_ophciexamination_cvi_status', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'cvi_status_id' => 'int(10) unsigned NOT NULL',
            'element_date' => 'date',
            'created_date' =>'datetime',
            'created_user_id' => 'int(10) unsigned',
            'last_modified_date' => 'datetime ',
            'last_modified_user_id' => 'int(10) unsigned',
        ), true);

        $this->addForeignKey('et_ophciexamination_cvi_status_event_id_fk', 'et_ophciexamination_cvi_status',
            'event_id', 'event', 'id');
        $this->addForeignKey('et_ophciexamination_cvi_status_cvi_id_fk', 'et_ophciexamination_cvi_status',
            'cvi_status_id', 'patient_oph_info_cvi_status', 'id');


    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophciexamination_cvi_status_event_id_fk', 'et_ophciexamination_cvi_status');
        $this->dropForeignKey('et_ophciexamination_cvi_status_cvi_id_fk', 'et_ophciexamination_cvi_status');
        $this->dropTable('et_ophciexamination_cvi_status');
        $this->dropTable('et_ophciexamination_cvi_status_version');

        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_CVI_Status'));
    }

}