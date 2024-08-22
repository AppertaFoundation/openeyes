<?php

class m170201_114016_add_letter_type_to_document_log extends OEMigration
{
    public function safeUp()
    {
            $this->createOETable('ophcocorrespondence_letter_type', array(
                'id' => 'pk',
                'name' => 'varchar(40)'
            ));

            $this->insert('ophcocorrespondence_letter_type', array( 'id' => 1, 'name' => 'Clinic discharge letter'));
            $this->insert('ophcocorrespondence_letter_type', array( 'id' => 2, 'name' => 'Post-op letter'));
            $this->insert('ophcocorrespondence_letter_type', array( 'id' => 3, 'name' => 'Clinic letter'));
            $this->insert('ophcocorrespondence_letter_type', array( 'id' => 4, 'name' => 'Other letter'));

            $this->addColumn('document_log', 'letter_type', 'VARCHAR(40) DEFAULT NULL AFTER clinician_name');

            //letter type is not mandatory
            $this->alterColumn('et_ophcocorrespondence_letter', 'letter_type', 'int(1) DEFAULT NULL');
            $this->alterColumn('et_ophcocorrespondence_letter_version', 'letter_type', 'int(1) DEFAULT NULL');
            $this->alterColumn('ophcocorrespondence_letter_macro', 'letter_type', 'int(1) DEFAULT NULL');
            $this->alterColumn('ophcocorrespondence_letter_macro_version', 'letter_type', 'int(1) DEFAULT NULL');

            //set back letter_type to NULL
            $this->update('et_ophcocorrespondence_letter', array('letter_type' => null), 'letter_type=0');
            $this->update('et_ophcocorrespondence_letter_version', array('letter_type' => null), 'letter_type=0');
            $this->update('ophcocorrespondence_letter_macro', array('letter_type' => null), 'letter_type=0');
            $this->update('ophcocorrespondence_letter_macro_version', array('letter_type' => null), 'letter_type=0');

            $this->renameColumn('et_ophcocorrespondence_letter', 'letter_type', 'letter_type_id');
            $this->renameColumn('et_ophcocorrespondence_letter_version', 'letter_type', 'letter_type_id');

            $this->renameColumn('ophcocorrespondence_letter_macro', 'letter_type', 'letter_type_id');
            $this->renameColumn('ophcocorrespondence_letter_macro_version', 'letter_type', 'letter_type_id');

            $this->addForeignKey('et_ophcocorrespondence_letter_ibfk_1', 'et_ophcocorrespondence_letter', 'letter_type_id', 'ophcocorrespondence_letter_type', 'id');
            $this->addForeignKey('ophcocorrespondence_letter_macro_ibfk_1', 'ophcocorrespondence_letter_macro', 'letter_type_id', 'ophcocorrespondence_letter_type', 'id');
    }

    public function safeDown()
    {
            $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_1', 'et_ophcocorrespondence_letter');
            $this->dropForeignKey('ophcocorrespondence_letter_macro_ibfk_1', 'ophcocorrespondence_letter_macro');

            $this->dropOETable('ophcocorrespondence_letter_type');
            $this->dropColumn('document_log', 'letter_type');

            $this->update('et_ophcocorrespondence_letter', array('letter_type_id' => 0), 'letter_type_id IS NULL');
            $this->update('et_ophcocorrespondence_letter_version', array('letter_type_id' => 0), 'letter_type_id IS NULL');
            $this->update('ophcocorrespondence_letter_macro', array('letter_type_id' => 0), 'letter_type_id IS NULL');
            $this->update('ophcocorrespondence_letter_macro_version', array('letter_type_id' => 0), 'letter_type_id IS NULL');

            $this->alterColumn('et_ophcocorrespondence_letter', 'letter_type_id', 'int(1) NOT NULL DEFAULT 0');
            $this->alterColumn('et_ophcocorrespondence_letter_version', 'letter_type_id', 'int(1) NOT NULL DEFAULT 0');
            $this->alterColumn('ophcocorrespondence_letter_macro', 'letter_type_id', 'int(1) NOT NULL DEFAULT 0');
            $this->alterColumn('ophcocorrespondence_letter_macro_version', 'letter_type_id', 'int(1) NOT NULL DEFAULT 0');

            $this->renameColumn('et_ophcocorrespondence_letter', 'letter_type_id', 'letter_type');
            $this->renameColumn('et_ophcocorrespondence_letter_version', 'letter_type_id', 'letter_type');

            $this->renameColumn('ophcocorrespondence_letter_macro', 'letter_type_id', 'letter_type');
            $this->renameColumn('ophcocorrespondence_letter_macro_version', 'letter_type_id', 'letter_type');
    }
}
