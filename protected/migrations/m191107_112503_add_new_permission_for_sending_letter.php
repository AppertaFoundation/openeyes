<?php

class m191107_112503_add_new_permission_for_sending_letter extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'OprnOperationBookingLetterSend', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'TaskManageOperationBookings', 'child' => 'OprnOperationBookingLetterSend'));
        $this->insert('authitemchild', array('parent' => 'Schedule operation', 'child' => 'OprnOperationBookingLetterSend'));
    }

    public function down()
    {
        $this->delete('authitemchild', 'parent = ? and child = ?', array('TaskManageOperationBookings', 'OprnOperationBookingLetterSend'));
        $this->delete('authitemchild', 'parent = ? and child = ?', array('Schedule operation', 'OprnOperationBookingLetterSend'));
        $this->delete('authitem', 'name = ?', array('OprnOperationBookingLetterSend'));
    }

}
