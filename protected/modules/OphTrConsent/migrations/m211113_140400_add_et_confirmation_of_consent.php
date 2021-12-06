<?php

class m211113_140400_add_et_confirmation_of_consent extends OEMigration
{
    const LEGACY_ET = "et_ophtrconsent_confirmation_consultant_signature";

    const NEW_ITEM = "ophtrconsent_signature";
    const NEW_2ND_ET = "et_ophtrconsent_esign";
    const NEW_ET = "et_ophtrconsent_confirmation";

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable('et_ophtrconsent_confirmation', true) === NULL) {
            $event_type_id = $this->getIdOfEventTypeByClassName('OphTrConsent');

            $this->createOETable("et_ophtrconsent_confirmation", [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'confirmed' => 'BOOLEAN NOT NULL DEFAULT 0',
                'signature_id' => 'INT(11) NULL'
            ], true);

            $this->addForeignKey("fk_et_ophtrc_confirm_event", "et_ophtrconsent_confirmation", "event_id", "event", "id");
            $this->addForeignKey('fk_et_ophtrc_confirm_signature', "et_ophtrconsent_confirmation", 'signature_id', 'ophtrconsent_signature', 'id');

            $this->insertOEElementType(array('Element_OphTrConsent_Confirm' => array(
                'name' => 'Confirm consent',
                'required' => 0,
                'default' => 0,
                'display_order' => 0,
            )), $event_type_id);
        } else {
            $event_type_id = $this->getIdOfEventTypeByClassName('OphTrConsent');
            $this->insertOEElementType(array('Element_OphTrConsent_Confirm' => array(
                'name' => 'Confirm consent',
                'required' => 0,
                'default' => 0,
                'display_order' => 0,
            )), $event_type_id);

            //MIGRATE DATA
            $this->addOEColumn('et_ophtrconsent_confirmation', 'signature_id', 'INT(11) NULL', true);
            $this->upgradeHealthSignatures();
            $this->addForeignKey('fk_et_ophtrc_confirm_signature', "et_ophtrconsent_confirmation", 'signature_id', 'ophtrconsent_signature', 'id');
        }
    }

    public function safeDown()
    {
        $this->deleteElementType('OphTrConsent', 'Element_OphTrConsent_Confirm');
        $this->dropForeignKey('fk_et_ophtrc_confirm_signature', 'et_ophtrconsent_confirmation');
        $this->dropForeignKey('fk_et_ophtrc_confirm_event', 'et_ophtrconsent_confirmation');
        $this->dropOETable('et_ophtrconsent_confirmation', true);
    }

    public function upgradeHealthSignatures()
    {
        $evt_type_id = $this->dbConnection
            ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphTrConsent';")
            ->queryScalar();

        $element_type_id = $this->dbConnection
        ->createCommand("SELECT `id` FROM `element_type` WHERE `class_name` = 'Element_OphTrConsent_Confirm';")
        ->queryScalar();

        if (
            $this->dbConnection->schema->getTable(self::LEGACY_ET) && $this->dbConnection->schema->getTable(self::NEW_2ND_ET) &&
            $this->dbConnection->schema->getTable(self::NEW_ITEM)
        ) {
            $this->execute("INSERT INTO ".self::NEW_2ND_ET."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    a.event_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                    FROM ".self::LEGACY_ET." AS a
                    LEFT JOIN event ON event.id = a.event_id
                    WHERE event.event_type_id = ".$evt_type_id."
                    AND a.event_id NOT IN (SELECT x.event_id FROM ".self::NEW_2ND_ET." AS x)
                ");

            $this->execute("INSERT INTO ".self::NEW_ITEM."
                    (element_id, type, signature_file_id, signed_user_id, signatory_role, signatory_name, `timestamp`, 
                    initiator_element_type_id, initiator_row_id,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, " . \BaseSignature::TYPE_OTHER_USER . ", le.protected_file_id, user.id, 'Confirmed by',
                    CONCAT(user.first_name, ' ', user.last_name),
                    UNIX_TIMESTAMP(le.signature_date),
                    ".$element_type_id.",
                    6,
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_ET." AS le
                    LEFT JOIN ".self::NEW_2ND_ET." AS e ON e.event_id = le.event_id
                    LEFT JOIN `event` ON `event`.id = le.event_id
                    LEFT JOIN `user` ON `user`.id = le.signed_by_user_id
                    WHERE event.event_type_id = ".$evt_type_id."
                    AND le.protected_file_id IS NOT NULL
                    AND UNIX_TIMESTAMP(le.signature_date) IS NOT NULL
                    AND user.id IS NOT NULL
                    ;"
            );

            $this->execute("UPDATE " . self::NEW_ET . "
                            LEFT JOIN ".self::NEW_2ND_ET." ON ".self::NEW_ET.".event_id = ".self::NEW_2ND_ET.".event_id
                            LEFT JOIN ".self::NEW_ITEM." ON ".self::NEW_2ND_ET.".id = ".self::NEW_ITEM.".element_id
                            SET ".self::NEW_ET.".signature_id = ".self::NEW_ITEM.".id WHERE ".self::NEW_ITEM.".signatory_role = 'Confirmed by'"
            );
        }
    }
}
