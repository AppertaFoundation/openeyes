<?php

class m180416_133000_add_sop_uid_colum extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophinbiometry_imported_events', 'sop_uid', 'varchar(255)');
        $this->addColumn('dicom_import_log', 'sop_uid', 'varchar(255)');

    }

    public function down()
    {
        $this->dropColumn('et_ophinbiometry_biometrydat', 'sop_uid');
        $this->dropColumn('dicom_import_log', 'sop_uid');
    }
}
