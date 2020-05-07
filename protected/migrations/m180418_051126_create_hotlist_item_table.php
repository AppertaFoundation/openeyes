<?php

class m180418_051126_create_hotlist_item_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('user_hotlist_item', array(
            'id' => 'pk',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'is_open' => 'boolean NOT NULL DEFAULT true',
            'user_comment' => 'text',
        ), true);

        $this->addForeignKey(
            'user_hotlist_item_user_fk',
            'user_hotlist_item',
            'patient_id',
            'patient',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('user_hotlist_item', true);
    }
}
