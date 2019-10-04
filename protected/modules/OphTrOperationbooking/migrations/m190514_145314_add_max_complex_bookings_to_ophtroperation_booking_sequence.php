<?php

class m190514_145314_add_max_complex_bookings_to_ophtroperation_booking_sequence extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('ophtroperationbooking_operation_sequence', 'max_complex_bookings', 'tinyint DEFAULT NULL');
        $this->addColumn('ophtroperationbooking_operation_sequence_version', 'max_complex_bookings', 'tinyint DEFAULT NULL');
        $this->addColumn('ophtroperationbooking_operation_sequence', 'max_procedures', 'tinyint DEFAULT NULL');
        $this->addColumn('ophtroperationbooking_operation_sequence_version', 'max_procedures', 'tinyint DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('ophtroperationbooking_operation_sequence_version', 'max_procedures');
        $this->dropColumn('ophtroperationbooking_operation_sequence', 'max_procedures');
        $this->dropColumn('ophtroperationbooking_operation_sequence_version', 'max_complex_bookings');
        $this->dropColumn('ophtroperationbooking_operation_sequence', 'max_complex_bookings');
    }
}