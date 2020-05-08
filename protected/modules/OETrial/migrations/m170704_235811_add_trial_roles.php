<?php

class m170704_235811_add_trial_roles extends OEMigration
{
    const CREATE_TRIALS_ROLE = 'Create Trial';
    const VIEW_TRIALS_ROLE = 'Trial User';

    const CREATE_TRIALS_TASK = 'TaskCreateTrial';
    const VIEW_TRIALS_TASK = 'TaskViewTrial';

    public function safeUp()
    {

        $this->insert('authitem', array('name' => self::CREATE_TRIALS_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::VIEW_TRIALS_ROLE, 'type' => 2));

        $this->insert('authitem', array('name' => self::CREATE_TRIALS_TASK, 'type' => 1));
        $this->insert('authitem', array('name' => self::VIEW_TRIALS_TASK, 'type' => 1));

        $this->insert(
            'authitemchild',
            array('parent' => self::CREATE_TRIALS_ROLE, 'child' => self::CREATE_TRIALS_TASK)
        );

        $this->insert(
            'authitemchild',
            array('parent' => self::VIEW_TRIALS_ROLE, 'child' => self::VIEW_TRIALS_TASK)
        );

        $this->insert(
            'authitemchild',
            array('parent' => self::CREATE_TRIALS_ROLE, 'child' => self::VIEW_TRIALS_TASK)
        );
    }

    public function safeDown()
    {
        $this->delete('authassignment', 'itemname = "' . self::CREATE_TRIALS_ROLE . '"');

        $this->delete('authitemchild', 'parent = "' . self::CREATE_TRIALS_ROLE . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_TRIALS_TASK . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_TRIALS_ROLE . '"');

        $this->delete('authassignment', 'itemname = "' . self::VIEW_TRIALS_ROLE . '"');

        $this->delete('authitemchild', 'parent = "' . self::VIEW_TRIALS_ROLE . '"');
        $this->delete('authitem', 'name = "' . self::VIEW_TRIALS_TASK . '"');
        $this->delete('authitem', 'name = "' . self::VIEW_TRIALS_ROLE . '"');
    }
}
