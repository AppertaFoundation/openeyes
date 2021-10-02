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
        $this->upgradeAdditionalSignatures();
        $this->upgradeHealthSignatures();
    }

    public function safeDown()
    {
        $this->execute("TRUNCATE TABLE " . self::NEW_ITEM);
        $this->execute("TRUNCATE TABLE " . self::NEW_ET);
        $this->execute("TRUNCATE TABLE " . self::NEW_2ND_ET);
    }

    public function upgradeHealthSignatures()
    {
        $evt_type_id = $this->dbConnection
            ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphTrConsent';")
            ->queryScalar();

        if ($this->dbConnection->schema->getTable(self::LEGACY_6_ET)) {
            $constultant_signatures = $this->dbConnection
                ->createCommand("
				SELECT * FROM " . self::LEGACY_6_ET . " WHERE protected_file_id IS NOT NULL
				")->queryAll();

            foreach ($constultant_signatures as $signature) {
                $isset_signature = $this->dbConnection
                    ->createCommand("SELECT id FROM " . self::NEW_2ND_ET . " WHERE event_id = ".$signature['event_id']." ORDER BY id DESC LIMIT 1;")
                    ->queryScalar();

                if (!$isset_signature) {
                    $this->execute("
                        INSERT INTO " . self::NEW_2ND_ET . " (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                        VALUES (
                    " . $signature['event_id'] . ",
                    " . $signature['last_modified_user_id'] . ",
                    '" . $signature['last_modified_date'] . "',
                    " . $signature['created_user_id'] . ",
                    '" . $signature['created_date'] . "'
                    )
                    ");
                }

                $esign_id = $this->dbConnection
                    ->createCommand("SELECT `id` FROM " . self::NEW_2ND_ET . " WHERE event_id = ".$signature['event_id'].";")
                    ->queryScalar();

                $signatory_name = "N/A";
                if (!empty($signature['signed_by_user_id'])) {
                    $user = $this->dbConnection
                        ->createCommand("SELECT * FROM user WHERE id = " . $signature['signed_by_user_id'] . ";")
                        ->queryRow();
                    $signatory_name  = $user['first_name'] . " " . $user['last_name'];
                    $signatory_name = str_replace("'", "\'", $signatory_name);
                }

                $this->execute("
                INSERT INTO " . self::NEW_ITEM . " (
                element_id,
                `type`,
                signature_file_id,
                signed_user_id,
                signatory_role,
                signatory_name,
                `timestamp`,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
                ) VALUES (
								" . $esign_id . ",
								2,
								" . $signature['protected_file_id'] . ",
								" . $signature['signed_by_user_id'] . ",
								'Health professional',
								'" . $signatory_name ."',
								" . strtotime($signature['signature_date']) . ",
								" . $signature['last_modified_user_id'] . ",
								'" . $signature['last_modified_date'] . "',
								" . $signature['created_user_id'] . ",
								'" . $signature['created_date'] . "'
            		)
        ");

                $healthprof_signature_id = $this->dbConnection
                    ->createCommand("SELECT `id` FROM " . self::NEW_ITEM . " WHERE element_id = ".$esign_id.";")
                    ->queryScalar();

                $this->execute("
					UPDATE " . self::NEW_2ND_ET . " SET healthprof_signature_id = " . $healthprof_signature_id . " WHERE event_id = " . $signature['event_id'] . "
					");
            }
        }
    }

    public function upgradeAdditionalSignatures()
    {

        if ($this->dbConnection->schema->getTable(self::LEGACY_ET) && $this->dbConnection->schema->getTable(self::LEGACY_2ND_ET) && $this->dbConnection->schema->getTable(self::LEGACY_4_ET) && $this->dbConnection->schema->getTable(self::LEGACY_5_ET)) {
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
            // Copy elements
            $this->execute("
                INSERT INTO " . self::NEW_ET . " (
                event_id, interpreter_required, interpreter_name, witness_required, witness_name, guardian_required, guardian_name, guardian_relationship, child_agreement, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT
                a.event_id, a.signatory_required, a.signatory_name, b.signatory_required, b.signatory_name, c.signatory_required, c.signatory_name, c.relationship_status, d.signatory_required, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                FROM " . self::LEGACY_ET . " as a
                LEFT JOIN " . self::LEGACY_2ND_ET . " as b ON a.event_id = b.event_id
                LEFT JOIN " . self::LEGACY_4_ET . " as c ON b.event_id = c.event_id
                LEFT JOIN " . self::LEGACY_5_ET . " as d ON c.event_id = d.event_id
                LEFT JOIN `event` ON `event`.id = a.event_id OR `event`.id = b.event_id OR `event`.id = c.event_id
                WHERE `event`.event_type_id = " . $evt_type_id . "
                 AND (a.protected_file_id IS NOT NULL
                OR b.protected_file_id IS NOT NULL
                OR c.protected_file_id IS NOT NULL)
                ");

            $this->execute("
                INSERT INTO " . self::NEW_2ND_ET . "
            (
            				event_id, last_modified_user_id, last_modified_date, created_user_id, created_date
            )
            SELECT
                a.event_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                FROM " . self::NEW_ET . " as a
                "
            );

            $additionals = $this->dbConnection
                ->createCommand("
				SELECT * FROM " . self::NEW_ET . "
				")->queryAll();

            foreach ($additionals as $additional) {
                $this->addSignature($additional, self::LEGACY_ET, 'Interpreter', 'interpreter_signature_id');
                $this->addSignature($additional, self::LEGACY_2ND_ET, 'Witness', 'witness_signature_id');
                $this->addSignature($additional, self::LEGACY_3_ET, 'Patient', 'patient_signature_id');
                $this->addSignature($additional, self::LEGACY_4_ET, 'Parent/Guardian', 'guardian_signature_id');
                $this->addSignature($additional, self::LEGACY_5_ET, 'Child', 'child_signature_id');
            }
        }
    }

    public function addSignature($additional, $table, $role, $attribute)
    {

        $signature_items = $this->dbConnection
            ->createCommand("
				SELECT * FROM " . $table . " WHERE event_id = ".$additional['event_id']." AND protected_file_id IS NOT NULL
				")->queryAll();

        foreach ($signature_items as $signature_item) {
            $element_id = $this->dbConnection
                ->createCommand("
				SELECT id FROM " . self::NEW_2ND_ET . " WHERE event_id = ".$signature_item['event_id']."
				")->queryScalar();
            $date = strtotime($signature_item['signature_date']);
            $this->execute("
                INSERT INTO " . self::NEW_ITEM . "
            (
            				element_id, `type`, signature_file_id, signatory_role, signatory_name,
            				`timestamp`, last_modified_user_id, last_modified_date, created_user_id, created_date
            ) VALUES (
            " . $element_id . ",
            " . \BaseSignature::TYPE_OTHER_USER . ",
            " . $signature_item['protected_file_id'] . ",
            '".$role."',
            '" . substr($signature_item['signatory_name'], 0, 60) . "',
            ".$date.",
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

            $this->execute("
					UPDATE " . self::NEW_ET . " SET ".$attribute." = " . $signature_id . " WHERE event_id = " . $additional['event_id'] . "
					");
        }
    }
}
