<?php

class m160818_124651_add_device_serial_and_acquisition_datetime extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophinbiometry_imported_events', 'device_serial_number', 'varchar(20)');
        $this->addColumn('ophinbiometry_imported_events', 'acquisition_datetime', 'varchar(25)');
    }

    public function down()
    {
        $this->dropColumn('ophinbiometry_imported_events', 'device_serial_number');
        $this->dropColumn('ophinbiometry_imported_events', 'acquisition_datetime');
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
