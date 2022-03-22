<?php

class m201026_135422_update_row_format extends CDbMigration
{
    public function up()
    {
        /**
         * Some older databases still use the 'Compact' row_format, which only supports row lengths up to 8196 bytes
         * Since MySQL 5.6 the default format changed to 'Dynamic', which supports row lengths of 65535 bytes
         *
         * This migration will update any old tables still set as 'Compact' to the newer 'Dynamic' format
         */

        $tables = $this->dbConnection->createCommand("SELECT TABLE_NAME
													FROM information_schema.TABLES
													WHERE TABLE_SCHEMA = database()
													AND ENGINE = 'InnoDB'
													AND ROW_FORMAT IN('Redundant', 'Compact')")->queryAll();
        foreach ($tables as $table) {
            echo "Updating ROW_FORMAT for " . $table['TABLE_NAME'] . "\n";
            $this->dbConnection->createCommand("ALTER TABLE `" . $table['TABLE_NAME'] . "` ROW_FORMAT=dynamic")->execute();
        }
    }

    public function down()
    {
        echo "m201026_135422_update_row_format does not support migration down.\n";
        return false;
    }
}
