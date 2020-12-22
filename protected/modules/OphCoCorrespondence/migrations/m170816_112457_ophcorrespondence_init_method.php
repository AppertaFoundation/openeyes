<?php

class m170816_112457_ophcorrespondence_init_method extends CDbMigration
{
    public function up()
    {

        $this->createTable('ophcorrespondence_init_method', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'method' => 'varchar(255) NOT NULL',
            'short_code' => 'varchar(255) NOT NULL',
            'description' => 'varchar(1024) NOT NULL',
            'active' => 'tinyint(1) NOT NULL DEFAULT 1',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addColumn('macro_init_associated_content', 'init_method_id', 'int(10) unsigned NOT NULL AFTER `is_print_appended`');
        $this->createIndex('id_macro_init_associated_content_init_method_id', 'macro_init_associated_content', 'init_method_id');
        $this->addForeignKey('fk_macro_init_associated_content_init_method_id', 'macro_init_associated_content', 'init_method_id', 'ophcorrespondence_init_method', 'id');

        $this->insert('ophcorrespondence_init_method', array(
            'method'=>'getLastExaminationInSs',
            'short_code' =>'LAST_EXAMINATION_IN_SS',
            'description' => 'Last Clinic Examination',
        ));

        $this->insert('ophcorrespondence_init_method', array(
            'method'=>'getLastOpNoteInSs',
            'short_code' =>'LAST_OP_NOTE_IN_SS',
            'description' => 'Last Operation note',
        ));

        $this->insert('ophcorrespondence_init_method', array(
            'method'=>'getLastEventInSs',
            'short_code' =>'LAST_EVENT_IN_SS',
            'description' => 'Last Event',
        ));

        $this->insert('ophcorrespondence_init_method', array(
            'method'=>'getLastInjectionInSs',
            'short_code' =>'LAST_INJECTION_IN_SS',
            'description' => 'Last Injection Event',
        ));

        $this->insert('ophcorrespondence_init_method', array(
            'method'=>'getLastPrescriptionInSs',
            'short_code' =>'LAST_PRESCRIPTION_IN_SS',
            'description' => 'Last Prescription Event',
        ));
    }

    public function down()
    {
        $this->dropForeignKey('fk_macro_init_associated_content_init_method_id', 'macro_init_associated_content');
        $this->dropIndex('id_macro_init_associated_content_init_method_id', 'macro_init_associated_content');
        $this->dropColumn('macro_init_associated_content', 'init_method_id');
        $this->dropTable('ophcorrespondence_init_method');
    }
}
