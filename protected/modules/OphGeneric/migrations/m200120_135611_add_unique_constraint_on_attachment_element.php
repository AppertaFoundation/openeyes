<?php

class m200120_135611_add_unique_constraint_on_attachment_element extends CDbMigration
{
    public function safeUp()
    {
        $this->createIndex(
            'generic_attachment_element_unique_index',
            'et_ophgeneric_attachment',
            'event_id',
            1
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophgeneric_attach_ev_fk', 'et_ophgeneric_attachment');
        $this->dropIndex('generic_attachment_element_unique_index', 'et_ophgeneric_attachment');
        $this->addForeignKey('et_ophgeneric_attach_ev_fk', 'et_ophgeneric_attachment', 'event_id', 'event', 'id');
    }
}
