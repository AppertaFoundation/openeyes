<?php

class m180414_125301_add_system_setting_FORUM extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'enable_forum_integration',
            'name' => 'FORUM: enable integration',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',));
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'forum_force_refresh',
            'name' => 'FORUM: Force patient refresh on every pageload',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',));
        $this->insert('setting_installation', array('key' => 'enable_forum_integration', 'value' => 'off'));
        $this->insert('setting_installation', array('key' => 'forum_force_refresh', 'value' => 'off'));
    }

    public function down()
    {
        $this->delete('setting_installation', "`key` = 'enable_forum_integration'");
        $this->delete('setting_metadata', "`key` = 'forum_force_refresh'");
    }
}
