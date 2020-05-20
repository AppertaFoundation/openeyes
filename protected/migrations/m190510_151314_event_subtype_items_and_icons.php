<?php

class m190510_151314_event_subtype_items_and_icons extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('event_subtype_item', [
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'event_subtype' => 'VARCHAR(100) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL'
        ], true);

        $this->createOETable('event_subtype', [
            'event_subtype' => 'VARCHAR(100) NOT NULL',
            'dicom_modality_code' => 'VARCHAR(45) NOT NULL',
            'icon_name' => 'VARCHAR(45) NOT NULL',
            'display_name' => 'VARCHAR(100) NOT NULL',
            'CONSTRAINT PK_event_subtype PRIMARY KEY (event_subtype)'
        ], true);

        $this->createIndex('event_uk_event_subtype', 'event_subtype_item', ['event_id', 'event_subtype'], true);
        $this->addForeignKey('fk_event_subtype_item_event_id', 'event_subtype_item', 'event_id', 'event', 'id');
        $this->addForeignKey('fk_event_subtype_item_event_subtype_id', 'event_subtype_item', 'event_subtype', 'event_subtype', 'event_subtype');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_event_subtype_item_event_subtype_id', 'event_subtype_item');
        $this->dropForeignKey('fk_event_subtype_item_event_id', 'event_subtype_item');
        $this->dropIndex('event_uk_event_subtype', 'event_subtype_item');

        $this->dropOETable('event_subtype', true);
        $this->dropOETable('event_subtype_item', true);
    }
}
