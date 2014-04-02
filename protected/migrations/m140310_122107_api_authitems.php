<?php

class m140310_122107_api_authitems extends OEMigration
{
	public function up()
	{
		$this->insert('authitem', array('name' => 'OprnApi', 'type' => 0));
		$this->insert('authitem', array('name' => 'TaskApi', 'type' => 1));
		$this->insert('authitem', array('name' => 'API access', 'type' => 2));
		$this->insert('authitemchild', array('parent' => 'TaskApi', 'child' => 'OprnApi'));
		$this->insert('authitemchild', array('parent' => 'API access', 'child' => 'TaskApi'));

		$this->initialiseData($this->getMigrationPath());
	}

	public function down()
	{
		$this->delete('authitemchild', array('in', 'parent', array('TaskApi', 'API access')));
		$this->delete('authitem', array('in', 'name', array('OprnApi', 'TaskApi', 'API access')));
	}
}
