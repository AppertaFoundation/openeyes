<?php

class m170510_154800_add_doc_instance_to_macro_fk extends OEMigration
{
    public function safeUp()
    {
        // Add the FK between document_instance_data and ophcocorrespondence_letter_macro, but only if it hasn't already been
        // added by the original version of m160915_093448_add_document_management_tables.php

        $fk_exists = $this->dbConnection->createCommand('SELECT count(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = "document_instance_data" AND constraint_name = "fk_document_instance_data_macro_id" AND constraint_type = "FOREIGN KEY"')->queryScalar();
        if (!$fk_exists) {
            $this->addForeignKey('fk_document_instance_data_macro_id', 'document_instance_data', 'macro_id', 'ophcocorrespondence_letter_macro', 'id');
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_document_instance_data_macro_id', 'document_instance_data');
    }
}
