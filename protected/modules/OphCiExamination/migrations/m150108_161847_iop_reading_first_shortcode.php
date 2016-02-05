<?php

class m150108_161847_iop_reading_first_shortcode extends  OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'iof', 'code' => 'iof', 'method' => 'getLetterIOPReadingBothFirst', 'description' => 'Intraocular pressure, both eyes, first reading only'));
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getLetterIOPReadingBothFirst'));
    }
}
