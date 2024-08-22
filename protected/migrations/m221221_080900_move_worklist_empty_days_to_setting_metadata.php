<?php

class m221221_080900_move_worklist_empty_days_to_setting_metadata extends OEMigration
{
    public function safeUp()
    {
        $this->addSetting(
            'worklist_show_empty',
            'Show empty worklists in worklist manager',
            'When ON, worklists with no patients booked will be shown in the worklist manager. When OFF, empty worklists will be hidden. There is a slight performance advantage in hiding the empty worklists',
            'Worklists',
            'Radio buttons',
            'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'off',
            'INSTALLATION'
        );
    }

    public function safeDown()
    {
        $this->deleteSetting('worklist_show_empty');
    }
}
