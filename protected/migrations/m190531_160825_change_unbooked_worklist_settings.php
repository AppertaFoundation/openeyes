<?php

class m190531_160825_change_unbooked_worklist_settings extends CDbMigration
{
    public function up()
    {
        $this->delete('setting_metadata', "`key` = 'worklist_search_appt_within'");
        $this->delete('setting_installation', "`key` = 'worklist_search_appt_within'");

        $this->insert('setting_metadata', ['element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'worklist_past_search_days',
                'name' => 'Search worklist past appointment within (days)',
                'default_value' => '30',
                'data' => ''
            ]);

        $this->insert('setting_metadata', ['element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'worklist_future_search_days',
                'name' => 'Search worklist future appointment within (days)',
                'default_value' => '30',
                'data' => ''
            ]);

        $this->insert('setting_installation', ['key' => 'worklist_past_search_days', 'value' => '30']);
        $this->insert('setting_installation', ['key' => 'worklist_future_search_days', 'value' => '30']);
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = ?', ["worklist_future_search_days"]);
        $this->delete('setting_metadata', '`key` = ?', ["worklist_past_search_days"]);

        $this->delete('setting_installation', '`key` = ?', ["worklist_future_search_days"]);
        $this->delete('setting_installation', '`key` = ?', ["worklist_past_search_days"]);

        $this->insert('setting_installation', ['key' => 'worklist_search_appt_within', 'value' => '30']);

        $this->insert('setting_metadata', ['element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'worklist_search_appt_within',
                'name' => 'Search worklist appointment within (days)',
                'default_value' => '30',
                'data' => ''
            ]);

    }
}
