<?php

class m210920_135000_delete_created_by_from_correspondence_letter_tbl extends OEMigration
{
    private const TABLE = "et_ophcocorrespondence_letter";
    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('created_by')) {
            $this->dropColumn(self::TABLE, 'created_by');
        }
    }

    public function safeDown()
    {
        if (!is_null($this->dbConnection->schema->getTable(self::TABLE)->getColumn('created_by'))) {
            $this->addColumn(self::TABLE, "created_by", "INT(10) unsigned NOT NULL");
        }
    }
}
