<?php

class m210705_235745_add_external_portal_reference_fields_to_letter_element extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            'et_ophcocorrespondence_letter',
            'supersession_id',
            'varchar(255)',
            true
        );
        $this->addOEColumn(
            'ophcocorrespondence_letter_type',
            'export_label',
            'varchar(100)',
            true
        );
    }

    public function down()
    {
        $this->dropOEColumn('et_ophcocorrespondence_letter', 'supersession_id', true);
        $this->dropOEColumn('ophcocorrespondence_letter_type', 'export_label', true);
    }
}
