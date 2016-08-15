<?php

class m151216_135138_add_content_time_surgeon extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophinbiometry_imported_events', 'content_datetime', 'datetime after is_merged');
        $this->addColumn('ophinbiometry_imported_events', 'surgeon_name', 'varchar(255) after content_datetime');
    }

    public function down()
    {
        $this->dropColumn('ophinbiometry_imported_events', 'surgeon_name');
        $this->dropColumn('ophinbiometry_imported_events', 'content_datetime');
    }
}
