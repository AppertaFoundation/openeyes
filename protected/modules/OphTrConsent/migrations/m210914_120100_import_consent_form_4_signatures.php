<?php

class m210914_120100_import_consent_form_4_signatures extends OEMigration
{
    const LEGACY_ET = "et_ophtrconsent_best_interest_decision";
    const LEGACY_2ND_ET = "et_ophtrconsent_best_interest_decision_ppl";

    const NEW_ET = "et_ophtrconsent_others_involved_decision_making_process";
    const NEW_ASSIGNMENT_ET = "ophtrconsent_others_involved_decision_making_process_contact";

    const NEW_SIGNATURE_ET = "et_ophtrconsent_esign";
    const NEW_SIGNATURE_ITEM = "ophtrconsent_signature";


    public function safeUp()
    {
        $this->upgradeMakingProcessContactSignatures();
    }

    public function safeDown()
    {
        // truncate not working on related tables
        $this->execute("DELETE FROM " . self::NEW_ASSIGNMENT_ET);
        $this->execute("DELETE FROM " . self::NEW_ET);
    }

    public function upgradeMakingProcessContactSignatures()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET)) {
            $best_decision_elements = $this->dbConnection->createCommand("SELECT * FROM " . self::LEGACY_ET)->queryAll();

            foreach ($best_decision_elements as $element) {
                $this->execute("
                INSERT INTO " . self::NEW_ET . "(event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                VALUES
                (
                " . $element['event_id'] . ",
                " . $element['last_modified_user_id'] . ",
                '" . $element['last_modified_date'] . "',
                " . $element['created_user_id'] . ",
                '" . $element['created_date'] . "')
                ");

                $old_element_id = $element['id'];
                $element_id = $this->getDbConnection()->getLastInsertID();
                $event_id = $element['event_id'];

                // CONTACTS FROM ALL ELEMENTS
                $this->execute("
                INSERT INTO " . self::NEW_ASSIGNMENT_ET . " (
                    element_id,
                    contact_type_id,
                    first_name,
                    consent_patient_relationship_id,
                    consent_patient_contact_method_id,
                    other_relationship,
                    other_contact_method,
                    signature_required,
                    last_modified_user_id,
                    last_modified_date,
                    created_user_id,
                    created_date
                )
                SELECT
                    ".$element_id.",
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
                    (
						CASE
							WHEN b.contact_method = 1 THEN 1
							WHEN b.contact_method = 2 THEN 0
							ELSE 0
						END
					) AS signature_required,  
                    b.last_modified_user_id, 
                    b.last_modified_date, 
                    b.created_user_id, 
                    b.created_date
                FROM ".self::LEGACY_2ND_ET." AS b
					LEFT JOIN `protected_file` pf ON pf.id = b.`signature_file_id`
                WHERE b.`element_id` = ".$old_element_id."
                ;");

                $this->execute("
                INSERT INTO ".self::NEW_SIGNATURE_ET." (
                    `event_id`,
                    `healthprof_signature_id`,
                    `last_modified_user_id`,
                    `last_modified_date`,
                    `created_user_id`,
                    `created_date`
                )
                SELECT
                    ".$event_id.",
                    null as healthprof_signature_id,
                    b.last_modified_user_id, 
                    b.last_modified_date, 
                    b.created_user_id, 
                    b.created_date
                FROM ".self::LEGACY_2ND_ET." AS b
					LEFT JOIN `protected_file` pf ON pf.id = b.`signature_file_id`
                WHERE b.`signature_file_id` IS NOT NULL AND b.`element_id` = ".$old_element_id."
                ;");

                $signature_element_id = $this->getDbConnection()->getLastInsertID();

                $this->execute("
                INSERT INTO " . self::NEW_SIGNATURE_ITEM . " (
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
                    ".$signature_element_id.",
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
                FROM ".self::LEGACY_2ND_ET." AS b
					LEFT JOIN `protected_file` pf ON pf.id = b.`signature_file_id`
                WHERE b.`signature_file_id` IS NOT NULL AND b.`element_id` = ".$old_element_id."
                ;");

                $signature_item_id = $this->getDbConnection()->getLastInsertID();
                $this->execute("UPDATE ".self::NEW_SIGNATURE_ET." SET healthprof_signature_id = ".$signature_item_id." WHERE id = ".$signature_element_id.";");
            }
        }
    }
}
