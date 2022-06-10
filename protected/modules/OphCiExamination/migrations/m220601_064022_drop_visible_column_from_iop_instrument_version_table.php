<?php

/**
 * This is based on original migration m211203_002947_drop_visibility_column_in_ophciexamination_instrument
 * where in some environments the 'visible' column was not dropped from the version table of ophciexamination_instrument.
 */

class m220601_064022_drop_visible_column_from_iop_instrument_version_table extends OEMigration
{
    public function up()
    {
        $version_table_obj = $this->dbConnection->schema->getTable('ophciexamination_instrument_version', true);

        if ($version_table_obj) {
            $version_tbl_cols = $version_table_obj->getColumnNames();

            if (in_array('visible', $version_tbl_cols) !== false) {
                $this->dropColumn('ophciexamination_instrument_version', 'visible');
            }
        }
    }

    public function down()
    {
        echo "m220601_064022_drop_visible_column_from_iop_instrument_version_table does not support migration down.\n";
        return false;
    }
}
