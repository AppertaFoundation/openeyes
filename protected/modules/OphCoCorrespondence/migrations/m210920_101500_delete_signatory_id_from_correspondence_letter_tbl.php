<?php

class m210920_101500_delete_signatory_id_from_correspondence_letter_tbl extends OEMigration
{
    private const TABLE = "et_ophcocorrespondence_letter";
    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('signatory_id')) {
            $this->dropOEColumn(self::TABLE, 'signatory_id', true);
        }
    }

    public function safeDown()
    {
        if (!is_null($this->dbConnection->schema->getTable(self::TABLE)->getColumn('signatory_id'))) {
            $this->addOEColumn(self::TABLE, "signatory_id", "INT(10) unsigned NOT NULL", true);
        }
    }
}
