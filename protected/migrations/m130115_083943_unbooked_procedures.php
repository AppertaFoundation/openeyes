<?php

class m130115_083943_unbooked_procedures extends CDbMigration
{
	public function up()
	{
		$this->addColumn('proc','unbooked','tinyint(1) unsigned NOT NULL DEFAULT 0');

		Yii::app()->db->createCommand("update proc set unbooked = 1 where snomed_code in ('231705009','35137007','371345007','172485001','287588003')")->query();
		Yii::app()->db->createCommand("update proc set unbooked = 1 where snomed_code in ('33810006','120113002','74410004','231701000','66205002','231755001','231779004','120103005','274937009','285435007','4143006','410565007','410563000')")->query();
	}

	public function down()
	{
		$this->dropColumn('proc','unbooked');
	}
}
