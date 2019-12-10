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
            $new_set->id = null;
            $new_set->automatic = 1;
            $new_set->created_date = date('Y-m-d H:i:s');

            // Cannot have a medication set with the name
            $set->name = $set->name . " (manual)";
            $set->update(['name']);
            $set->refresh();

            if (!$new_set->save()) {
                \OELog::log(print_r($new_set->getErrors(), true));
            }

            \MedicationSetAutoRuleSetMembership::model()->updateAll(['source_medication_set_id' => $new_set->id], 'source_medication_set_id = :set_id', [':set_id' => $set->id]);
            \MedicationSetAutoRuleSetMembership::model()->updateAll(['target_medication_set_id' => $new_set->id], 'target_medication_set_id = :set_id', [':set_id' => $set->id]);
            \MedicationSetAutoRuleAttribute::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $set->id]);
            \MedicationSetAutoRuleMedication::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $set->id]);
            \MedicationSetItem::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $set->id]);
            \MedicationSetRule::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $set->id]);
            OphCiExaminationAllergy::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $set->id]);


            /*
                * Update ophciexamination_risk_tag
                */
            Yii::app()->db->createCommand("UPDATE ophciexamination_risk_tag SET medication_set_id = " . $new_set->id . " WHERE medication_set_id = " . $set->id)->execute();

            $set->delete();
        }
    }

    public function safeDown()
    {
            echo "m191029_172959_move_sets_to_autoset does not support migration down.\n";
    }
}
