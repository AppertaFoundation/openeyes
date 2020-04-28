<?php

class m170915_132315_add_opnote_lens_import_link_settings extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'opnote_lens_migration_link',
            'name' => 'Enable "Merge operation note cataract element lens data" link on Biometry lens type admin page',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'on'
        ));

        $this->insert('setting_installation', array(
            'key' => 'opnote_lens_migration_link',
            'value' => 'on'
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key`="opnote_lens_migration_link"');
        $this->delete('setting_metadata', '`key`="opnote_lens_migration_link"');
    }
}
