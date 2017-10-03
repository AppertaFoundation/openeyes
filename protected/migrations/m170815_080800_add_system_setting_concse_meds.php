<?php

class m170815_080800_add_system_setting_concse_meds extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'enable_concise_med_history',
            'name' => 'Require Dose/Freq for systemic meds',
            'data' => serialize(array('on'=>'On', 'off'=>'Off'))
        ));

        $this->insert('setting_installation', array(
            'key' => 'enable_concise_med_history',
            'value' => 'off'
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key`="enable_concise_med_history"');
        $this->delete('setting_metadata', '`key`="enable_concise_med_history"');
    }
}
