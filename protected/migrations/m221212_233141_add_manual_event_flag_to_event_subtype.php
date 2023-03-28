<?php

class m221212_233141_add_manual_event_flag_to_event_subtype extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            'event_subtype',
            'manual_entry',
            'tinyint(1) unsigned NOT NULL DEFAULT 0',
            true
        );

        $this->createOETable(
            'event_subtype_element_entries',
            [
                'id' => 'pk',
                'event_subtype' => 'VARCHAR(100) NOT NULL',
                'element_type_id' => 'INT(10) UNSIGNED NOT NULL',
                'display_order' => 'INT(10)'
            ],
            true
        );

        $this->addForeignKey('event_subtype_element_entries_es_fk', 'event_subtype_element_entries', 'event_subtype', 'event_subtype', 'event_subtype');
        $this->addForeignKey('event_subtype_element_entries_et_fk', 'event_subtype_element_entries', 'element_type_id', 'element_type', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('event_subtype_element_entries_es_fk', 'event_subtype_element_entries');
        $this->dropForeignKey('event_subtype_element_entries_et_fk', 'event_subtype_element_entries');

        $this->dropOETable('event_subtype_element_entries', true);

        $this->dropOEColumn(
            'event_subtype',
            'manual_entry',
            true
        );
    }
}
