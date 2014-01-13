<?php

class m140113_101202_fix_table_collation extends CDbMigration
{
	public function up()
	{
		foreach (Yii::app()->db->getSchema()->getTables() as $table) {
			$create = Yii::app()->db->createCommand("show create table $table->name")->queryRow();
			if (!preg_match('/DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci/',$create['Create Table'])) {
				Yii::app()->db->createCommand("alter table $table->name convert to character set utf8 collate utf8_unicode_ci;")->query();
			}
		}
	}

	public function down()
	{
	}
}
