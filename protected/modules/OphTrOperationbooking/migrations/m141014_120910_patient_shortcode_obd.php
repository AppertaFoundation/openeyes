<?php

class m141014_120910_patient_shortcode_obd extends CDbMigration
{
    public function up()
    {
        $et = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphTrOperationbooking'))->queryRow();

        $this->update('patient_shortcode', array('method' => 'getLatestCompletedOperationBookingDiagnosis'), "event_type_id = {$et['id']} and code = 'obd'");
    }

    public function down()
    {
        $et = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphTrOperationbooking'))->queryRow();

        $this->update('patient_shortcode', array('method' => 'getLatestOperationBookingDiagnosis'), "event_type_id = {$et['id']} and code = 'obd'");
    }
}
