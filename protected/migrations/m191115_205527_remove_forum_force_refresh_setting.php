<?php

class m191115_205527_remove_forum_force_refresh_setting extends CDbMigration
{
    public function up()
    {
        $this->delete('setting_installation', '`key` = "forum_force_refresh"');
        $this->delete('setting_metadata', "`key` = 'forum_force_refresh'");
    }

    public function down()
    {
        $this->insert('setting_installation', [
            'key' => 'forum_force_refresh',
            'value' => 'off',
        ]);
        $this->insert('setting_metadata', [
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'forum_force_refresh',
            'name' => 'FORUM: Force patient refresh on every pageload',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
        ]);
    }
}
