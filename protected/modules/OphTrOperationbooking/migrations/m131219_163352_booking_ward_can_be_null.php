<?php

class m131219_163352_booking_ward_can_be_null extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('ophtroperationbooking_operation_booking', 'ward_id', 'int(10) unsigned null');
    }

    public function down()
    {
        $this->alterColumn('ophtroperationbooking_operation_booking', 'ward_id', 'int(10) unsigned not null');
    }
}
