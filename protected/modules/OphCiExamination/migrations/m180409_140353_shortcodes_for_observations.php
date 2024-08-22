<?php

class m180409_140353_shortcodes_for_observations extends CDbMigration
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
            'default_code' => 'lbp',
            'code' => 'lbp',
            'method' => 'getLastBloodPressure',
            'description' => 'Last BP (returned as systolic / diastolic - e.g, 100/80)',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'lo2',
            'code' => 'lo2',
            'method' => 'getLastO2Stat',
            'description' => 'Last O2 Stat',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'lbg',
            'code' => 'lbg',
            'method' => 'getLastBloodGlucose',
            'description' => 'Last Blood Glucose',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'lh1',
            'code' => 'lh1',
            'method' => 'getLastHbA1c',
            'description' => 'Last HbA1c',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'lht',
            'code' => 'lht',
            'method' => 'getLastHeight',
            'description' => 'Last Height',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'lwt',
            'code' => 'lwt',
            'method' => 'getLastWeight',
            'description' => 'Last Weight',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'bmi',
            'code' => 'bmi',
            'method' => 'getLastBMI',
            'description' => 'Last BMI',
            'last_modified_user_id' => '1',
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', '`default_code`="bmi"');
        $this->delete('patient_shortcode', '`default_code`="lwt"');
        $this->delete('patient_shortcode', '`default_code`="lht"');
        $this->delete('patient_shortcode', '`default_code`="lh1"');
        $this->delete('patient_shortcode', '`default_code`="lbg"');
        $this->delete('patient_shortcode', '`default_code`="lo2"');
        $this->delete('patient_shortcode', '`default_code`="lbp"');
    }
}
