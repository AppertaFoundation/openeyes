<?php

class m130424_095050_trabectome_procedure extends CDbMigration
{
	public function up()
	{
		if (!$proc = Yii::app()->db->createCommand()->select("*")->from("proc")->where("term=:term",array(':term'=>'Trabectome'))->queryRow()) {
			$this->insert('proc',array('term'=>'Trabectome','short_format'=>'Trabectome','default_duration'=>20,'snomed_code'=>'31337','snomed_term'=>'Trabectome'));
		}
	}

	public function down()
	{
		if ($proc = Yii::app()->db->createCommand()->select("*")->from("proc")->where("term=:term",array(':term'=>'Trabectome'))->queryRow()) {
			$this->delete('proc','id='.$proc['id']);
		}
	}
}
