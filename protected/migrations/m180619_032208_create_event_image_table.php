<?php

class m180619_032208_create_event_image_table extends OEMigration
{

    public function safeUp()
    {
        $this->createOETable(
            'event_image',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned',
                'page' => 'int(10) unsigned',
                'image_data' => 'mediumblob',
                'status_id' => 'int(11) NOT NULL',
                'message' => 'text',
            )
        );

        $this->createOETable(
            'event_image_status',
            array(
                'id' => 'pk',
                'name' => 'varchar(50)',
            )
        );

        $this->addForeignKey('event_image_event_id_fk', 'event_image', 'event_id', 'event', 'id');
        $this->addForeignKey('event_image_eye_id_fk', 'event_image', 'eye_id', 'eye', 'id');
        $this->addForeignKey('event_image_status_id_fk', 'event_image', 'status_id', 'event_image_status', 'id');

        $this->insert('event_image_status', array('name' => 'NOT_CREATED'));
        $this->insert('event_image_status', array('name' => 'CREATED'));
        $this->insert('event_image_status', array('name' => 'FAILED'));
        $this->insert('event_image_status', array('name' => 'GENERATING'));
    }

    public function safeDown()
    {
        $this->dropTable('event_image');
        $this->dropTable('event_image_status');
    }
}
