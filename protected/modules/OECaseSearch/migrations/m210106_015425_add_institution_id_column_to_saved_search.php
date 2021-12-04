<?php

class m210106_015425_add_institution_id_column_to_saved_search extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addOEColumn('case_search_saved_search', 'institution_id', 'int(10) unsigned', true);
        $this->addForeignKey(
            'case_search_saved_search_institution_fk',
            'case_search_saved_search',
            'institution_id',
            'institution',
            'id'
        );

        // Users will continue to see saved searches they have created regardless of the current institution.
    }

    public function safeDown()
    {
        $this->dropForeignKey('case_search_saved_search_institution_fk', 'case_search_saved_search');
        $this->dropOEColumn('case_search_saved_search', 'institution_id', true);
    }
}
