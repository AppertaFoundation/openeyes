<?php

class m200504_083443_edit_csm_shortcode_for_cataract_surgical_management extends OEMigration
{

    public function safeUp()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->update('patient_shortcode', ['method' => 'getCataractSurgicalManagementAsText', 'description' => 'Cataract Surgical Management in text readable format'], "code = 'csm'");

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type['id'],
            'code' => 'cst',
            'default_code' => 'cst',
            'method' => 'getCataractSurgicalManagementAsTable',
            'description' => 'Cataract Surgical Management in table format',
        ));
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'cst'));
    }
}
