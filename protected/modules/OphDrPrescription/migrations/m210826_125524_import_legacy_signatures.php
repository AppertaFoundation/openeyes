<?php

class m210826_125524_import_legacy_signatures extends OEMigration
{
    private const NEW_ET = "et_ophdrprescription_esign";
    private const ITEM_TBL = "ophdrprescription_signature";
    private const LEGACY_ET = "et_consultant_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET)) {
            $evt_type_id = $this->dbConnection
                ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphDrPrescription';")
                ->queryScalar();
            // Copy elements
            $this->execute("
                INSERT INTO ".self::NEW_ET." (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT b.event_id, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_ET." as b
                LEFT JOIN `event` ON `event`.id = b.event_id
                WHERE `event`.event_type_id = ".$evt_type_id."
                AND b.protected_file_id IS NOT NULL");
            // Copy signatures
            $this->execute("
                INSERT INTO ".self::ITEM_TBL." (element_id, `type`, signature_file_id, signed_user_id, signatory_role,
                    signatory_name, `timestamp`, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT
                    c.id, 1, b.protected_file_id, b.signed_by_user_id, 'Consultant', CONCAT(`user`.first_name, ' ', `user`.last_name),
                    UNIX_TIMESTAMP(b.signature_date),
                    b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_ET." AS b
                LEFT JOIN ".self::NEW_ET." AS c ON c.event_id = b.event_id
                LEFT JOIN `event` ON `event`.id = b.event_id
                LEFT JOIN `user` ON `user`.id = b.signed_by_user_id
                WHERE `event`.event_type_id = ".$evt_type_id."
                AND b.protected_file_id IS NOT NULL");
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::ITEM_TBL." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::NEW_ET." WHERE 1 = 1");
    }
}
