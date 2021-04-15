<?php

class m210311_031644_add_institution_id_column_to_reference_data_tables extends OEMigration
{

    public function safeUp()
    {
        $this->addOEColumn('ophcocorrespondence_letter_string_group', 'institution_id', 'int(10) unsigned AFTER display_order', true);
        $this->addForeignKey('letter_string_group_institution_fk', 'ophcocorrespondence_letter_string_group', 'institution_id', 'institution', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('letter_string_group_institution_fk', 'ophcocorrespondence_letter_string_group');
        $this->dropOEColumn('ophcocorrespondence_letter_string_group', 'institution_id', true);
    }
}
