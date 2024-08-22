<?php

class m220506_031611_remove_empty_institution_settings extends OEMigration
{
    public function safeUp()
    {
        $this->dbConnection->createCommand("DELETE FROM setting_institution WHERE `value` = '' OR `value` IS NULL")->execute();
    }

    public function safeDown()
    {
        echo "m220506_031611_remove_empty_institution_settings does not support migration down.\n";
        return false;
    }
}
