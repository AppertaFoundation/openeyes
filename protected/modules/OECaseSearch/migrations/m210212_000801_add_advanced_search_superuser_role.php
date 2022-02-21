<?php

class m210212_000801_add_advanced_search_superuser_role extends CDbMigration
{
    private const ADVANCED_SEARCH_SUPER_ROLE = 'Advanced Search Superuser';
    private const ADVANCED_SEARCH_STANDARD_TASK = 'TaskCaseSearch';
    private const ADVANCED_SEARCH_SUPER_TASK = 'TaskCaseSearchSuperUser';
    public function safeUp()
    {
        $this->insert('authitem', array('name' => self::ADVANCED_SEARCH_SUPER_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::ADVANCED_SEARCH_SUPER_TASK, 'type' => 1));
        $this->insertMultiple(
            'authitemchild',
            array(
                array('parent' => self::ADVANCED_SEARCH_SUPER_ROLE, 'child' => self::ADVANCED_SEARCH_SUPER_TASK),
                array('parent' => self::ADVANCED_SEARCH_SUPER_ROLE, 'child' => self::ADVANCED_SEARCH_STANDARD_TASK)
            )
        );
    }

    public function safeDown()
    {
        $this->delete(
            'authitemchild',
            'parent = "' . self::ADVANCED_SEARCH_SUPER_ROLE . '" AND child = "' . self::ADVANCED_SEARCH_SUPER_TASK . '"'
        );
        $this->delete(
            'authitemchild',
            'parent = "' . self::ADVANCED_SEARCH_SUPER_ROLE . '" AND child = "' . self::ADVANCED_SEARCH_STANDARD_TASK . '"'
        );
        $this->delete('authitem', 'name = "' . self::ADVANCED_SEARCH_SUPER_TASK . '"');
        $this->delete('authitem', 'name = "' . self::ADVANCED_SEARCH_SUPER_ROLE . '"');
    }
}
