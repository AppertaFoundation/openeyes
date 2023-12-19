<?php

class m230813_153011_add_document_unique_identifier extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('et_ophcodocument_document', 'unique_ref', 'varchar(255) NULL', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('et_ophcodocument_document', 'unique_ref', true);
    }
}
