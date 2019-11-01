<?php

class m190403_095532_insert_opt_to_correspondence_letter_recipient extends CDbMigration
{
    public function up()
    {
        $this->insert('ophcocorrespondence_letter_recipient', array('id' => 3, 'name' => 'Optometrist', 'display_order' => 3));
        $this->addColumn('ophcocorrespondence_letter_macro', 'cc_optometrist', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('ophcocorrespondence_letter_macro_version', 'cc_optometrist', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->delete('ophcocorrespondence_letter_recipient', 'name="Optometrist"');
        $this->dropColumn('ophcocorrespondence_letter_macro', 'cc_optometrist');
        $this->dropColumn('ophcocorrespondence_letter_macro_version', 'cc_optometrist');
    }
}