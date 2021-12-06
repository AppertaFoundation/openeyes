<?php

class m211112_052225_add_service_firm_filtering_for_correspondence_internal_referral extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'filter_service_firms_internal_referral',
            'name' => 'Display only service firms in correspondence internal referral',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'off'
        ));
        $this->insert('setting_installation', array('key' => 'filter_service_firms_internal_referral', 'value' => 'off'));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', "`key` = 'filter_service_firms_internal_referral'");
        $this->delete('setting_installation', "`key` = 'filter_service_firms_internal_referral'");
    }
}
