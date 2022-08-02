<?php

class m220729_121200_remove_duplicate_setting_overrides extends OEMigration
{
    public function safeUp()
    {
        // removes any institution overrides that are the same as the installation level.
        // This prevents the unexpected behaviour where a setting is changed at the installation level
        // , but it doesn't take effect becuase there was a default override set from an earlier migration

        $this->dbConnection->createCommand("
            DELETE sint 
            FROM setting_institution sint INNER JOIN setting_installation si ON sint.`key` = si.`key`
            WHERE sint.value = si.value;")->execute();

        // Delete an old copy of the sample data reset that may have made it into the institution table during an old migration
        $this->dbConnection->createCommand("
            DELETE FROM setting_institution
            WHERE `key` = 'watermark' 
                AND `value` like '%Database reset at%';")->execute();
    }

    public function down()
    {
        echo "down not supported for this migration";
    }
}
