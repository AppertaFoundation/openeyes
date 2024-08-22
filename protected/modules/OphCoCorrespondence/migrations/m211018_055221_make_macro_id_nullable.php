<?php

class m211018_055221_make_macro_id_nullable extends OEMigration
{
    private const TABLE = "et_ophcocorrespondence_letter";

    public function up()
    {
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('macro_id')) {
            $this->alterOEColumn(
                self::TABLE,
                "macro_id",
                "INT(10) UNSIGNED NULL DEFAULT NULL",
                true
            );
        }
    }

    public function down()
    {
        if ($this->dbConnection->schema->getTable(self::TABLE)->getColumn('macro_id')) {
            $this->alterOEColumn(
                self::TABLE,
                "macro_id",
                "INT(10) UNSIGNED NOT NULL",
                true
            );
        }
    }
}
