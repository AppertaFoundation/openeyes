<?php

class m220211_083440_change_generated_columns_to_normal_stored_columns extends OEMigration
{
    public function safeUp()
    {
        $tables_to_change = [
            'patient_identifier_type' => 'uk_unique_row_str',
            'patient_identifier_type_display_order' =>'uk_unique_row_str',
            'patient_identifier' =>  'uk_patient_identifier_unique_row_str'];

        $new_column_name = 'unique_row_string';

        foreach ($tables_to_change as $table_to_change => $index_key) {
            $this->dropIndex($index_key, $table_to_change);
            $this->addOEColumn($table_to_change , $new_column_name , 'varchar(255) NOT NULL',true);
            $this->dbConnection->createCommand("UPDATE $table_to_change
                                            SET unique_row_string  = unique_row_str
                                            WHERE id = id")->query();
            $this->dropOEColumn($table_to_change, 'unique_row_str',true);
            $this->createIndex($index_key, $table_to_change, $new_column_name, true);
        }
    }

    public function safeDown()
    {
        echo "m220211_083440_change_generated_columns_to_normal_stored_columns does not support migration down.\n";
        return false;
    }
}
