<?php

class m180404_082154_add_opbooking_preass_booking_req extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'preassessment_booking_required', 'TINYINT NOT NULL DEFAULT 0');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'preassessment_booking_required', 'TINYINT NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'preassessment_booking_required');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'preassessment_booking_required');
    }

}