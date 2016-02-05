<?php

class m141024_105644_iot_shortcode extends  OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->insert('patient_shortcode', array('event_type_id' => $event_type_id, 'default_code' => 'iot', 'code' => 'iot', 'method' => 'getIOPValuesAsTable', 'description' => 'Intraocular pressure values returned in a tabular format'));
        $this->addColumn('ophciexamination_instrument', 'short_name', 'varchar(30) after name');
        $this->addColumn('ophciexamination_instrument_version', 'short_name', 'varchar(30) after name');
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $this->delete('patient_shortcode', 'event_type_id = ? and method = ?', array($event_type_id, 'getIOPValuesAsTable'));
        $this->dropColumn('ophciexamination_instrument', 'short_name');
        $this->dropColumn('ophciexamination_instrument_version', 'short_name');
    }
}
