<?php

class m220412_094300_default_draft_auto_print_setting_off extends OEMigration
{
    public function safeUp()
    {

        $this->update('setting_metadata', ['default_value' => 'on'], "`key` = 'disable_draft_auto_print'");

        $this->delete('setting_installation', "`key` = 'disable_draft_auto_print'");
    }

    public function safeDown()
    {
        $this->update('setting_metadata', ['default_value' => 'off'], "`key` = 'disable_draft_auto_print'");
        $this->insert('setting_installation', array('key' => 'disable_draft_auto_print', 'value' => 'off'));
    }
}
