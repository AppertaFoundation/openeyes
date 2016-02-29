<?php

class m140908_155528_abbr_shortcodes extends OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'ipa', 'code' => 'ipa', 'method' => 'getLetterIOPReadingAbbr', 'description' => 'Intraocular pressure, abbreviated form'));
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'cta', 'code' => 'cta', 'method' => 'getCCTAbbr', 'description' => 'Central corneal thickness, abbreviated form'));
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getLetterIOPReadingAbbr'));
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getCCTAbbr'));
    }
}
