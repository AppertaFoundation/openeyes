<?php

class m131121_114638_prescription_authitems extends CDbMigration
{
    private $authitems = array(
        array('name' => 'OprnCreatePrescription', 'type' => 0, 'bizrule' => 'canCreateEvent'),
        array('name' => 'OprnEditPrescription', 'type' => 0, 'bizrule' => 'canEditEvent'),
        array('name' => 'OprnPrintPrescription', 'type' => 0),
    );

    private $parents = array(
        'OprnCreatePrescription' => 'TaskPrescribe',
        'OprnEditPrescription' => 'TaskPrescribe',
        'OprnPrintPrescription' => 'TaskPrescribe',
    );

    public function up()
    {
        foreach ($this->authitems as $authitem) {
            $this->insert('authitem', $authitem);
        }

        foreach ($this->parents as $child => $parent) {
            $this->insert('authitemchild', array('parent' => $parent, 'child' => $child));
        }
    }

    public function down()
    {
        foreach ($this->parents as $child => $parent) {
            $this->delete('authitemchild', 'parent = ? and child = ?', array($parent, $child));
        }

        foreach ($this->authitems as $authitem) {
            $this->delete('authitem', 'name = ?', array($authitem['name']));
        }
    }
}
