<?php

class m170811_054148_advanced_search_rbac extends OEMigration
{
    const ADVANCED_SEARCH_ROLE = 'Advanced Search';
    const ADVANCED_SEARCH_TASK = 'TaskCaseSearch';

    public function safeUp()
    {
        $this->insert('authitem', array('name' => self::ADVANCED_SEARCH_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::ADVANCED_SEARCH_TASK, 'type' => 1));
        $this->insert(
            'authitemchild',
            array('parent' => self::ADVANCED_SEARCH_ROLE, 'child' => self::ADVANCED_SEARCH_TASK)
        );
    }

    public function safeDown()
    {
        $this->delete(
            'authitemchild',
            'parent = "' . self::ADVANCED_SEARCH_ROLE . '" AND child = "' . self::ADVANCED_SEARCH_TASK . '"'
        );
        $this->delete('authitem', 'name = "' . self::ADVANCED_SEARCH_TASK . '"');
        $this->delete('authitem', 'name = "' . self::ADVANCED_SEARCH_ROLE . '"');
    }
}
