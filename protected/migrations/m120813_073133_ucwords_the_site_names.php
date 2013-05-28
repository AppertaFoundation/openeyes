<?php

class m120813_073133_ucwords_the_site_names extends CDbMigration
{
	public function up()
	{
		foreach (Yii::app()->db->createCommand()->select("*")->from("site")->where("institution_id=:one",array(':one'=>1))->queryAll() as $site) {
			$this->update('site',array('name'=>str_replace(' At ',' at ',ucwords($site['name']))),"id={$site['id']}");
		}
	}

	public function down()
	{
	}
}
