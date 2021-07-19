<?php

class m210323_032502_add_worklist_sync_interval_setting extends OEMigration
{
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Radio buttons"')->queryRow();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'worklist_auto_sync_interval',
            'name' => 'Worklist Auto Sync Interval',
            'data' => serialize(
                array(
                    '10'=>'10 Seconds',
                    '30'=>'30 Seconds',
                    '60'=>'1 Minute',
                    '300'=>'5 Minutes',
                    '600'=>'10 Minutes',
                    'off'=>'Stop Auto Sync'
                )
            ),
            'default_value' => '60',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "worklist_auto_sync_interval"');
    }
}
