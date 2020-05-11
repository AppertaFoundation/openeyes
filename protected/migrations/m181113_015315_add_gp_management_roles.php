<?php

class m181113_015315_add_gp_management_roles extends CDbMigration
{
    const CREATE_GPS_ROLE = 'Create GP';
    const VIEW_GPS_ROLE = 'View GP';
    const CREATE_GPS_TASK = 'TaskCreateGp';
    const VIEW_GPS_TASK = 'TaskViewGp';
    public function safeUp()
    {
        $this->insert('authitem', array('name' => self::CREATE_GPS_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::CREATE_GPS_TASK, 'type' => 1));
        $this->insert(
            'authitemchild',
            array('parent' => self::CREATE_GPS_ROLE, 'child' => self::CREATE_GPS_TASK)
        );
        $this->insert('authitem', array('name' => self::VIEW_GPS_ROLE, 'type' => 2));
        $this->insert('authitem', array('name' => self::VIEW_GPS_TASK, 'type' => 1));
        $this->insert(
            'authitemchild',
            array('parent' => self::VIEW_GPS_ROLE, 'child' => self::VIEW_GPS_TASK)
        );
    }
    public function safeDown()
    {
        $this->delete(
            'authitemchild',
            'parent = "' . self::CREATE_GPS_ROLE . '" AND child = "' . self::CREATE_GPS_TASK . '"'
        );
        $this->delete('authitem', 'name = "' . self::CREATE_GPS_TASK . '"');
        $this->delete('authitem', 'name = "' . self::CREATE_GPS_ROLE . '"');
        $this->delete(
            'authitemchild',
            'parent = "' . self::VIEW_GPS_ROLE . '" AND child = "' . self::VIEW_GPS_TASK . '"'
        );
        $this->delete('authitem', 'name = "' . self::VIEW_GPS_TASK . '"');
        $this->delete('authitem', 'name = "' . self::VIEW_GPS_ROLE . '"');
    }
}
