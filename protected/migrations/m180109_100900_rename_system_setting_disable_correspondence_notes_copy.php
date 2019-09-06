<?php

class m180109_100900_rename_system_setting_disable_correspondence_notes_copy extends CDbMigration
{
    public function up()
    {

        # Check that these values do not already exist
        $ismetadata = $this->dbConnection->createCommand()->select('id')->from('setting_metadata')->where('`key` = :setting_key', array(':setting_key' => 'disable_correspondence_notes_copy'))->queryRow();
        # Insert values if they don't already exist
        if ($ismetadata['id'] == '') {
            $this->insert('setting_metadata', array(
                'display_order' => 0,
                'field_type_id' => 3,
                'key' => 'disable_print_notes_copy',
                'name' => 'Disable additional copy for notes when printing',
                'data' => serialize(array('on'=>'On', 'off'=>'Off')),
                'default_value' => 'on'
            ));
            $this->insert('setting_installation', array(
                'key' => 'disable_print_notes_copy',
                'value' => 'on'
            ));
        } else {
            ## Update the old correspondence setting to a name more befitting a core feature
            $this->update('setting_metadata', array('key' => 'disable_print_notes_copy', 'name' => 'Disable additional copy for notes when printing'), '`key` = :setting_key', array(':setting_key' => 'disable_correspondence_notes_copy'));
            $this->update('setting_installation', array('key' => 'disable_print_notes_copy'), '`key` = :setting_key', array(':setting_key' => 'disable_correspondence_notes_copy'));
        }




    }

    public function down()
    {
        $this->update('setting_metadata', array('key' => 'disable_correspondence_notes_copy', 'name' => 'Disable additional copy for notes when printing correspondence'), '`key` = :setting_key', array(':setting_key' => 'disable_print_notes_copy'));
        $this->update('setting_installation', array('key' => 'disable_correspondence_notes_copy'), '`key` = :setting_key', array(':setting_key' => 'disable_print_notes_copy'));

    }
}
