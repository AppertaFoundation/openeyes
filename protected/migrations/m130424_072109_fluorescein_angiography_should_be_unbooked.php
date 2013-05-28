<?php

class m130424_072109_fluorescein_angiography_should_be_unbooked extends CDbMigration
{
	public function up()
	{
		if ($proc = Yii::app()->db->createCommand()->select("*")->from("proc")->where('term=:term',array(':term'=>'Fluorescein angiography'))->queryRow()) {
			$this->update('proc',array('unbooked'=>1),"id={$proc['id']}");
		}
	}

	public function down()
	{
	}
}
