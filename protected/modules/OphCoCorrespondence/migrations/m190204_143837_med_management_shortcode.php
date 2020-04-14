<?php

class m190204_143837_med_management_shortcode extends CDbMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))
            ->queryScalar();

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'mms',
            'code' => 'mms',
            'method' => 'getMedicationManagementSummary',
            'description' => 'This is a combined summary of [dst], [dsp], [dct] shortcodes.',
            'last_modified_user_id' => '1',
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', '`default_code`="mms"');
    }
}
