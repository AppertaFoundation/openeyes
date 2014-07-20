<?php

class m140521_150455_patient_history_rbac extends CDbMigration
{
	public function up()
	{
		$this->insert('authitem',array('name'=>'OprnEditSocialHistory'));
		$this->insert('authitemchild',array('parent'=>'TaskEditPatientData','child'=>'OprnEditSocialHistory'));
	}

	public function down()
	{

	}
}