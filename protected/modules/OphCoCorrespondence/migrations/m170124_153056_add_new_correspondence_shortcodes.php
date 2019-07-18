<?php

class m170124_153056_add_new_correspondence_shortcodes extends CDbMigration
{
    public function up()
    {

        $data = $this->dbConnection->createCommand()
            ->select('id')->from('event_type')
            ->where('name = "Correspondence"')->queryAll();
        $event_type_id = $data[0]['id'];

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pna',
            'code' => 'pna',
            'method' => 'getFullName',
            'description' => 'Get Full Patient Name',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pnt',
            'code' => 'pnt',
            'method' => 'getPatientTitle',
            'description' => 'Get Patient Title',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pnf',
            'code' => 'pnf',
            'method' => 'getFirstName',
            'description' => 'Get Patient First Name',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pnl',
            'code' => 'pnl',
            'method' => 'getLastName',
            'description' => 'Get Patient Last Name',
            'last_modified_user_id' => '1',
        ));

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'eld',
            'code' => 'eld',
            'method' => 'getLastExaminationDate',
            'description' => 'Get Date of Last Examination Date',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'aod',
            'code' => 'aod',
            'method' => 'getOphthalmicDiagnoses',
            'description' => 'List of Ophthalmic Diagnoses',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'ilt',
            'code' => 'ilt',
            'method' => 'getLastIOLType',
            'description' => 'IOL type from last cataract Operation Note',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'ilp',
            'code' => 'ilp',
            'method' => 'getLastIOLPower',
            'description' => 'IOL Power from last cataract operation note',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'loe',
            'code' => 'loe',
            'method' => 'getLastOperatedEye',
            'description' => 'Operated Eye (left/right) from last operation note',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pov',
            'code' => 'pov',
            'method' => 'getPreOpVABothEyes',
            'description' => 'Pre-Op Visual Acuity - both eyes',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'por',
            'code' => 'por',
            'method' => 'getPreOpRefraction',
            'description' => 'Pre-Op Refraction - both eyes',
            'last_modified_user_id' => '1',
        ));
        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'alg',
            'code' => 'alg',
            'method' => 'getAllergiesBulleted',
            'description' => 'List all patients assigned allergies',
            'last_modified_user_id' => '1',
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', '`default_code`="pna"');
        $this->delete('patient_shortcode', '`default_code`="pnt"');
        $this->delete('patient_shortcode', '`default_code`="pnf"');
        $this->delete('patient_shortcode', '`default_code`="pnl"');
        $this->delete('patient_shortcode', '`default_code`="ilp"');
        $this->delete('patient_shortcode', '`default_code`="ilt"');
        $this->delete('patient_shortcode', '`default_code`="aod"');
        $this->delete('patient_shortcode', '`default_code`="eld"');
        $this->delete('patient_shortcode', '`default_code`="loe"');
        $this->delete('patient_shortcode', '`default_code`="pov"');
        $this->delete('patient_shortcode', '`default_code`="por"');
        $this->delete('patient_shortcode', '`default_code`="alg"');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}