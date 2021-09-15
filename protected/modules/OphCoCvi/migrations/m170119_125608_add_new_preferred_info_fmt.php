<?php

class m170119_125608_add_new_preferred_info_fmt extends CDbMigration
{
    public function up()
    {
        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', array(
            'name' => 'No Preference',
            'require_email' => '0',
            'active' => '1',
            'display_order' => '5',
            'code' => 'NOPREF',
            'last_modified_user_id' => '1',
            'created_user_id' => '1',
            'deleted' => '0',
        ));
    }

    public function down()
    {
        $this->delete('ophcocvi_clericinfo_preferred_info_fmt', '`name`="No Preference"');
    }
}
