<?php

class m170621_195228_VA_shortcodes extends CDbMigration
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
            'default_code' => 'bvd',
            'code' => 'bvd',
            'method' => 'getLetterVisualAcuityBothLast3weeks',
            'description' => 'Best visual acuity in both eyes with date (Latest recorded within the last 3 weeks)',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'lvd',
            'code' => 'lvd',
            'method' => 'getLetterVisualAcuityLeftLast3weeks',
            'description' => 'Best visual acuity in the left eye with date (Latest recorded within the last 3 weeks)',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pvd',
            'code' => 'pvd',
            'method' => 'getLetterVisualAcuityPrincipalLast3weeks',
            'description' => 'Best visual acuity in the principle eye with date (Latest recorded within the last 3 weeks)',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'rvd',
            'code' => 'rvd',
            'method' => 'getLetterVisualAcuityRightLast3weeks',
            'description' => 'Best visual acuity in the right eye with date (Latest recorded within the last 3 weeks)',
            'last_modified_user_id' => '1',
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', '`default_code`="bvd"');
        $this->delete('patient_shortcode', '`default_code`="lvd"');
        $this->delete('patient_shortcode', '`default_code`="pvd"');
        $this->delete('patient_shortcode', '`default_code`="rvd"');
    }
}
