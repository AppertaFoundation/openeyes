<?php

class m181025_135400_add_user_and_admin_banner_short_to_config extends CDbMigration
{
    public function up()
    {
        $settings = array(
            array(
                'key' => 'watermark_short',
                'name' => 'User Banner short',
                'data' => '',
                'default_value' => '',
                'display_order' => 3
            ),
            array(
                'key' => 'watermark_admin_short',
                'name' => 'Admin Banner short',
                'data' => '',
                'default_value' => '',
                'display_order' => 5
            ),
        );
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        if (isset($id['id']) && $id['id']) {
            foreach ($settings as $setting) {
                $setting['field_type_id'] = $id['id'];
                $this->insert('setting_metadata', $setting);
            }
        }
        $this->insert('setting_installation', array('key' => 'watermark_short', 'value' => ''));
        $this->insert('setting_installation', array('key' => 'watermark_admin_short', 'value' => ''));
    }
}
