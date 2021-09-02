<?php

class m210830_111957_import_legacy_patient_signatures extends OEMigration
{
    private const LEGACY_ET_TABLE =  "et_ophcocvi_patient_signature";
    private const ET_TABLE = "et_ophcocvi_esign";
    private const ITEM_TABLE = "ophcocvi_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET_TABLE)) {
            $this->execute("INSERT INTO ".self::ET_TABLE."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    event_id, last_modified_user_id, last_modified_date, created_user_id, created_date
                    FROM ".self::LEGACY_ET_TABLE."
                    WHERE protected_file_id IS NOT NULL
                    AND event_id NOT IN (SELECT x.event_id FROM ".self::LEGACY_ET_TABLE." AS x)
                ");
            $this->execute("INSERT INTO ".self::ITEM_TABLE."
                    (element_id, type, signature_file_id, signatory_role, signatory_name, `timestamp`,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, 3, le.protected_file_id,
                    CASE 
                        WHEN le.signatory_person = 1 THEN 'Patient'
                        WHEN le.signatory_person = 2 THEN 'Patient\'s representative'
                        WHEN le.signatory_person = 5 THEN 'Parent/Guardian'
                    END,
                    le.signatory_name,
                    UNIX_TIMESTAMP(le.signature_date),
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS le
                    LEFT JOIN ".self::ET_TABLE." AS e ON e.event_id = le.event_id
                    WHERE le.protected_file_id IS NOT NULL;"
            );
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::ITEM_TABLE." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::ET_TABLE." WHERE 1 = 1");
    }
}
