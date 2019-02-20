<?php

class m190201_090710_add_shortcode_csd extends CDbMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', [':class_name' => 'OphCiExamination'])
            ->queryScalar();

        $this->insert('patient_shortcode', [
            'event_type_id' => $event_type_id,
            'code' => 'csd',
            'default_code' => 'csd',
            'method' => 'getCurrentSystemicDrugs',
            'description' => 'Current Systemic Drugs',
        ]);
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'code = ?', ['csd']);
    }
}