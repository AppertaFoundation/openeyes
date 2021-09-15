<?php

class m160802_112433_add_commissioning_body_for_local_authorities extends CDbMigration
{
    public function up()
    {
        $this->insert('commissioning_body_type', array('name'=>'Local Authorities', 'shortname'=>'LA'));
        $this->insert('commissioning_body_service_type', array('name'=>'Social Services Department', 'shortname'=>'SSD', 'correspondence_name'=>'Social Services Department'));
    }

    public function down()
    {
        $this->delete('commissioning_body_type', "shortname = 'LA'");
        $this->delete('commissioning_body_service_type', "shortname = 'SSD'");
    }
}
