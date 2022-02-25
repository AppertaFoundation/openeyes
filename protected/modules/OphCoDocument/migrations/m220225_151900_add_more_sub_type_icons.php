<?php

class m220225_151900_add_more_sub_type_icons extends CDbMigration
{
    public function up()
    {
        $icon_names = ['i-aM', 'i-aW', 'i-aC', 'i-aC', 'i-aD', 'i-aA', 'i-aV'];

        // set start of display order - original migration ended at 1180
        $key = 1190;

        foreach ($icon_names as $key => $event_icon) {
            $key = ($key + 10);
            $this->insert('event_icon', ['name' => $event_icon, 'display_order' => $key]);
        }
    }

    public function down()
    {
        $this->dropTable('event_icon');
    }
}
