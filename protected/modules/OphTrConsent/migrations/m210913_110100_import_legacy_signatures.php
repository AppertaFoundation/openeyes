<?php

class m210913_110100_import_legacy_signatures extends OEMigration
{
    const LEGACY_ET = "et_ophtrconsent_interpreter_signature";
    const LEGACY_2ND_ET = "et_ophtrconsent_witness_signature";
    const LEGACY_3_ET = "et_ophtrconsent_patient_signature";
    const LEGACY_4_ET = "et_ophtrconsent_parent_signature";
    const LEGACY_5_ET = "et_ophtrconsent_child_signature";
    const LEGACY_6_ET = "et_consultant_signature";

    const NEW_ET = "et_ophtrconsent_additional_signatures";
    const NEW_ITEM = "ophtrconsent_signature";
    const NEW_2ND_ET = "et_ophtrconsent_esign";

    public function up()
    {
        $this->upgradeHealthSignatures();
        $this->upgradeAdditionalSignatures();
    }

    public function safeDown()
    {
        if (
            $this->dbConnection->schema->getTable(self::NEW_ET) && $this->dbConnection->schema->getTable(self::NEW_2ND_ET) &&
            $this->dbConnection->schema->getTable(self::NEW_ITEM)
        ) {
            $this->execute("TRUNCATE TABLE " . self::NEW_ITEM);
            $this->execute("TRUNCATE TABLE " . self::NEW_ET);
            $this->execute("TRUNCATE TABLE " . self::NEW_2ND_ET);
        }
    }

    public function upgradeHealthSignatures()
    {
        $evt_type_id = $this->dbConnection
            ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphTrConsent';")
            ->queryScalar();

        if (
            $this->dbConnection->schema->getTable(self::LEGACY_6_ET) && $this->dbConnection->schema->getTable(self::NEW_2ND_ET) &&
            $this->dbConnection->schema->getTable(self::NEW_ITEM)
        ) {
            $this->execute("INSERT INTO ".self::NEW_2ND_ET."
                    (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    a.event_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                    FROM ".self::LEGACY_6_ET." AS a
                    LEFT JOIN event ON event.id = a.event_id
                    WHERE event.event_type_id = ".$evt_type_id."
                    AND a.event_id NOT IN (SELECT x.event_id FROM ".self::NEW_2ND_ET." AS x)
                ");

            $this->execute("INSERT INTO ".self::NEW_ITEM."
                    (element_id, type, signature_file_id, signatory_role, signatory_name, `timestamp`,
                    last_modified_user_id, last_modified_date, created_user_id, created_date)
                    SELECT
                    e.id, " . \BaseSignature::TYPE_OTHER_USER . ", le.protected_file_id, 'Health professional',
                    CONCAT(user.first_name, ' ', user.last_name),
                    UNIX_TIMESTAMP(le.signature_date),
                    le.last_modified_user_id, le.last_modified_date, le.created_user_id, le.created_date
                    FROM ".self::LEGACY_6_ET." AS le
                    LEFT JOIN ".self::NEW_2ND_ET." AS e ON e.event_id = le.event_id
                    LEFT JOIN `event` ON `event`.id = le.event_id
                    LEFT JOIN `user` ON `user`.id = le.signed_by_user_id
                    WHERE event.event_type_id = ".$evt_type_id."
                    AND le.protected_file_id IS NOT NULL
                    AND user.id IS NOT NULL
                    ;"
            );

            $this->execute("UPDATE " . self::NEW_2ND_ET . "
                            LEFT JOIN ".self::NEW_ITEM." ON ".self::NEW_2ND_ET.".id = ".self::NEW_ITEM.".element_id
                            SET ".self::NEW_2ND_ET.".healthprof_signature_id = ".self::NEW_ITEM.".id WHERE ".self::NEW_ITEM.".signatory_role = 'Health professional'"
            );
        }
    }

    public function upgradeAdditionalSignatures()
    {
        if (
            $this->dbConnection->schema->getTable(self::LEGACY_ET) && $this->dbConnection->schema->getTable(self::LEGACY_2ND_ET) &&
            $this->dbConnection->schema->getTable(self::LEGACY_4_ET) && $this->dbConnection->schema->getTable(self::LEGACY_5_ET) &&
            $this->dbConnection->schema->getTable(self::NEW_ET) && $this->dbConnection->schema->getTable(self::NEW_2ND_ET)
        ) {
            foreach ([self::LEGACY_ET, self::LEGACY_2ND_ET, self::LEGACY_3_ET, self::LEGACY_4_ET, self::LEGACY_5_ET] as $item) {
                $this->execute("
                UPDATE " . $item . " cs
                    LEFT JOIN `protected_file` pf ON pf.id = cs.protected_file_id
                SET cs.signature_date = pf.last_modified_date
                WHERE
                    UNIX_TIMESTAMP(cs.signature_date) IS NULL
                    AND cs.protected_file_id IS NOT NULL
                ;
            ");
            }
            $evt_type_id = $this->dbConnection
                ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphTrConsent';")
                ->queryScalar();

            $this->execute("
                INSERT INTO " . self::NEW_ET . " (
                event_id, interpreter_required, interpreter_name, witness_required, witness_name, guardian_required, guardian_name, guardian_relationship, child_agreement, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT
                a.id, e.signatory_required, e.signatory_name,
                b.signatory_required, b.signatory_name,
                c.signatory_required, c.signatory_name, c.relationship_status,
                d.signatory_required, a.last_modified_user_id,
                a.last_modified_date, a.created_user_id, a.created_date
                FROM event as a
                LEFT JOIN " . self::LEGACY_2ND_ET . " as b ON a.id = b.event_id
                LEFT JOIN " . self::LEGACY_4_ET . " as c ON a.id = c.event_id
                LEFT JOIN " . self::LEGACY_5_ET . " as d ON a.id = d.event_id
                LEFT JOIN " . self::LEGACY_ET . " as e ON a.id = e.event_id
                LEFT JOIN " . self::LEGACY_3_ET . " as f ON a.id = f.event_id
                WHERE a.event_type_id = " . $evt_type_id . "
                ");

            $this->execute("
                INSERT INTO " . self::NEW_2ND_ET . "
            (
                                        event_id, last_modified_user_id, last_modified_date, created_user_id, created_date
            )
            SELECT
                a.event_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                FROM " . self::NEW_ET . " as a
                WHERE NOT EXISTS (SELECT *from " . self::NEW_2ND_ET . " WHERE event_id = a.event_id)
                "
            );

            $additionals = $this->dbConnection
                ->createCommand("
                                SELECT * FROM " . self::NEW_ET . "
                                ")->queryAll();

            $this->setVerbose(false);
            foreach ($additionals as $additional) {
                $this->addSignature($additional, self::LEGACY_ET, 'Interpreter', 'interpreter_signature_id', 2);
                $this->addSignature($additional, self::LEGACY_2ND_ET, 'Witness', 'witness_signature_id', 1);
                $this->addSignature($additional, self::LEGACY_3_ET, 'Patient', 'patient_signature_id', 5);
                $this->addSignature($additional, self::LEGACY_4_ET, 'Parent/Guardian', 'guardian_signature_id', 3);
                $this->addSignature($additional, self::LEGACY_5_ET, 'Child', 'child_signature_id', 4);
            }
            $this->setVerbose(true);
        }
    }

    public function addSignature($additional, $table, $role, $attribute, $initiator_row_id)
    {
        $element_type_id = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name = :class_name',
            array(':class_name' => 'OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures'))
            ->queryScalar();
        $signature_item = $this->dbConnection
            ->createCommand("SELECT * FROM " . $table . " WHERE event_id = ".$additional['event_id']."")->queryRow();

        if ($signature_item && !is_null($signature_item['protected_file_id'])) {
            $element_id = $this->dbConnection
                ->createCommand("
                                SELECT id FROM " . self::NEW_2ND_ET . " WHERE event_id = " . $signature_item['event_id'] . "
                                ")->queryScalar();

            $date = strtotime($signature_item['signature_date']);
            $name = str_replace("'"," ",substr($signature_item['signatory_name'], 0, 60));
            $this->execute("
                INSERT INTO " . self::NEW_ITEM . "
            (
                                        element_id, `type`, signature_file_id, signatory_role, signatory_name, initiator_element_type_id, initiator_row_id,
                                        `timestamp`, last_modified_user_id, last_modified_date, created_user_id, created_date
            ) VALUES (
            " . $element_id . ",
            " . \BaseSignature::TYPE_OTHER_USER . ",
            " . $signature_item['protected_file_id'] . ",
            '" . $role . "',
            '" . $name . "',
            '" . $element_type_id . "',
            '" . $initiator_row_id . "',
            " . $date . ",
            " . $signature_item['last_modified_user_id'] . ",
            '" . $signature_item['last_modified_date'] . "',
            " . $signature_item['created_user_id'] . ",
            '" . $signature_item['created_date'] . "'
            )
                "
            );

            $signature_id = $this->dbConnection
                ->createCommand("SELECT id FROM " . self::NEW_ITEM . " ORDER BY id DESC LIMIT 1;")
                ->queryScalar();

            $this->execute("UPDATE " . self::NEW_ET . " SET " . $attribute . " = " . $signature_id . " WHERE id = " . $additional['id'] . "");
        }
    }
}
