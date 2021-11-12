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
            if ($this->dbConnection->schema->getTable($table, true)) {
                $changed_table = strlen($table) <= 55 ? $table : substr($table, 0, 55);
                $version_table = $table.'_version';
                $changed_version_table = strlen($changed_table) <= 47 ? $changed_table.'_version' : substr($changed_table, 0, 47).'_version';
                $this->renameTable($table, 'archive_'.$changed_table);
                $this->renameTable($version_table, 'archive_'.$changed_version_table);
            }
        }
        $this->deleteElementType('OphTrConsent', 'Element_OphTrConsent_Other');
        $this->deleteElementType('OphTrConsent', 'Element_OphTrConsent_Permissions');
    }

    public function safeDown()
    {
        foreach (self::OLD_TABLES as $table) {
            if ($this->dbConnection->schema->getTable('archive_'.$table, true)) {
                $changed_table = strlen($table) <= 55 ? $table : substr($table, 0, 55);
                $version_table = $table.'_version';
                $changed_version_table = strlen($changed_table) <= 47 ? $changed_table.'_version' : substr($changed_table, 0, 47).'_version';
                $this->renameTable('archive_'.$changed_table, $table);
                $this->renameTable('archive_'.$changed_version_table, $version_table);
            }
        }

        $this->createElementType(
            'OphTrConsent',
            'Other',
            array(
                'class_name'    => 'Element_OphTrConsent_Other',
                'display_order' => 60,
                'default'       => 1,
                'required'  => 1,
            )
        );

        $this->createElementType(
            'OphTrConsent',
            'Permissions for images',
            array(
                'class_name'    => 'Element_OphTrConsent_Permissions',
                'display_order' => 50,
                'default'       => 1,
                'required'  => 1,
            )
        );
    }
}
