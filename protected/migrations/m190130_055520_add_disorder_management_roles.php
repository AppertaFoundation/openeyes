<?php

class m190130_055520_add_disorder_management_roles extends CDbMigration
{

    const CREATE_DISORDER_ROLE = 'Create Disorder';
    const VIEW_DISORDER_ROLE = 'View Disorder';
    const CREATE_DISORDER_TASK = 'TaskCreateDisorder';
    const VIEW_DISORDER_TASK = 'TaskViewDisorder';

	public function safeUp()
	{
        $this->insert('authitem', array('name' => self::CREATE_DISORDER_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::CREATE_DISORDER_TASK, 'type' => 1));
        $this->insert('authitemchild',
            array('parent' => self::CREATE_DISORDER_ROLE, 'child' => self::CREATE_DISORDER_TASK));
	}

	public function safeDown()
	{
        $this->delete('authitemchild',
            'parent = "' . self::CREATE_DISORDER_ROLE . '" AND child = "' . self::CREATE_DISORDER_TASK . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_DISORDER_TASK . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_DISORDER_ROLE . '"');
	}

}