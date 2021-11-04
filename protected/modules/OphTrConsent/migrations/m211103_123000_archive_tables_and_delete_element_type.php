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
                $version_table = $table.'_version';
                $this->renameTable($table, 'archive_'.$table);
                $this->renameTable($version_table, 'archive_'.$version_table);
            }
        }
    }

    public function safeDown()
    {
        foreach (self::OLD_TABLES as $table) {
            if ($this->dbConnection->schema->getTable($table)) {
                $version_table = $table.'_version';
                $version_table = strlen($version_table) > 63 ? $version_table : substr($version_table, 0, 63);
                $this->renameTable('archive_'.$table, $table);
                $this->renameTable('archive_'.$version_table, $version_table);
            }
        }
    }
}
