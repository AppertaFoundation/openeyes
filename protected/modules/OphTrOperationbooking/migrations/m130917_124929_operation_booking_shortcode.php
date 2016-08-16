<?php

class m130917_124929_operation_booking_shortcode extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphTrOperationbooking'))->queryRow();

        $this->insert('patient_shortcode', array('event_type_id' => $event_type['id'], 'default_code' => 'obd', 'code' => 'obd', 'description' => 'The latest operation booking diagnosis', 'method' => 'getLatestOperationBookingDiagnosis'));
    }

    public function down()
    {
        $this->delete('patient_shortcode', "default_code = 'obd'");
    }
}
