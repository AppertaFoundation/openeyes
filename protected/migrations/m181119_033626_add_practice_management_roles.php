<?php

class m181119_033626_add_practice_management_roles extends CDbMigration
{
    const CREATE_PRACTICE_ROLE = 'Create Practice';
    const VIEW_PRACTICE_ROLE = 'View Practice';
    const CREATE_PRACTICE_TASK = 'TaskCreatePractice';
    const VIEW_PRACTICE_TASK = 'TaskViewPractice';
    public function safeUp()
    {
        $this->insert('authitem', array('name' => self::CREATE_PRACTICE_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::CREATE_PRACTICE_TASK, 'type' => 1));
        $this->insert('authitemchild',
            array('parent' => self::CREATE_PRACTICE_ROLE, 'child' => self::CREATE_PRACTICE_TASK));
        $this->insert('authitem', array('name' => self::VIEW_PRACTICE_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::VIEW_PRACTICE_TASK, 'type' => 1));
        $this->insert('authitemchild',
            array('parent' => self::VIEW_PRACTICE_ROLE, 'child' => self::VIEW_PRACTICE_TASK));
    }
    public function safeDown()
    {
        $this->delete('authitemchild',
            'parent = "' . self::CREATE_PRACTICE_ROLE . '" AND child = "' . self::CREATE_PRACTICE_TASK . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_PRACTICE_TASK . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_PRACTICE_ROLE . '"');
        $this->delete('authitemchild',
            'parent = "' . self::VIEW_PRACTICE_ROLE . '" AND child = "' . self::VIEW_PRACTICE_TASK . '"');
        $this->delete('authitem', 'name = "' . self::VIEW_PRACTICE_TASK . '"');
        $this->delete('authitem', 'name = "' . self::VIEW_PRACTICE_ROLE . '"');
    }
}