<?php

class m190325_113630_rename_common_subspecialty_meds extends CDbMigration
{
	public function up()
	{
		/** @var CDbTransaction $transaction */
		$transaction = Yii::app()->db->beginTransaction();
		try {
            $common_oph_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
			$rules = MedicationSetRule::model()->findAll("usage_code_id='{$common_oph_id}'");
			foreach ($rules as $rule) {
				/** @var MedicationSetRule $rule */
				$set = $rule->medicationSet;
				$site = $rule->site->name;
				$subspec = $rule->subspecialty->name;

				$set->name = "Common $site $subspec medications";
				if(!$set->save()) {
					$transaction->rollback();
					return false;
				}
			}
		}
		catch (Exception $e) {
			$transaction->rollback();
			return false;
		}

		$transaction->commit();
		return true;
	}

	public function down()
	{
		/** @var CDbTransaction $transaction */
		$transaction = Yii::app()->db->beginTransaction();
		try {
            $common_oph_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
            $rules = MedicationSetRule::model()->findAll("usage_code_id='{$common_oph_id}'");
			foreach ($rules as $rule) {
				$set = $rule->medicationSet;
				$set->name = "Common subspecialty medications";
				if(!$set->save()) {
					$transaction->rollback();
					return false;
				}
			}
		}
		catch (Exception $e) {
			$transaction->rollback();
			return false;
		}

		$transaction->commit();
		return true;
	}
}