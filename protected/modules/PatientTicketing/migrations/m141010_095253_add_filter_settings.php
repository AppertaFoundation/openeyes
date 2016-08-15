<?php

class m141010_095253_add_filter_settings extends CDbMigration
{
    public function up()
    {
        $this->execute('insert into patientticketing_queueset_filter (id) select (id) from patientticketing_queueset');
        $this->execute('update patientticketing_queueset set queueset_filter_id = id');
    }

    public function down()
    {
        $this->execute('update patientticketing_queueset set queueset_filter_id = null');
        $this->execute('delete from patientticketing_queueset_filter');
    }
}
