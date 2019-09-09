<?php

class m190402_120500_remove_system_setting_worklist_future_days extends CDbMigration
{
    public function safeUp()
    {
        $this->delete('setting_installation', '`key`="worklist_dashboard_future_days"');
        $this->delete('setting_metadata', '`key`="worklist_dashboard_future_days"');
    }


    public function safeDown()
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
          'value' => '0'
        ));
    }

}
