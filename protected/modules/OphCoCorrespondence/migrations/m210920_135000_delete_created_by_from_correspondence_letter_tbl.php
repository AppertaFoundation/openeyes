<?php

class m210920_135000_delete_created_by_from_correspondence_letter_tbl extends OEMigration
{
    private const TABLE = "et_ophcocorrespondence_letter";
    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('created_by')) {
            $this->dropOEColumn(self::TABLE, 'created_by', true);
        }
    }

    public function safeDown()
    {
        if (!is_null($this->dbConnection->schema->getTable(self::TABLE)->getColumn('created_by'))) {
            $this->addOEColumn(self::TABLE, "created_by", "INT(10) unsigned NOT NULL", true);
        }
    }
}
