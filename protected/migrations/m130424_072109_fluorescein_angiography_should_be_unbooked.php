<?php

class m130424_072109_fluorescein_angiography_should_be_unbooked extends CDbMigration
{
	public function up()
	{
		$db = $this->getDbConnection();
		if ($proc = $db->createCommand()->select("*")->from("proc")->where('term=:term',array(':term'=>'Fluorescein angiography'))->queryRow()) {
			$this->update('proc',array('unbooked'=>1),"id={$proc['id']}");
		}
	}

	public function down()
	{
	}
}
