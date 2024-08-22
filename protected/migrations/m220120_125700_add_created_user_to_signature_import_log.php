<?php

class m220120_125700_add_created_user_to_signature_import_log extends OEMigration
{
    public function up()
    {
        if (!$this->dbConnection->schema->getTable('signature_import_log')->getColumn('created_user_id')) {
            $this->addColumn('signature_import_log', 'created_user_id', 'int(10) unsigned DEFAULT 1');
        }
        if (!$this->dbConnection->schema->getTable('signature_import_log')->getColumn('last_modified_user_id')) {
            $this->addColumn('signature_import_log', 'last_modified_user_id', 'int(10) unsigned DEFAULT 1');
        }

        if (!$this->dbConnection->schema->getTable('signature_import_log')->getColumn('created_date')) {
            $this->addColumn('signature_import_log', 'created_date', "date not null default '1900-01-01 00:00:00'");
        }

        if (!$this->dbConnection->schema->getTable('signature_import_log')->getColumn('last_modified_date')) {
            $this->addColumn('signature_import_log', 'last_modified_date', "date not null default '1900-01-01 00:00:00'");
        }
    }

    public function down()
    {
        $this->dropColumn('signature_import_log', 'created_user_id');
        $this->dropColumn('signature_import_log', 'last_modified_user_id');
        $this->dropColumn('signature_import_log', 'created_date');
        $this->dropColumn('signature_import_log', 'last_modified_date');
    }
}
