<?php

class m201023_042621_change_message_type_display_order extends CDbMigration
{
    public function safeUp()
    {
        $this->update('ophcomessaging_message_message_type', ['display_order' => '1'], "name = 'Query'");
        $this->update('ophcomessaging_message_message_type', ['display_order' => '2'], "name = 'General'");
    }

    public function safeDown()
    {
        $this->update('ophcomessaging_message_message_type', ['display_order' => '2'], "name = 'Query'");
        $this->update('ophcomessaging_message_message_type', ['display_order' => '1'], "name = 'General'");
    }
}
