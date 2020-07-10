<?php

class m200307_070914_modify_enum_output_status_column_document_output_table extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('document_output', 'output_status', "enum('DRAFT','SENDING','PENDING','PENDING_RETRY','FAILED','COMPLETE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DRAFT'", true);
    }

    public function down()
    {
        $this->alterOEColumn('document_output', 'output_status', "enum('DRAFT','PENDING','PENDING_RETRY','FAILED','COMPLETE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DRAFT'", true);
    }
}
