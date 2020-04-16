<?php

class m160930_130953_OphTrOperationbooking_short_codes extends CDbMigration
{
    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphTrOperationbooking'))
            ->queryScalar();

        $this->insert('patient_shortcode', array(
            'event_type_id' => $eventTypeId,
            'default_code' => 'opf',
            'code' => 'opf',
            'method' => 'getAllBookingsWithoutOperationNotes',
            'description' => "All Operation Bookings WITHOUT Operation Notes",
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'default_code = :code', array(':code' => 'opf'));
    }

}
