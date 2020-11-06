<?php

class m201030_134926_add_changed_medication_shortcode_dcc extends OEMigration
{

    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))
            ->queryScalar();

        $this->registerShortcode($event_type_id, 'dcc', 'getLetterDrugsChangedToday', 'Drugs changed today');
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'default_code = :code', array(':code' => 'dcc'));
    }
}
