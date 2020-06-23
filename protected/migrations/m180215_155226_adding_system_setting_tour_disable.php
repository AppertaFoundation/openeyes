<?php

class m180215_155226_adding_system_setting_tour_disable extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_installation', array('key' => 'disable_auto_feature_tours', 'value' => 'off'));
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'disable_auto_feature_tours',
            'name' => 'Disable Automatic Feature Tours',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
            'last_modified_user_id' => 1));
    }

    public function down()
    {
        $this->delete('setting_installation', "`key` = 'disable_auto_feature_tours'");
        $this->delete('setting_metadata', "`key` = 'disable_auto_feature_tours'");
    }
}
