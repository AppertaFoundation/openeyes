<?php

class m230328_123721_pin_requirement_setting_updates extends OEMigration
{
    public function safeUp()
    {
        foreach (['require_pin_for_consent', 'require_pin_for_correspondence',
                     'require_pin_for_prescription', 'require_pin_for_cvi'] as $key) {
            $this->update('setting_installation', ['value' => 1], '`key`=:key AND  value ="Yes"' ,
                [':key' => $key]);
        }
    }
    public function safeDown(): bool
    {
        return true;
    }
}
