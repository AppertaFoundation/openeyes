<?php

class m211028_132907_fix_version_table extends OEMigration
{
    private const TABLE = "et_ophcocorrespondence_letter_version";

    public function up()
    {
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('signatory_id')) {
            $this->dropColumn(self::TABLE, 'signatory_id');
        }
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('created_by')) {
            $this->dropColumn(self::TABLE, 'created_by');
        }
    }

    public function down()
    {
        if (!is_null($this->dbConnection->schema->getTable(self::TABLE)->getColumn('created_by'))) {
            $this->addColumn(self::TABLE, "created_by", "INT(10) unsigned NOT NULL");
        }
        if (!is_null($this->dbConnection->schema->getTable(self::TABLE)->getColumn('signatory_id'))) {
            $this->addColumn(self::TABLE, "signatory_id", "INT(10) unsigned NOT NULL");
        }
    }
}
