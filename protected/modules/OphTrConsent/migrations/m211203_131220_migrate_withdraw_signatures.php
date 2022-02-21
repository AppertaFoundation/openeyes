<?php

class m211203_131220_migrate_withdraw_signatures extends OEMigration
{
    private const et_withdraw_table = "et_ophtrconsent_withdrawal";
    private const et_withdraw_assign_table = "et_ophtrconsent_withdrawal_patient_signature"; // archive
    private const et_signature_table = "et_ophtrconsent_esign"; // spec
    private const signature_item_table = "ophtrconsent_signature"; // spec

    public function addSignatureElement($element)
    {
        $element_id = null;

        $existing_element = $this->dbConnection->createCommand(
            "SELECT id FROM " . self::et_signature_table . " WHERE event_id = :event_id LIMIT 1;"
        )->queryScalar(array(':event_id' => $element['event_id']));

        if (!$existing_element) {
            $data = [
                'event_id' => $element['event_id'],
                'healthprof_signature_id' => null,
                'last_modified_user_id' => $element['last_modified_user_id'],
                'last_modified_date' => $element['last_modified_date'],
                'created_user_id' => $element['created_user_id'],
                'created_date' => $element['created_date']
            ];

            $this->insert(self::et_signature_table, $data);
            $element_id = $this->getDbConnection()->getLastInsertID();
        } else {
            $element_id = $existing_element;
        }
        return $element_id;
    }

    public function addSignatureElementItem($signature_element_id, $withdraw, $signature_file_id)
    {
        $signature_file_data = $this->dbConnection->createCommand("
            SELECT * FROM protected_file WHERE id = {$signature_file_id};
        ")->queryRow();
        $element_type_id = $this->getIdOfElementTypeByClassName('Element_OphTrConsent_Withdrawal');
        $data = [
            "element_id" => $signature_element_id,
            "type" => 3,
            "signature_file_id" => $signature_file_id,
            "signatory_role" => "Withdrawn by",
            "initiator_row_id" => $withdraw['id'],
            "initiator_element_type_id" => $element_type_id,
            "signatory_name" => trim($withdraw["first_name"].' '.$withdraw["last_name"]),
            "timestamp" => new CDbExpression("UNIX_TIMESTAMP('" . $signature_file_data["last_modified_date"] . "')"),
            "last_modified_user_id" => $signature_file_data["last_modified_user_id"],
            "last_modified_date" => $signature_file_data["last_modified_date"],
            "created_user_id" => $signature_file_data["created_user_id"],
            "created_date" => $signature_file_data["created_date"]
        ];

        $this->insert(self::signature_item_table, $data);
        return $this->getDbConnection()->getLastInsertID();
    }

    public function updateWithdraw($contact_id, $signature_item_id)
    {
        $this->update(self::et_withdraw_table, ["signature_id" => $signature_item_id], "id = {$contact_id}");
    }

    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable(self::et_withdraw_assign_table)) {
            $withdraw_signatures = $this->dbConnection->createCommand("
                SELECT * FROM " . self::et_withdraw_assign_table . " WHERE protected_file_id IS NOT NULL;
            ")->queryAll();

            foreach ($withdraw_signatures as $signature) {
                $signature_file_id = $signature['protected_file_id'];

                $withdraw_element = $this->dbConnection->createCommand("
                    SELECT * FROM " . self::et_withdraw_table . " WHERE event_id = :event_id LIMIT 1;
                ")->queryRow(true,array(':event_id' => $signature['event_id']));

                $signature_element_id = $this->addSignatureElement($withdraw_element);
                $signature_item_id = $this->addSignatureElementItem($signature_element_id, $withdraw_element, $signature_file_id);
                $this->updateWithdraw($withdraw_element['id'], $signature_item_id);
            }
        } else {
            echo self::et_withdraw_assign_table." table not found. Migration skipped.".PHP_EOL;
        }
    }

    public function safeDown()
    {
        echo "m211203_131220_migrate_withdraw_signatures does not support migration down.\n";
        return true;
    }
}
