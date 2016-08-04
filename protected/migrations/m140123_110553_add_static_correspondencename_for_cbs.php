<?php

class m140123_110553_add_static_correspondencename_for_cbs extends CDbMigration
{
    public function up()
    {
        $this->addColumn('commissioning_body_service_type', 'correspondence_name', 'varchar(255)');
    }

    public function down()
    {
        $this->dropColumn('commissioning_body_service_type', 'correspondence_name');
    }
}
