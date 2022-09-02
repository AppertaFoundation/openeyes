<?php

class m220825_124636_add_event_subtype_to_message_type extends OEMigration
{
    public function safeUp()
    {
        // If the message type table doesn't have an event_subtype column then add it now.
        // It may have already been added by migration m220810_145245_archive_module in SupCoPhoneLog modile
        if (!$this->verifyColumnExists('ophcomessaging_message_message_type', 'event_subtype')) {
            $this->addOEColumn('ophcomessaging_message_message_type', 'event_subtype', 'varchar(100) NULL', true);
        }
    }

    public function safeDown()
    {
        // No down provided - additional column is benign, and repetition of the up step will not fail due to column verification
        return true;
    }

    /**
     * Checks if a named column exists on a named table
     * @param $table_name Name of he table to check for the column on
     * @param $column_name Name of the column to check for
     * @return bool true if the column exists
     *
     * NOTE: This method should be removed when merged to 6.x, as it is provided by the base class
     */
    protected function verifyColumnExists($table_name, $column_name)
    {
        $cols = $this->dbConnection->createCommand("SHOW COLUMNS FROM `" . $table_name . "` LIKE '" . $column_name . "'")->queryScalar([ ':table_name' => $table_name ]);
        return !empty($cols);
    }
}
