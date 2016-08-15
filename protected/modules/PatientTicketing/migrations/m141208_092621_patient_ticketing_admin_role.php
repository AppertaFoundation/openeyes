<?php

class m141208_092621_patient_ticketing_admin_role extends CDbMigration
{
    public function up()
    {
        $this->insert('authitem', array('name' => 'Patient Tickets admin', 'type' => 2));
        $this->insert('authitem', array('name' => 'OprnUndoTicketLastStep', 'type' => 0));

        $this->insert('authitemchild', array(
            'parent' => 'Patient Tickets admin',
            'child' => 'OprnUndoTicketLastStep',
        ));
    }

    public function down()
    {
        $this->delete('authitemchild', "parent = 'Patient Tickets admin'");
        $this->delete('authassignment', "itemname = 'Patient Tickets admin'");
        $this->delete('authitem', "name = 'Patient Tickets admin'");
        $this->delete('authitem', "name = 'OprnUndoTicketLastStep'");
    }
}
