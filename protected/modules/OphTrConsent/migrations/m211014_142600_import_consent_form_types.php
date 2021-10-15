<?php
class m211014_142600_import_consent_form_types extends OEMigration
{
    private const ARCHIVE_TABLE = 'et_ophtrconsent_type_archive';

    public function safeUp()
    {
        if (!$this->dbConnection->schema->getTable(self::ARCHIVE_TABLE, true)) {
            $this->execute("CREATE TABLE et_ophtrconsent_type_archive AS SELECT * FROM et_ophtrconsent_type");
        }

        $this->execute("
			UPDATE et_ophtrconsent_type
			SET type_id = CASE
				WHEN type_id = 5 THEN 1
				WHEN type_id = 6 THEN 2
				WHEN type_id = 7 THEN 1
				WHEN type_id = 8 THEN 4
				WHEN type_id = 9 THEN 3
				WHEN type_id = 10 THEN 3
				ELSE type_id
			END
			WHERE type_id  in (5,6,7,8,9,10)
		");
    }

    public function safeDown()
    {
        echo "m211012_125343_migration_consent_form_types does not support migration down.\n";
        return false;
    }
}
