<?php

class m170917_101700_add_system_setting_worklist_future_days extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 4,
            'key' => 'worklist_dashboard_future_days',
            'name' => 'Number of days in the future to display clinic lists on home screen',
            'default_value' => 2
        ));

        $this->insert('setting_installation', array(
            'key' => 'worklist_dashboard_future_days',
            'value' => '2'
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key`="worklist_dashboard_future_days"');
        $this->delete('setting_metadata', '`key`="worklist_dashboard_future_days"');
    }
}
