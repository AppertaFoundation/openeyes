<?php

class m191029_172959_move_sets_to_autoset extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CDbException
     * @throws Exception
     */
    public function safeUp()
    {
        $db = $this->dbConnection;
        $iterator = $db->createCommand('SELECT * FROM medication_set WHERE automatic = 0')
            ->queryAll();

        foreach ($iterator as $set) {
            $this->update(
                'medication_set',
                array('name' => $set['name'] . ' (manual)'),
                'id = :id',
                array(':id' => $set['id'])
            );

            $this->insert(
                'medication_set',
                array(
                    'name' => $set['name'],
                    'deleted_date' => $set['deleted_date'],
                    'last_modified_date' => $set['last_modified_date'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'automatic' => 1,
                    'hidden' => $set['hidden'],
                    'antecedent_medication_set_id' => $set['antecedent_medication_set_id'],
                    'display_order' => $set['display_order'],
                    'last_modified_user_id' => $set['last_modified_user_id'],
                    'created_user_id' => $set['created_user_id'],
                )
            );

            $new_set_id = $db->getLastInsertID();

            $medication_set_items = $db->createCommand('SELECT * FROM medication_set_item WHERE medication_set_id = :set_id')
                ->bindValue(':set_id', $set['id'])
                ->queryAll();

            // create entry in medication_set_auto_rule_medication for every medication set item in set
            foreach ($medication_set_items as $medication_item) {
                $this->insert(
                    'medication_set_auto_rule_medication',
                    array(
                        'medication_id' => $medication_item['medication_id'],
                        'medication_set_id' => $new_set_id,
                        'include_children' => 0,
                        'include_parent' => 0,
                        'created_date' => date('Y-m-d H:i:s'),
                        'default_form_id' => $medication_item['default_form_id'],
                        'default_dose' => $medication_item['default_dose'],
                        'default_route_id' => $medication_item['default_route_id'],
                        'default_dispense_location_id' => $medication_item['default_dispense_location_id'],
                        'default_dispense_condition_id' => $medication_item['default_dispense_condition_id'],
                        'default_frequency_id' => $medication_item['default_frequency_id'],
                        'default_dose_unit_term' => $medication_item['default_dose_unit_term'],
                        'default_duration_id' => $medication_item['default_duration_id'],
                    )
                );
            }

            $medication_set_rules = $db->createCommand('SELECT * FROM medication_set_rule WHERE medication_set_id = :set_id')
                ->bindValue(':set_id', $set['id'])
                ->queryAll();

            foreach ($medication_set_rules as $set_rule) {
                $this->insert(
                    'medication_set_rule',
                    array(
                        'subspecialty_id' => $set_rule['subspecialty_id'],
                        'site_id' => $set_rule['site_id'],
                        'usage_code_id' => $set_rule['usage_code_id'],
                        'deleted_date' => $set_rule['deleted_date'],
                        'medication_set_id' => $new_set_id
                    )
                );
            }

            $this->update(
                'ophciexamination_risk_tag',
                array('medication_set_id' => $new_set_id),
                'medication_set_id = :set_id',
                array(':set_id' => $set['id'])
            );
        }
    }

    public function safeDown()
    {
        echo "m191029_172959_move_sets_to_autoset does not support migration down.\n";
    }
}
