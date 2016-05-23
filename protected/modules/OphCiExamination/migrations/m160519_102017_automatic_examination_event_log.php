<?php

class m160519_102017_automatic_examination_event_log extends OEMigration {

    public function up() {
        $this->createOETable('automatic_examination_event_log', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'unique_code' => 'varchar(6) NOT NULL',
            'examination_data' => 'blob',
            'examination_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
            'importSuccess' => 'int(1) unsigned NOT NULL default 0',
                ), true);
        $this->createTable('import_status', array(
            'id' => 'pk',
            'status' => 'int(1) unsigned not null',
            'status_value' => 'varchar(30) NOT NULL',
                ));
        $this->insert('import_status', array(
            'status' => '0',
            'status_value' => 'Duplicate/Unfound Event',
        ));
        $this->insert('import_status', array(
            'status' => '1',
            'status_value' => 'Success Event',
        ));
        $this->insert('import_status', array(
            'status' => '2',
            'status_value' => 'Import Failure',
        ));
        $this->addForeignKey('automatic_examination_event_log_event_id_fk', 'automatic_examination_event_log', 'event_id', 'event', 'id');
    }

    public function down() {
        $this->dropOETable('automatic_examination_event_log', true);
        $this->dropTable('import_status');
    }

}
