<?php

class m160114_155508_add_series_instance_id extends CDbMigration
{
    public function up()
    {
        $this->addColumn('dicom_import_log', 'series_instance_id', 'varchar(255)');
        $this->addColumn('ophinbiometry_imported_events', 'series_id', 'varchar(255)');
    }

    public function down()
    {
        $this->dropColumn('dicom_import_log', 'series_instance_id');
        $this->dropColumn('ophinbiometry_imported_events', 'series_id');
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
