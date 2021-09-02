<?php

class m210819_122506_import_legacy_signatures extends OEMigration
{
    private const LEGACY_ET = "et_consultant_signature";
    private const LEGACY_2ND_ET = "et_secondary_signature";
    private const LEGACY_SECRETARY_ET = "et_ophcocorrespondence_secretary_signature";
    private const NEW_ET = "et_ophcocorrespondence_esign";
    private const NEW_ITEM = "ophcocorrespondence_signature";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET)) {
            $evt_type_id = $this->dbConnection
                            ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphCoCorrespondence';")
                            ->queryScalar();
            // Copy elements
            $this->execute("
                INSERT INTO ".self::NEW_ET." (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT b.event_id, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_ET." as b
                LEFT JOIN `event` ON `event`.id = b.event_id
                WHERE `event`.event_type_id = ".$evt_type_id."
                AND b.protected_file_id IS NOT NULL");
            // Copy primary consultant signatures
            $this->execute("
                INSERT INTO ".self::NEW_ITEM." (element_id, `type`, signature_file_id, signed_user_id, signatory_role,
                    signatory_name, `timestamp`, secretary, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT
                    c.id, 1, b.protected_file_id, b.signed_by_user_id, 'Consultant', CONCAT(`user`.first_name, ' ', `user`.last_name),
                    UNIX_TIMESTAMP(b.signature_date),
                    0, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_ET." AS b
                LEFT JOIN ".self::NEW_ET." AS c ON c.event_id = b.event_id
                LEFT JOIN `event` ON `event`.id = b.event_id
                LEFT JOIN `user` ON `user`.id = b.signed_by_user_id
                WHERE `event`.event_type_id = ".$evt_type_id."
                AND b.protected_file_id IS NOT NULL"
            );
            // Copy secondary consultant elements (if no esign element exists for the event already)
            $this->execute("
                INSERT INTO ".self::NEW_ET." (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT b.event_id, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_2ND_ET." as b
                LEFT JOIN `event` ON `event`.id = b.event_id 
                WHERE `event`.event_type_id = ".$evt_type_id."
                AND b.protected_file_id IS NOT NULL
                AND b.event_id NOT IN (SELECT event_id FROM ".self::NEW_ET.")");
            // Copy secondary consultant signatures
            $this->execute("
                INSERT INTO ".self::NEW_ITEM." (element_id, `type`, signature_file_id, signed_user_id, signatory_role,
                    signatory_name, `timestamp`, secretary, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT
                    c.id, 1, b.protected_file_id, b.signed_by_user_id, 'Secondary Consultant', CONCAT(`user`.first_name, ' ', `user`.last_name),
                    UNIX_TIMESTAMP(b.signature_date),
                    0, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_2ND_ET." AS b
                LEFT JOIN ".self::NEW_ET." AS c ON c.event_id = b.event_id
                LEFT JOIN `event` ON `event`.id = b.event_id
                LEFT JOIN `user` ON `user`.id = b.signed_by_user_id
                WHERE `event`.event_type_id = ".$evt_type_id."
                AND b.protected_file_id IS NOT NULL"
            );
            // Copy secretary elements (if no esign element exists for the event already)
            $this->execute("
                INSERT INTO ".self::NEW_ET." (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT b.event_id, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_SECRETARY_ET." as b 
                WHERE b.is_signed = 1
                AND b.event_id NOT IN (SELECT event_id FROM ".self::NEW_ET.")");
            // Copy secretary signatures
            $this->execute("
                INSERT INTO ".self::NEW_ITEM." (element_id, `type`, signature_file_id, signed_user_id, signatory_role,
                    signatory_name, `timestamp`, secretary, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT
                    c.id, 1, NULL, b.signed_by_user_id, 'Consultant',  CONCAT(`user`.first_name, ' ', `user`.last_name), UNIX_TIMESTAMP(b.signature_date),
                    1, b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM ".self::LEGACY_SECRETARY_ET." AS b
                LEFT JOIN ".self::NEW_ET." AS c ON c.event_id = b.event_id
                LEFT JOIN `event` ON `event`.id = b.event_id
                LEFT JOIN `user` ON `user`.id = b.signed_by_user_id
                WHERE b.is_signed = 1"
            );
        }
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM ".self::NEW_ITEM." WHERE 1 = 1");
        $this->execute("DELETE FROM ".self::NEW_ET." WHERE 1 = 1");
    }
}
