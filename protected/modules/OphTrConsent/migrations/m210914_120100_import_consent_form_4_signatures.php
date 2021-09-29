<?php

class m210914_120100_import_consent_form_4_signatures extends OEMigration
{
    const LEGACY_ET = "et_ophtrconsent_best_interest_decision";
    const LEGACY_2ND_ET = "et_ophtrconsent_best_interest_decision_ppl";

    const NEW_ET = "et_ophtrconsent_others_involved_decision_making_process";
    const NEW_2ND_ET = "ophtrconsent_others_involved_decision_making_process_contact";
    const NEW_ITEM = "ophtrconsent_signature";

    public function up()
    {
        $this->upgradeMakingProcessContactSignatures();
    }

    public function safeDown()
    {
        $this->execute("TRUNCATE TABLE " . self::NEW_ITEM);
        $this->execute("TRUNCATE TABLE " . self::NEW_ET);
        $this->execute("TRUNCATE TABLE " . self::NEW_2ND_ET);
    }

    public function upgradeMakingProcessContactSignatures()
    {
        $evt_type_id = $this->dbConnection
            ->createCommand("SELECT `id` FROM `event_type` WHERE `class_name` = 'OphTrConsent';")
            ->queryScalar();

        if ($this->dbConnection->schema->getTable(self::LEGACY_ET)) {
            $best_decision_elements = $this->dbConnection
            ->createCommand("
				SELECT *FROM " . self::LEGACY_ET . "
				")->queryAll();

            foreach ($best_decision_elements as $element) {
                $this->execute("
                INSERT INTO " . self::NEW_ET . "
                (id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                VALUES
                (
                " . $element['id'] . ",
                " . $element['event_id'] . ",
                " . $element['last_modified_user_id'] . ",
                '" . $element['last_modified_date'] . "',
                " . $element['created_user_id'] . ",
                '" . $element['created_date'] . "')
                ");

                $element_id = $this->getDbConnection()->getLastInsertID();

                $this->execute("
                INSERT INTO " . self::NEW_2ND_ET . " (
                element_id,
                contact_type_id,
                first_name,
                consent_patient_relationship_id,
                consent_patient_contact_method_id,
                other_relationship,
                other_contact_method,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
                )
                SELECT
                    b.element_id,
                    1,
                    b.name,
                    (
						CASE
							WHEN relationship_status = 1 THEN 1
							WHEN relationship_status = 2 THEN 2
							WHEN relationship_status = 4 THEN 3
							WHEN relationship_status = 5 THEN 4
							WHEN relationship_status = 6 THEN 5
							WHEN relationship_status = 7 THEN 6
							WHEN relationship_status = 8 THEN 7
							WHEN relationship_status = 9 THEN 8
							ELSE 8
						END
					) as consent_patient_relationship_id,
                    b.contact_method as consent_patient_contact_method_id,
					IF(relationship_status=3,'IMCA',b.`relationship_other`) as `other_relationship`,
					IF(b.contact_method=3, 'Other','') as `other_contact_method`,
                    b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM et_ophtrconsent_best_interest_decision_ppl AS b
					LEFT JOIN `protected_file` pf ON pf.id = b.`signature_file_id`
                WHERE b.`signature_file_id` IS NOT NULL AND b.`element_id` = " . $element_id . "
                "
                );

                $this->execute("
                INSERT INTO " . self::NEW_ITEM . " (
                    element_id,
                    `type`,
                    signature_file_id,
                    signatory_role,
                	signatory_name,
                	`timestamp`,
                	last_modified_user_id,
                	last_modified_date,
                	created_user_id,
                	created_date
                )
                SELECT
                    b.element_id,
                    3 as signature_type,
                    b.signature_file_id,
                    (SELECT `name` FROM `ophtrconsent_patient_relationship` opr WHERE opr.id = (
						CASE
							WHEN relationship_status = 1 THEN 1
							WHEN relationship_status = 2 THEN 2
							WHEN relationship_status = 4 THEN 3
							WHEN relationship_status = 5 THEN 4
							WHEN relationship_status = 6 THEN 5
							WHEN relationship_status = 7 THEN 6
							WHEN relationship_status = 8 THEN 7
							WHEN relationship_status = 9 THEN 8
							ELSE 8
						END)
					) as signature_role,
                    b.name,
                    UNIX_TIMESTAMP(pf.`last_modified_date`) as `timestamp`,
                    b.last_modified_user_id, b.last_modified_date, b.created_user_id, b.created_date
                FROM et_ophtrconsent_best_interest_decision_ppl AS b
					LEFT JOIN `protected_file` pf ON pf.id = b.`signature_file_id`
                WHERE b.`signature_file_id` IS NOT NULL AND b.`element_id` = " . $element_id . "
                "
                );

                $signature_id = $this->dbConnection
                    ->createCommand("SELECT id FROM " . self::NEW_ITEM . " ORDER BY id DESC LIMIT 1;")
                    ->queryScalar();

                $this->execute("
					UPDATE " . self::NEW_2ND_ET . " SET contact_signature_id = " . $signature_id . " WHERE element_id = " . $element_id . "
					");
            }
        }
    }
}
