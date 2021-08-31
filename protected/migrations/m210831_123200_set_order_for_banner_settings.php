<?php

class m210831_123200_set_order_for_banner_settings extends OEMigration
{
    public function safeUp()
    {
        // $this->dbConnection->createCommand("UPDATE setting_metadata  SET display_order=12 WHERE `key` = 'watermark_short';")->execute();
        $this->update('setting_metadata', array('display_order' => 12 ), '`key`=:key', array(':key' => 'watermark_short'));
        $this->update('setting_metadata', array('display_order' => 13 ), '`key`=:key', array(':key' => 'watermark'));
        $this->update('setting_metadata', array('display_order' => 14 ), '`key`=:key', array(':key' => 'watermark_admin_short'));
        $this->update('setting_metadata', array('display_order' => 15 ), '`key`=:key', array(':key' => 'watermark_admin'));
    }

    public function down()
    {
        echo "does not support down";
    }
}
