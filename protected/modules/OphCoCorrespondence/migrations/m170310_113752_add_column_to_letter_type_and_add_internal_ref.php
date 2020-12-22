<?php

class m170310_113752_add_column_to_letter_type_and_add_internal_ref extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophcocorrespondence_letter_type', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER name');
        $this->versionExistingTable('ophcocorrespondence_letter_type');
        $this->insert('ophcocorrespondence_letter_type', array('name' => 'Internal Referral'));
    }

    public function down()
    {
        $this->dropColumn('ophcocorrespondence_letter_type', 'is_active');
        $this->dropTable('ophcocorrespondence_letter_type_version');
        $this->delete('ophcocorrespondence_letter_type', 'name = :name', array('name' => 'Internal Referral'));
    }
}
