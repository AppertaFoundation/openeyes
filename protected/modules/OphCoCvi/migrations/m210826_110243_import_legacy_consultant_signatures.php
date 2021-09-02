<?php

class m210826_110243_import_legacy_consultant_signatures extends OEMigration
{
    private const LEGACY_ET_TABLE = "et_consultant_signature";
    private const LEGACY_ET_PATIENT_SIGNATURE =  "et_ophcocvi_consentsig";
    private const ET_TABLE = "et_ophcocvi_esign";
    private const ITEM_TABLE = "ophcocvi_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET_TABLE)) {
            $evt_type_id = $this->dbConnection
                ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphCoCvi';")
                ->queryScalar();
            $this->execute("INSERT INTO ".self::ET_TABLE."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    a.event_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS a
                    LEFT JOIN event ON event.id = a.event_id
                    WHERE event.event_type_id = $evt_type_id
                    AND a.protected_file_id IS NOT NULL
                ");
            $this->execute("INSERT INTO ".self::ITEM_TABLE."
                    (element_id, type, signature_file_id, signatory_role, signatory_name, `timestamp`,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, 1, le.protected_file_id, 'Consultant',
                    CONCAT(user.first_name, ' ', user.last_name),
                    UNIX_TIMESTAMP(le.signature_date),
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_ET_TABLE." AS le
                    LEFT JOIN ".self::ET_TABLE." AS e ON e.event_id = le.event_id
                    LEFT JOIN `event` ON `event`.id = le.event_id
                    LEFT JOIN `user` ON `user`.id = le.signed_by_user_id
                    WHERE event.event_type_id = $evt_type_id
                    AND le.protected_file_id IS NOT NULL;"
            );
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::ITEM_TABLE." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::ET_TABLE." WHERE 1 = 1");
    }
}
