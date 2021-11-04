<?php

class m211028_104120_migrate_deputy_signatures extends OEMigration
{
    protected const CONTACT_TYPE = 1; // PATIENT CONTACT

    protected const DEPUTY_SIGNATURE_ET = "et_ophtrconsent_best_interest_decision_deputy_signature";
    protected const ESIGN_ET = "et_ophtrconsent_esign";
    protected const ESIGN = "ophtrconsent_signature";
    protected const CONTACT_ASSIGN = "ophtrconsent_others_involved_decision_making_process_contact";
    protected const ET = "et_ophtrconsent_others_involved_decision_making_process";

    private function createNewElementIfNeeded($element)
    {
        $new_element_id = null;

        $existing_element = $this->execute("SELECT id FROM " . self::ET . " WHERE event_id = {$element['event_id']};");
        if (!$existing_element) {
            ob_start();
            $this->insert(self::ET, [
                'event_id' => $element['event_id'],
                'last_modified_user_id' => $element['last_modified_user_id'],
                'last_modified_date' => $element['last_modified_date'],
                'created_user_id' => $element['created_user_id'],
                'created_date' => $element['created_date']
            ]);
            ob_clean();
            $new_element_id = Yii::app()->db->getLastInsertID();
        } else {
            $new_element_id = $existing_element['id'];
        }
        return $new_element_id;
    }

    private function addContact($new_element_id, $old_element_id)
    {
        ob_start();
        $this->execute("
            INSERT INTO " . self::CONTACT_ASSIGN . " (
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
                {$new_element_id} as element_id,
                1 AS contact_type_id,
                dds.signatory_name,
                8 AS consent_patient_relationship_id, -- Other....
                IF(dds.signatory_required,
                    (SELECT id FROM ophtrconsent_patient_contact_method WHERE need_signature = 1),
                    (SELECT id FROM ophtrconsent_patient_contact_method WHERE need_signature = 0)
                ) AS consent_patient_contact_method_id,
                relation_to_patient AS `other_relationship`,
                'N/A' AS `other_contact_method`,
                dds.signatory_required AS signature_required,  
                dds.last_modified_user_id,
                dds.last_modified_date, 
                dds.created_user_id, 
                dds.created_date
            FROM " . self::DEPUTY_SIGNATURE_ET . " dds
                LEFT JOIN `protected_file` pf ON pf.id = dds.`protected_file_id`
            WHERE dds.id = " . $old_element_id . " AND protected_file_id IS NOT NULL
        ;");
        ob_clean();
        return Yii::app()->db->getLastInsertID();
    }

    private function createSignatureElementIfNeeded($element)
    {
        $new_element_id = null;
        $existing_signature_element = $this->execute("SELECT id FROM " . self::ESIGN_ET . " WHERE event_id = {$element['event_id']};");
        if (!$existing_signature_element) {
            ob_start();
            $this->insert(self::ESIGN_ET, [
                'event_id' => $element['event_id'],
                'last_modified_user_id' => $element['last_modified_user_id'],
                'last_modified_date' => $element['last_modified_date'],
                'created_user_id' => $element['created_user_id'],
                'created_date' => $element['created_date']
            ]);
            ob_clean();
            $new_element_id = Yii::app()->db->getLastInsertID();
        } else {
            $new_element_id = $existing_signature_element['id'];
        }

        return $new_element_id;
    }

    private function addSignature($new_signature_element_id, $old_element_id)
    {
        ob_start();
        $this->execute("
                INSERT INTO " . self::ESIGN . " (
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
                    {$new_signature_element_id} as element_id,
                    3 as signature_type,
                    dds.protected_file_id,
                    dds.relation_to_patient as signature_role,
                    dds.signatory_name,
                    UNIX_TIMESTAMP(signature_date) as `timestamp`,
                    dds.last_modified_user_id,
                    dds.last_modified_date, 
                    dds.created_user_id,
                    dds.created_date
                FROM " . self::DEPUTY_SIGNATURE_ET . " AS dds
                    LEFT JOIN `protected_file` pf ON pf.id = dds.`protected_file_id`
                WHERE dds.`protected_file_id` IS NOT NULL AND dds.id = " . $old_element_id . "
                ;
            ");
        ob_clean();
        return Yii::app()->db->getLastInsertID();
    }

    protected function updateContactSignatory($new_signature_id,$new_contact_id)
    {
        $this->update(self::CONTACT_ASSIGN, [
            'contact_signature_id' => $new_signature_id
        ],
            'id = '.$new_contact_id
        );
    }

    public function safeUp()
    {
        $this->setVerbose(false);
        if ($this->dbConnection->schema->getTable(self::DEPUTY_SIGNATURE_ET)) {
            $deputy_elements = $this->dbConnection->createCommand("SELECT * FROM " . self::DEPUTY_SIGNATURE_ET)->queryAll();

            echo "  Migrate " . count($deputy_elements) . " 'deputy signature element' to the 'others involved decision making process' element." . PHP_EOL;
            foreach ($deputy_elements as $element) {
                $new_element_id = $this->createNewElementIfNeeded($element);
                $old_element_id = $element["id"];

                $new_contact_id = $this->addContact($new_element_id, $old_element_id);
                if ((int)$element['protected_file_id'] > 0) {
                    $new_signature_element_id = $this->createSignatureElementIfNeeded($element);
                    $new_signature_id = $this->addSignature($new_signature_element_id, $old_element_id);

                    $this->updateContactSignatory($new_signature_id,$new_contact_id);
                }
            }
        }
    }

    public function safeDown()
    {
        return false;
    }
}
