<?php

class m200311_155430_show_old_oct_element extends CDbMigration
{
    public function safeUp()
    {
        $this->update("element_type", ["name"=>"OCT (manual)"], 'name="OCT (Deprecated)"');
    }

    public function safeDown()
    {
        $this->update("element_type", ["name"=>"OCT (Deprecated)"], 'name="OCT (manual)"');
    }
}
