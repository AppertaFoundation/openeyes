<?php

class m210526_082900_create_table_signature_import_log extends OEMigration
{
    public function up()
    {
        if ($this->dbConnection->schema->getTable('signature_import_log', true) === null) {
            $this->createOETable('signature_import_log', [
                'id' => 'pk',
                'filename' => 'varchar(255) NOT NULL',
                'import_datetime' => 'DATETIME NULL DEFAULT NULL',
                'status_id' => 'INT(1) UNSIGNED NOT NULL',
                'return_message' => 'TEXT',
                'event_id' => 'INT(10) unsigned default NULL',
            ]);
            $this->addForeignKey('signature_import_log_event_id_pk', 'signature_import_log', 'event_id', 'event', 'id');
        }
    }

    public function down()
    {
        $this->dropForeignKey('signature_import_log_event_id_pk', 'signature_import_log');
        $this->dropOETable('signature_import_log');
    }
}
