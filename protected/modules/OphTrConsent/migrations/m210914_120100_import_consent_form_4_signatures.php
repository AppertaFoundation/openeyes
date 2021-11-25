<?php

class m210914_120100_import_consent_form_4_signatures extends OEMigration
{
    const LEGACY_ET = "et_ophtrconsent_best_interest_decision";
    const LEGACY_2ND_ET = "et_ophtrconsent_best_interest_decision_ppl";
    const LEGACY_3RD_ET = "et_ophtrconsent_best_interest_decision_user";

    const NEW_ET = "et_ophtrconsent_others_involved_decision_making_process";
    const NEW_ASSIGNMENT_ET = "ophtrconsent_others_involved_decision_making_process_contact";

    const NEW_SIGNATURE_ET = "et_ophtrconsent_esign";
    const NEW_SIGNATURE_ITEM = "ophtrconsent_signature";

    public function safeUp()
    {
        $this->migrateMakingProcessContactSignatures();
    }

    public function safeDown()
    {
        // truncate not working on related tables
        $this->execute("DELETE FROM " . self::NEW_ASSIGNMENT_ET);
        $this->execute("DELETE FROM " . self::NEW_ET);
    }

    public function migrateColleagues()
    {
        if (!$this->dbConnection->schema->getTable(self::LEGACY_3RD_ET)) {
            return true;
        }

        $data = [];
        // cc 150 records
        $colleagues = $this->dbConnection->createCommand("
            SELECT 
                bid.event_id AS event_id,
                bid.id AS old_element_id,
                bid.last_modified_user_id,
                bid.last_modified_date,
                bid.created_user_id,
                bid.created_date,
                u.id as contact_user_id,
                u.first_name,
				u.last_name,
				u.email,
				c.primary_phone,
				c.mobile_phone,
				a.address1,
				a.address2,
                a.city,
                a.country_id,
                a.postcode,
                (SELECT id FROM ophtrconsent_patient_relationship WHERE LOWER(`name`) = 'other') as consent_patient_relationship_id,
                (SELECT id FROM ophtrconsent_patient_contact_method WHERE LOWER(`name`) = 'other') as consent_patient_contact_method_id,
                u.role as `other_relationship`,
                'N/A' as other_contact_method,
                NULL as comment,
                NULL as contact_signature_id,
                0 as signature_required,
                idu.last_modified_user_id,
                idu.last_modified_date,
                idu.created_user_id,
                idu.created_date
            FROM et_ophtrconsent_best_interest_decision bid
                LEFT JOIN et_ophtrconsent_best_interest_decision_user idu ON idu.element_id = bid.id
                LEFT JOIN `user` u ON u.id = idu.user_id
                LEFT JOIN contact c ON c.id = u.contact_id
                LEFT JOIN address a ON a.contact_id = c.id
            WHERE idu.id IS NOT NULL
            ;
        ")->queryAll();

        foreach ($colleagues as $colleague) {
            $event_id = $colleague['event_id'];
            $existing_element_id = $this->dbConnection->createCommand("SELECT id FROM " . self::NEW_ET . " WHERE event_id = :event_id LIMIT 1;")->queryScalar(array(':event_id'=>$event_id));

            if ((int)$existing_element_id > 0) {
                $element_id = $existing_element_id;
            } else {
                $this->execute("
                    INSERT INTO " . self::NEW_ET . "(event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                    VALUES
                    (
                    " . $event_id . ",
                    " . $colleague['last_modified_user_id'] . ",
                    '" . $colleague['last_modified_date'] . "',
                    " . $colleague['created_user_id'] . ",
                    '" . $colleague['created_date'] . "')
                ");
                $element_id = $this->getDbConnection()->getLastInsertID();
            }

            $data[] = [
                "element_id" => $element_id,
                "contact_type_id" => 2,
                "contact_user_id" => $colleague['contact_user_id'],
                "first_name" => $colleague['first_name'],
                "last_name" => $colleague['last_name'],
                "email" => $colleague['email'],
                "phone_number" => $colleague['primary_phone'],
                "mobile_number" => $colleague['mobile_phone'],
                "address_line1" => $colleague['address1'],
                "address_line2" => $colleague['address2'],
                "city" => $colleague['city'],
                "country_id" => $colleague['country_id'],
                "postcode" => $colleague['postcode'],
                "consent_patient_relationship_id" => $colleague['consent_patient_relationship_id'],
                "consent_patient_contact_method_id" => $colleague['consent_patient_contact_method_id'],
                "other_relationship" => $colleague['other_relationship'],
                "other_contact_method" => $colleague['other_contact_method'],
                "comment" => NULL,
                "contact_signature_id" => NULL,
                "signature_required" => 0,
                "last_modified_user_id" => $colleague['last_modified_user_id'],
                "last_modified_date" => $colleague['last_modified_date'],
                "created_user_id" => $colleague['created_user_id'],
                "created_date" => $colleague['created_date']
            ];
        }

        if (!empty($data)) {
            $this->insertMultiple(self::NEW_ASSIGNMENT_ET, $data);
        }
    }

    public function addContacts($contact_element_id, $element)
    {
        // GET CONTACT DATA
        $contacts = $this->dbConnection->createCommand("
            SELECT
                signature_file_id,
                " . $contact_element_id . " as element_id,
                1 as contact_type_id,
                b.name as first_name,
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
                    CASE WHEN b.contact_method = 1 THEN 1
                        WHEN b.contact_method = 2 THEN 0
                        ELSE 0
                    END
                ) AS signature_required,  
                b.last_modified_user_id, 
                b.last_modified_date, 
                b.created_user_id, 
                b.created_date,
                b.contact_details as comment
            FROM " . self::LEGACY_2ND_ET . " AS b
                LEFT JOIN `protected_file` pf ON pf.id = b.`signature_file_id`
            WHERE b.`element_id` = :element_id")->queryAll(true, array(':element_id'=>$element['id']));

        // cc 1-3 contact
        foreach ($contacts as $contact) {
            $signature_file_id = (int)$contact['signature_file_id'];
            unset($contact['signature_file_id']);
            $this->insert(self::NEW_ASSIGNMENT_ET, $contact);

            if ($signature_file_id > 0) {
                $contact_id = $this->getDbConnection()->getLastInsertID();
                $signature_element_id = $this->addSignatureElement($element);
                $signature_item_id = $this->addSignatureElementItem($signature_element_id, $contact, $signature_file_id);
                $this->updateContact($contact_id, $signature_item_id);
            }
        }
    }

    public function addSignatureElement($element)
    {
        $existing_element = $this->dbConnection->createCommand(
            "SELECT id FROM " . self::NEW_SIGNATURE_ET . " WHERE event_id = :event_id LIMIT 1;"
        )->queryScalar(array(':event_id'=>$element['event_id']));
        $element_id = null;

        if (!$existing_element) {
            $data = [
                'event_id' => $element['event_id'],
                'healthprof_signature_id' => null,
                'last_modified_user_id' => $element['last_modified_user_id'],
                'last_modified_date' => $element['last_modified_date'],
                'created_user_id' => $element['created_user_id'],
                'created_date' => $element['created_date']
            ];

            $this->insert(self::NEW_SIGNATURE_ET, $data);
            $element_id = $this->getDbConnection()->getLastInsertID();
        } else {
            $element_id = $existing_element;
        }
        return $element_id;
    }

    public function addSignatureElementItem($signature_element_id, $contact, $signature_file_id)
    {
        $signature_file_data = $this->dbConnection->createCommand("
            SELECT * FROM protected_file WHERE id = {$signature_file_id};
        ")->queryRow();

        $data = [
            "element_id" => $signature_element_id,
            "type" => 3,
            "signature_file_id" => $signature_file_id,
            "signatory_role" => new CDbExpression("
                (SELECT `name` FROM `ophtrconsent_patient_relationship` opr WHERE opr.id = {$contact['consent_patient_relationship_id']})
            "),
            "signatory_name" => $contact["first_name"],
            "timestamp" => new CDbExpression("UNIX_TIMESTAMP('" . $signature_file_data["last_modified_date"] . "')"),
            "last_modified_user_id" => $signature_file_data["last_modified_user_id"],
            "last_modified_date" => $signature_file_data["last_modified_date"],
            "created_user_id" => $signature_file_data["created_user_id"],
            "created_date" => $signature_file_data["created_date"]
        ];

        $this->insert(self::NEW_SIGNATURE_ITEM, $data);
        return $this->getDbConnection()->getLastInsertID();
    }

    public function createContactElement($element)
    {
        $existing_element = $this->dbConnection->createCommand(
            "SELECT id FROM " . self::NEW_ET . " WHERE event_id = {$element['event_id']} LIMIT 1;"
        )->queryScalar();

        if (!$existing_element) {
            echo 'Create ' . self::NEW_ET . ' element...' . PHP_EOL;
            $this->execute("INSERT INTO " . self::NEW_ET . "(event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                        VALUES
                        (
                            " . $element['event_id'] . ",
                            " . $element['last_modified_user_id'] . ",
                            '" . $element['last_modified_date'] . "',
                            " . $element['created_user_id'] . ",
                            '" . $element['created_date'] . "'
                        )
                    ");
            $element_id = $this->getDbConnection()->getLastInsertID();
        } else {
            $element_id = $existing_element['id'];
        }

        return $element_id;
    }

    public function updateContact($contact_id, $signature_item_id)
    {
        $this->update(self::NEW_ASSIGNMENT_ET, ["contact_signature_id" => $signature_item_id], "id = {$contact_id}");
    }

    public function migrateMakingProcessContactSignatures()
    {
        if ($this->dbConnection->schema->getTable(self::LEGACY_ET)) {
            $best_decision_elements = $this->dbConnection->createCommand("SELECT * FROM " . self::LEGACY_ET)->queryAll();
            echo 'Migrate contacts: ' . count($best_decision_elements) . PHP_EOL;

            foreach ($best_decision_elements as $element) {
                $event_id = $element['event_id'];
                $contact_element_id = $this->createContactElement($element);
                $this->addContacts($contact_element_id, $element);
            }
            $this->migrateColleagues();
        }
    }
}
