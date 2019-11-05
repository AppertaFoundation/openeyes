<?php

class m191029_172959_move_sets_to_autoset extends CDbMigration
{
	public function safeUp()
	{
        $data_provider = new CActiveDataProvider('MedicationSet', [
            'criteria' => [
                'condition' => 'automatic = 0'
            ],
        ]);
        $iterator = new CDataProviderIterator($data_provider);

        foreach ($iterator as $set) {

            // create new automatic set based on the non-automatic
            $new_set = new MedicationSet();
            $new_set->attributes = $set->attributes;
            $new_set->automatic = 1;
            $new_set->created_user_id = 3;
            $new_set->created_date = date('Y-m-d H:i:s');

            // Cannot have a medication set with the name
            $set->name = $set->name . " (manual)";
            $set->update(['name']);
            $set->refresh();

            if (!$new_set->save()) {

                echo '<pre>' . print_r($new_set->getErrors(), true) . '</pre>';
                file_put_contents('/tmp/debug_error.txt', print_r($new_set->getErrors(), true), FILE_APPEND);
            } else {
                file_put_contents('/tmp/debug.txt', "{$new_set->id} | ", FILE_APPEND);
            }

            // create entry in medication_set_auto_rule_medication for every medication set item in set
            foreach ($set->medicationSetItems as $medication_item) {
                $set_auto_rule = new MedicationSetAutoRuleMedication();
                $set_auto_rule->medication_id = $medication_item->medication_id;
                $set_auto_rule->medication_set_id = $new_set->id;
                $set_auto_rule->include_children = 1;
                $set_auto_rule->include_parent = 1;
                $set_auto_rule->created_user_id = 3;
                $set_auto_rule->created_date = date('Y-m-d H:i:s');

                // set all the defaults as well
                foreach ($set_auto_rule->getAttributes() as $attribute) {
                    // if attribute starts with 'default'
                    if (strpos($attribute, 'default') === 0) {
                        $set_auto_rule->{$attribute} = $medication_item->{$attribute};
                    }
                }

                $set_auto_rule->save();

                foreach ($set->medicationSetRules as $set_rule) {
                    $new_rule = new MedicationSetRule();
                    $new_rule->attributes = $set_rule->attributes;
                    $new_rule->medication_set_id = $new_set->id;
                    $new_rule->created_user_id = 3;
                    $new_rule->save();
                }
            }
        }
	}

	public function safeDown()
	{
			echo "m191029_172959_move_sets_to_autoset does not support migration down.\n";
	}
}
