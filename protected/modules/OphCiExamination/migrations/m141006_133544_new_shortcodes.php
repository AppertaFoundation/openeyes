<?php

class m141006_133544_new_shortcodes extends  OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'ior', 'code' => 'ior', 'method' => 'getIOPReadingRightNoUnits', 'description' => 'Intraocular pressure, right eye reading no units'));
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'iol', 'code' => 'iol', 'method' => 'getIOPReadingLeftNoUnits', 'description' => 'Intraocular pressure, left eye reading no units'));
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'cnr', 'code' => 'cnr', 'method' => 'getCCTRightNoUnits', 'description' => 'Central corneal thickness, right eye reading no units'));
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'cnl', 'code' => 'cnl', 'method' => 'getCCTLeftNoUnits', 'description' => 'Central corneal thickness, left eye reading no units'));
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getIOPReadingRightNoUnits'));
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getIOPReadingLeftNoUnits'));
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getCCTRightNoUnits'));
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getCCTLeftNoUnits'));
    }
}
