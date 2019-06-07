<?php

class m190325_113630_rename_common_subspecialty_meds extends CDbMigration
{
	public function up()
	{
		/** @var CDbTransaction $transaction */
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$rules = MedicationSetRule::model()->findAll("usage_code='COMMON_OPH'");
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
			$rules = MedicationSetRule::model()->findAll("usage_code='COMMON_OPH'");
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