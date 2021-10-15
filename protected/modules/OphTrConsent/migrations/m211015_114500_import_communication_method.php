<?php
class m211015_114500_import_communication_method extends OEMigration
{
    private const ELEMENT_TBL = 'et_ophtrconsent_specialreq';
    private const OTHER_ELEMENT_TBL = 'et_ophtrconsent_other';
    private const SPECIAL_REQ = 'special_requirement';

    public function safeUp()
    {
        if (
            ($this->dbConnection->schema->getTable(self::OTHER_ELEMENT_TBL, true)) &&
            ($this->dbConnection->schema->getTable(self::ELEMENT_TBL, true)) &&
            ($this->dbConnection->schema->getTable(self::SPECIAL_REQ, true))
        ) {
            $this->execute("
                INSERT INTO " . self::ELEMENT_TBL . "
                (event_id, specialreq, created_date, created_user_id, last_modified_date, last_modified_user_id)
                SELECT event_id,
					CASE
						WHEN `special_requirement_other` IS NOT NULL THEN `special_requirement_other`
						ELSE (
							SELECT name FROM " . self::SPECIAL_REQ . " WHERE id = et.special_requirement_id
						)
						END as specialreq,
					created_date, created_user_id, last_modified_date, last_modified_user_id
				FROM  " . self::OTHER_ELEMENT_TBL . " as et;
            ");
        }
    }

    public function safeDown()
    {
        echo "m211007_141253_migration_communication_method does not support migration down.\n";
        return false;
    }
}
