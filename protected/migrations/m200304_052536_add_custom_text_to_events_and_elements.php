<?php

class m200304_052536_add_custom_text_to_events_and_elements extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('event_type', 'custom_hint_text', 'text', true);
        $this->addOEColumn('event_type', 'hint_position', 'varchar(10) DEFAULT \'BOTTOM\'', true);
        $this->addOEColumn('element_type', 'custom_hint_text', 'text', true);
        //if the columns already exist, do nothing
        if (!isset($this->dbConnection->schema->getTable('element_type_version')->columns['group_title'])) {
            $this->addColumn('element_type_version', 'group_title', 'varchar(255) AFTER `tile_size`');
        }
    }

    public function down()
    {
        $this->dropOEColumn('event_type', 'custom_hint_text', true);
        $this->dropOEColumn('event_type', 'hint_position', true);
        $this->dropOEColumn('element_type', 'custom_hint_text', true);
        $this->dropColumn('element_type_version', 'group_title');
    }
}
