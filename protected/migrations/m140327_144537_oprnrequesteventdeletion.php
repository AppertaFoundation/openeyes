<?php

class m140327_144537_oprnrequesteventdeletion extends CDbMigration
{
	public function up()
	{
		$this->insert('authitem', array('name' => 'OprnRequestEventDeletion', 'type' => 0, 'bizrule' => 'canRequestEventDeletion'));
		$this->insert('authitemchild', array('parent' => 'TaskEditEvent', 'child' => 'OprnRequestEventDeletion'));
	}

	public function down()
	{
		$this->delete('authitemchild', 'parent = ? and child = ?', array('TaskEditEvent', 'OprnRequestEventDeletion'));
		$this->delete('authitem', 'name = ?', array('OprnRequestEventDeletion'));
	}
}
