<?php

class m131206_091441_trim_audit_data extends CDbMigration
{
	public function up()
	{
		$null_ids = array();

		foreach (Yii::app()->db->createCommand()->select("id,data")->from("audit")->queryAll() as $row) {
			if (@json_decode($row['data'])) {
				$null_ids[] = $row['id'];
			}
		}

		if (!empty($null_ids)) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$null_ids);

			Audit::model()->updateAll(array('data' => null),$criteria);
		}
	}

	public function down()
	{
	}
}
