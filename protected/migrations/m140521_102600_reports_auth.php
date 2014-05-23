<?php

class m140521_102600_reports_auth extends CDbMigration
{
	private $authitems = array(
		array('name' => 'OprnGenerateReport', 'type' => 0),
		array('name' => 'TaskGenerateReport', 'type' => 1),
		array('name' => 'Report', 'type' => 2),
	);

	private $parents = array(
		'OprnGenerateReport' => 'TaskGenerateReport',
		'TaskGenerateReport' => 'Report',
	);

	public function up()
	{
		foreach ($this->authitems as $authitem) {
			$this->insert('authitem', $authitem);
		}

		foreach ($this->parents as $child => $parent) {
			if ($this->dbConnection->createCommand()->select()->from('authitem')->where('name = ?')->queryScalar(array($parent))) {
				$this->insert('authitemchild', array('parent' => $parent, 'child' => $child));
			}
		}
	}

	public function down()
	{
		foreach ($this->parents as $child => $parent) {
			$this->delete('authitemchild', 'parent = ? and child = ?', array($parent, $child));
		}

		foreach ($this->authitems as $authitem) {
			$this->delete('authitem', 'name = ?', array($authitem['name']));
		}
	}
}
