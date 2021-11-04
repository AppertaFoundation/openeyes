<?php

class m211103_123000_archive_tables_and_delete_element_type extends OEMigration
{

    const OLD_TABLES = array(
        "et_ophtrconsent_interpreter_signature",
        "et_ophtrconsent_witness_signature",
        "et_ophtrconsent_patient_signature",
        "et_ophtrconsent_parent_signature",
        "et_ophtrconsent_child_signature",
        "et_consultant_signature",
        "ophtrconsent_permissions_images",
        "et_ophtrconsent_permissions",
        "et_ophtrconsent_other",
        "et_ophtrconsent_best_interest_decision_ppl",
        "et_ophtrconsent_best_interest_decision_deputy_signature",
        "et_ophtrconsent_consultant_signature_with_second_opinion",
        );

    public function safeUp()
    {
        foreach (self::OLD_TABLES as $table) {
            if ($this->dbConnection->schema->getTable($table)) {
                $table = strlen($table) <= 55 ? $table : substr($table, 0, 55);
                $version_table = strlen($table) <= 47 ? $table.'_version' : substr($table, 0, 47).'_version';
                $this->renameTable($table, 'archive_'.$table);
                $this->renameTable($version_table, 'archive_'.$version_table);
            }
        }
    }

    public function safeDown()
    {
        foreach (self::OLD_TABLES as $table) {
            if ($this->dbConnection->schema->getTable('archive_'.$table)) {
                $table = strlen($table) <= 55 ? $table : substr($table, 0, 55);
                $version_table = strlen($table) <= 47 ? $table.'_version' : substr($table, 0, 47).'_version';
                $this->renameTable('archive_'.$table, $table);
                $this->renameTable('archive_'.$version_table, $version_table);
            }
        }
    }
}
