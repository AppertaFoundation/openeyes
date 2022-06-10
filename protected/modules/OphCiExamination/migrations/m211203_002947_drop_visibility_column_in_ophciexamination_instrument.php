<?php

class m211203_002947_drop_visibility_column_in_ophciexamination_instrument extends OEMigration
{
    private const TABLE_NAME = 'ophciexamination_instrument';
    private const VERSION_TABLE_NAME = 'ophciexamination_instrument_version';
    private const TARGET_COLUMN = 'visible';
    public function up()
    {
        // getting main table and version table objects
        $table_obj = $this->dbConnection->schema->getTable(self::TABLE_NAME, true);
        $version_table_obj = $this->dbConnection->schema->getTable(self::VERSION_TABLE_NAME, true);
        // check if tables exist
        if ($table_obj && $version_table_obj) {
            // getting column names from main table and version table respectively
            $tbl_cols = $table_obj->getColumnNames();
            $version_tbl_cols = $version_table_obj->getColumnNames();

            // drop the target column if it exists in the main table
            if (in_array(self::TARGET_COLUMN, $tbl_cols) !== false) {
                $this->dropColumn(self::TABLE_NAME, self::TARGET_COLUMN);
            }
            // drop the target column if it exists in the version table
            if (in_array(self::TARGET_COLUMN, $version_tbl_cols) !== false) {
                $this->dropColumn(self::VERSION_TABLE_NAME, self::TARGET_COLUMN);
            }
        }
    }

    public function down()
    {
        echo "m211203_002947_drop_visibility_column_in_ophciexamination_instrument does not support migration down.\n";
        return false;
    }
}
