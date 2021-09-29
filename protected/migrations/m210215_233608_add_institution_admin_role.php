<?php

class m210215_233608_add_institution_admin_role extends CDbMigration
{
    public function safeUp()
    {
        $this->insertMultiple(
            'authitem',
            [['name' => 'Institution Admin', 'type' => 2], ['name' => 'OprnInstitutionAdmin', 'type' => 0]]
        );

        $this->insertMultiple(
            'authitemchild',
            [
                ['parent' => 'Institution Admin', 'child' => 'OprnInstitutionAdmin'],
                ['parent' => 'admin', 'child' => 'OprnInstitutionAdmin']
            ]
        );
    }

    public function safeDown()
    {
        $this->delete(
            'authitemchild',
            'parent = "Institution Admin" OR (parent = "admin" AND child = "OprnInstitutionAdmin")'
        );
        $this->delete('authitem', 'name IN ("Institution Admin", "OprnInstitutionAdmin")');
    }
}
