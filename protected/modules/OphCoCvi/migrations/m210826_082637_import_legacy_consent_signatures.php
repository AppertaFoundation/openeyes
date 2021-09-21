<?php

class m210826_082637_import_legacy_consent_signatures extends OEMigration
{
    private const LEGACY_ET_TABLE = "et_ophcocvi_consentsig";
    private const ET_TABLE = "et_ophcocvi_esign";
    private const ITEM_TABLE = "ophcocvi_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET_TABLE)) {
            $this->execute("
                UPDATE ".self::LEGACY_ET_TABLE." oc
                    LEFT JOIN `protected_file` pf ON pf.id = oc.signature_file_id
                SET oc.signature_date = pf.last_modified_date 
                WHERE 
                    UNIX_TIMESTAMP(oc.signature_date) IS NULL 
                    AND oc.signature_file_id IS NOT NULL
                ;
            ");

            $this->execute("INSERT INTO ".self::ET_TABLE."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    event_id, last_modified_user_id, last_modified_date, created_user_id, created_date
                    FROM ".self::LEGACY_ET_TABLE."
                    WHERE deleted = 0 AND signature_file_id IS NOT NULL
                ");
            $this->execute("INSERT INTO ".self::ITEM_TABLE."
                    (element_id, type, signature_file_id, signatory_role, signatory_name, `timestamp`,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, 3, le.signature_file_id, IF(le.is_patient = 1, 'Patient', \"Patient's representative\"),
                    IF(le.is_patient = 1, CONCAT(contact.first_name, ' ', contact.last_name), le.representative_name),
                    UNIX_TIMESTAMP(le.signature_date),
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS le
                    LEFT JOIN ".self::ET_TABLE." AS e ON e.event_id = le.event_id
                    LEFT JOIN event ON event.id = le.event_id
                    LEFT JOIN episode ON episode.id = event.episode_id
                    LEFT JOIN patient ON patient.id = episode.patient_id
                    LEFT JOIN contact ON contact.id = patient.contact_id
                    WHERE le.deleted = 0 AND le.signature_file_id IS NOT NULL;"
            );
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::ITEM_TABLE." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::ET_TABLE." WHERE 1 = 1");
    }
}
