<?php

class m140620_140021_pseudopemphigoid_spelling extends CDbMigration
{
	public function up()
	{
		Yii::app()->db->createCommand('update medication_stop_reason set name =\'Pseudopemphigoid\' where name=\'Pseudophembhygoid\'')->query();
	}

	public function down()
	{
		Yii::app()->db->createCommand('update medication_stop_reason set name =\'Pseudophembhygoid\' where name=\'Pseudopemphigoid\'')->query();
	}
}