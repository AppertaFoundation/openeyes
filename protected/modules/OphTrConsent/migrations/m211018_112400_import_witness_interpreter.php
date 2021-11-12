<?php

class m211018_112400_import_witness_interpreter extends OEMigration
{
    private const OTHER_ELEMENT_TBL = 'et_ophtrconsent_other';
    private const ELEMENT_TBL = 'et_ophtrconsent_additional_signatures';

    private const ELEMENT_INTERPRETER_TBL = 'et_ophtrconsent_interpreter_signature';
    private const ELEMENT_WITNESS_TBL = 'et_ophtrconsent_witness_signature';

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::OTHER_ELEMENT_TBL, true)) {
            if (
                (!$this->dbConnection->schema->getTable(self::ELEMENT_INTERPRETER_TBL, true)) &&
                (!$this->dbConnection->schema->getTable(self::ELEMENT_WITNESS_TBL, true))
            ) {
                $this->execute("
                    INSERT INTO " . self::ELEMENT_TBL . "
                    (event_id, witness_required, witness_name, interpreter_required, interpreter_name, guardian_name, created_date, created_user_id, last_modified_date, last_modified_user_id)
                    SELECT event_id, witness_required, witness_name, interpreter_required, interpreter_name, parent_guardian, created_date, created_user_id, last_modified_date, last_modified_user_id
                    FROM " . self::OTHER_ELEMENT_TBL . " as ot
                ");
            } else {
                $this->execute("
                    INSERT INTO " . self::ELEMENT_TBL . "
                    (event_id, witness_required, witness_name, interpreter_required, interpreter_name, guardian_name, created_date, created_user_id, last_modified_date, last_modified_user_id)
                    SELECT event_id, witness_required, witness_name, interpreter_required, interpreter_name, parent_guardian, created_date, created_user_id, last_modified_date, last_modified_user_id
                    FROM " . self::OTHER_ELEMENT_TBL . " as ot
                    WHERE
                        NOT EXISTS (SELECT * FROM " . self::ELEMENT_INTERPRETER_TBL . "
                        WHERE event_id = ot.event_id LIMIT 1)
                    AND
                        NOT EXISTS (SELECT * FROM " . self::ELEMENT_WITNESS_TBL . "
                        WHERE event_id = ot.event_id LIMIT 1)
                    ;
                ");
            }

            $this->execute("
                UPDATE " . self::ELEMENT_TBL . " SET guardian_required = 1 WHERE trim(coalesce(guardian_name, '')) <>''
            ");
        }
    }

    public function safeDown()
    {
        echo "m211018_112400_import_witness_interpreter does not support migration down.\n";
        return false;
    }
}
