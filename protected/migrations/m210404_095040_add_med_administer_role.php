<?php

class m210404_095040_add_med_administer_role extends OEMigration
{
    private const MED_ADMINISTER_ROLE = 'Med Administer';
    private const PRESCRIBE_ROLE = 'Prescribe';
    private const ROLE = 'role';
    private const TASK = 'task';
    private const OPRN = 'operation';
    private const AUTHITEM_TABLE = 'authitem';
    private const AUTHITEM_CHILD_TABLE = 'authitemchild';
    private const AUTHITEM_TYPE_TABLE = 'authitem_type';
    private const DA_PRESCRIBER_TASK = 'TaskDrugAdminPrecriber';
    private const DA_MEDADMIN_TASK = 'TaskDrugAdminMedAdmin';
    private const DA_CREATE_OPRN = 'OprnCreateDA';
    private const DA_EDIT_OPRN = 'OprnEditDA';
    private const DA_DELETE_OPRN = 'OprnDeleteDA';
    private const DA_ADD_MEDS = 'OprnAddMeds';
    private const DA_ADD_PRESETS = 'OprnAddPresets';
    private const AUTH_ASSIGNMENT_TABLE = 'authassignment';
    public function safeUp()
    {
        $role_id =  $this->dbConnection->createCommand()
            ->select('id')
            ->from(self::AUTHITEM_TYPE_TABLE)
            ->where('LOWER(name) = :role', array(':role' => self::ROLE))
            ->queryScalar();
        $task_id =  $this->dbConnection->createCommand()
            ->select('id')
            ->from(self::AUTHITEM_TYPE_TABLE)
            ->where('LOWER(name) = :task', array(':task' => self::TASK))
            ->queryScalar();
        $oprn_id =  $this->dbConnection->createCommand()
            ->select('id')
            ->from(self::AUTHITEM_TYPE_TABLE)
            ->where('LOWER(name) = :task', array(':task' => self::OPRN))
            ->queryScalar();
        // insert role
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::MED_ADMINISTER_ROLE, 'type' => $role_id));
        // insert tasks
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_PRESCRIBER_TASK, 'type' => $task_id));
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_MEDADMIN_TASK, 'type' => $task_id));
        // insert oprns
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_CREATE_OPRN, 'type' => $oprn_id, 'bizrule' => 'canCreateEvent'));
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_EDIT_OPRN, 'type' => $oprn_id, 'bizrule' => 'canEditEvent'));
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_DELETE_OPRN, 'type' => $oprn_id, 'bizrule' => 'canDeleteEvent'));
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_ADD_MEDS, 'type' => $oprn_id));
        $this->insert(self::AUTHITEM_TABLE, array('name' => self::DA_ADD_PRESETS, 'type' => $oprn_id));

        // insert task oprn assign for prescribe
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_PRESCRIBER_TASK, 'child' => self::DA_CREATE_OPRN));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_PRESCRIBER_TASK, 'child' => self::DA_EDIT_OPRN));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_PRESCRIBER_TASK, 'child' => self::DA_ADD_MEDS));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_PRESCRIBER_TASK, 'child' => self::DA_ADD_PRESETS));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_PRESCRIBER_TASK, 'child' => self::DA_DELETE_OPRN));
        // insert task oprn assign for med admin
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_MEDADMIN_TASK, 'child' => self::DA_CREATE_OPRN));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_MEDADMIN_TASK, 'child' => self::DA_EDIT_OPRN));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_MEDADMIN_TASK, 'child' => self::DA_ADD_MEDS));
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::DA_MEDADMIN_TASK, 'child' => self::DA_ADD_PRESETS));
        // insert role task assign for prescribe
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::PRESCRIBE_ROLE, 'child' => self::DA_PRESCRIBER_TASK));
        // insert role task assign for med admin
        $this->insert(self::AUTHITEM_CHILD_TABLE, array('parent' => self::MED_ADMINISTER_ROLE, 'child' => self::DA_MEDADMIN_TASK));
    }

    public function safeDown()
    {
        // delete med administer auth assignment
        $this->delete(self::AUTH_ASSIGNMENT_TABLE, 'itemname = ?', array(self::MED_ADMINISTER_ROLE));

        // delete role task assignment
        $this->delete(self::AUTHITEM_CHILD_TABLE, 'parent = ?', array(self::MED_ADMINISTER_ROLE));
        $this->delete(self::AUTHITEM_CHILD_TABLE, 'parent = ? and child = ?', array(self::PRESCRIBE_ROLE, self::DA_PRESCRIBER_TASK));

        // delete task oprn assignment
        $this->delete(self::AUTHITEM_CHILD_TABLE, 'parent = ?', array(self::DA_PRESCRIBER_TASK));
        $this->delete(self::AUTHITEM_CHILD_TABLE, 'parent = ?', array(self::DA_MEDADMIN_TASK));

        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::MED_ADMINISTER_ROLE));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_PRESCRIBER_TASK));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_MEDADMIN_TASK));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_CREATE_OPRN));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_EDIT_OPRN));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_DELETE_OPRN));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_ADD_MEDS));
        $this->delete(self::AUTHITEM_TABLE, 'name = ?', array(self::DA_ADD_PRESETS));
    }
}
