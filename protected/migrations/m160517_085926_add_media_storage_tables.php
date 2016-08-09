<?php

class m160517_085926_add_media_storage_tables extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'media_data',
            array(
                'id' => 'pk',
                'media_name' => 'varchar(255) not null',
                'original_file_path' => 'varchar(500)',
                'original_file_name' => 'varchar(255)',
                'original_file_size' => 'int(16)',
                'original_file_date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'media_type_id' => 'int(11)',
                'event_date' => 'datetime',
                'patient_id' => 'int(10) unsigned',
                'event_type_id' => 'int(10) unsigned',
                'parent_id' => 'int(11)',
                'eye_id' => 'int(10) unsigned',
                'mean_deviation' => 'float',
                'plot_values' => 'text',
            ),
            true
        );

        $this->createOETable(
            'media_type',
            array(
                'id' => 'pk',
                'type_name' => 'varchar(50) not null',
                'type_class' => 'varchar(500)',
                'type_method' => 'varchar(255)',
                'type_html_tag' => 'varchar(50)',
                'type_mime' => 'varchar(20)',
            ),
            true
        );

        $this->addForeignKey('media_type_id_fk', 'media_data', 'media_type_id', 'media_type', 'id');
        $this->addForeignKey('patient_id_fk', 'media_data', 'patient_id', 'patient', 'id');
        $this->addForeignKey('event_type_id_fk', 'media_data', 'event_type_id', 'event_type', 'id');

        $this->insert('media_type', array('type_name' => 'vfgreyscale', 'type_html_tag' => 'img', 'type_mime' => 'image/jpeg'));
    }

    public function down()
    {
        $this->dropForeignKey('media_type_id_fk', 'media_data');
        $this->dropForeignKey('patient_id_fk', 'media_data');
        $this->dropForeignKey('event_type_id_fk', 'media_data');

        $this->dropTable('media_type');
        $this->dropTable('media_data');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
