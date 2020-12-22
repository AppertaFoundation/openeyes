<?php

class m191128_121205_add_document_id_and_attachment_data_id_to_event_image extends OEMigration
{
    public function safeUp()
    {

        $this->createOETable(
            'tmp_event_image',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned',
                'page' => 'int(10) unsigned',
                'image_data' => 'mediumblob',
                'status_id' => 'int(11) NOT NULL',
                'message' => 'text',
                'document_number' => 'int(10) NULL',
                'attachment_data_id' => 'int(11) NULL'
            )
        );

        $this->addForeignKey('event_image_to_event_id_fk', 'tmp_event_image', 'event_id', 'event', 'id');
        $this->addForeignKey('event_image_to_eye_id_fk', 'tmp_event_image', 'eye_id', 'eye', 'id');
        $this->addForeignKey('event_image_to_status_id_fk', 'tmp_event_image', 'status_id', 'event_image_status', 'id');

        $this->addForeignKey('fk_event_image_to_attachment_data', 'tmp_event_image', 'attachment_data_id', 'attachment_data', 'id');
        $this->execute('INSERT INTO tmp_event_image(id,event_id,eye_id,page,image_data,status_id,message,
                        document_number , attachment_data_id, last_modified_user_id, last_modified_date, created_user_id, created_date) 
                            SELECT event_image.id,
                                   event_image.event_id, 
                                   event_image.eye_id, 
                                   event_image.page, 
                                   event_image.image_data, 
                                   event_image.status_id, 
                                   event_image.message, 
                                   null,
                                   null,
                                   event_image.last_modified_user_id,
                                   event_image.last_modified_date,
                                   event_image.created_user_id,
                                   event_image.created_date
                                   FROM event_image');

        $this->dropTable('event_image');
        $this->renameTable('tmp_event_image', 'event_image');
    }

    public function safeDown()
    {
        $this->dropColumn('event_image', 'document_number');
        $this->dropColumn('event_image', 'attachment_data_id');
        $this->dropForeignKey('fk_event_image_attachment_data', 'event_image');
    }
}
