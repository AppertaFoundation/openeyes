<?php

class m131206_091441_trim_audit_data extends CDbMigration
{
	public function up()
	{
		$null_ids = array();

		$limit = 10000;
		$offset = 0;

		while (1) {
			$data = Yii::app()->db->createCommand()->select("id,data")->from("audit")->order("id asc")->limit($limit)->offset($offset)->queryAll();

			if (empty($data)) break;

			foreach ($data as $row) {
				if (@json_decode($row['data'])) {
					$null_ids[] = $row['id'];

					if (count($null_ids) >= 1000) {
						$this->resetData($null_ids);
						$null_ids = array();
					}
				}
			}
		}

		if (!empty($null_ids)) {
			$this->resetData($null_ids);
		}
	}

	public function resetData($null_ids)
	{
		$this->update('audit',array('data' => null),"id in (".implode(",",$null_ids).")");
	}

	public function down()
	{
	}
}
