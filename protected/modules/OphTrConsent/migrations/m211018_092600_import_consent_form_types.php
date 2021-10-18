<?php

class m211018_092600_import_consent_form_types extends OEMigration
{
    private const ARCHIVE_TYPES_TABLE = 'ophtrconsent_type_type_archive';
    private const ARCHIVE_EXTRA_PROCEDURE_ASSESSMENT = 'ophtrconsent_extra_procedure_procedure_type_assignment_archive';

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable('ophtrconsent_extra_procedure_procedure_type_assignment', true)) {
            if (!$this->dbConnection->schema->getTable(self::ARCHIVE_EXTRA_PROCEDURE_ASSESSMENT, true)) {
                $this->execute("CREATE TABLE " . self::ARCHIVE_EXTRA_PROCEDURE_ASSESSMENT . " AS SELECT * FROM ophtrconsent_extra_procedure_procedure_type_assignment");
            }

            $this->execute("
                UPDATE ophtrconsent_extra_procedure_procedure_type_assignment
                SET ophtrconsent_type_type_id = CASE
                    WHEN ophtrconsent_type_type_id = 5 THEN 1
                    WHEN ophtrconsent_type_type_id = 6 THEN 2
                    WHEN ophtrconsent_type_type_id = 7 THEN 1
                    WHEN ophtrconsent_type_type_id = 8 THEN 4
                    WHEN ophtrconsent_type_type_id = 9 THEN 3
                    WHEN ophtrconsent_type_type_id = 10 THEN 3
                    ELSE ophtrconsent_type_type_id
                END
                WHERE ophtrconsent_type_type_id  in (5,6,7,8,9,10)
		    ");
        }

        if (!$this->dbConnection->schema->getTable(self::ARCHIVE_TYPES_TABLE, true)) {
            $this->execute("CREATE TABLE " . self::ARCHIVE_TYPES_TABLE . " AS SELECT * FROM ophtrconsent_type_type");
        }
        $this->execute("DELETE FROM ophtrconsent_type_type WHERE ID > 4;");

        $this->execute("DELETE FROM ophtrconsent_type_assessment WHERE type_id > 4;");
    }

    public function safeDown()
    {
        echo "m211018_092600_import_consent_form_types does not support migration down.\n";
        return false;
    }
}
