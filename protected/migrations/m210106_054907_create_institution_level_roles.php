<?php

class m210106_054907_create_institution_level_roles extends CDbMigration
{
    private $authitems = [
        ['name' => 'OprnPatientMerge', 'type' => 0],
        ['name' => 'OprnPatientMergeRequest', 'type' => 0],
        ['name' => 'OprnWorklist', 'type' => 0],
        ['name' => 'OprnDeletePatient', 'type' => 0],
        ['name' => 'Institution Audit', 'type' => 2],
        ['name' => 'Delete Patient', 'type' => 2],
    ];

    public function safeUp()
    {
        $this->insertMultiple('authitem', $this->authitems);

        $this->insertMultiple('authitemchild', [
            ['parent' => 'Patient Merge', 'child' => 'OprnPatientMerge'],
            ['parent' => 'Patient Merge Request', 'child' => 'OprnPatientMergeRequest'],
            ['parent' => 'User', 'child' => 'OprnWorklist'],
            ['parent' => 'Institution Audit', 'child' => 'TaskViewAudit'],
            ['parent' => 'Delete Patient', 'child' => 'OprnDeletePatient'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('authitemchild', 'parent="Patient Merge"');
        $this->delete('authitemchild', 'parent="Patient Merge Request"');
        $this->delete('authitemchild', 'child="OprnWorklist"');
        $this->delete('authitemchild', 'parent="Institution Audit"');
        $this->delete('authitemchild', 'parent="Delete Patient"');

        $this->delete('authassignment', 'itemname="Institution Audit"');
        $this->delete('authassignment', 'itemname="Delete Patient"');
        foreach ($this->authitems as $authitem) {
            $this->delete('authitem', 'name = :name', array(':name' => $authitem['name']));
        }
    }
}
