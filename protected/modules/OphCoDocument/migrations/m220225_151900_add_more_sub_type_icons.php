<?php

class m220225_151900_add_more_sub_type_icons extends CDbMigration
{
    public function up()
    {
        $icon_names = ['i-aM', 'i-aW', 'i-aC', 'i-aC', 'i-aD', 'i-aA', 'i-aV'];

        foreach ($icon_names as $key => $event_icon) {
            // original migration finished at 1180, so starting this one from 1190
            $key = ($key + 190) * 10;
            $this->insert('event_icon', ['name' => $event_icon, 'display_order' => $key]);
        }
    }

    public function down()
    {
        $this->dropTable('event_icon');
    }
}
