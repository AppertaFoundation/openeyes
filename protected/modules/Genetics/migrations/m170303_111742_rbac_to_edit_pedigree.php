<?php

class m170303_111742_rbac_to_edit_pedigree extends OEMigration
{
    public function up()
    {
        $this->addTaskToRole("TaskEditPedigreeData","Genetics Admin");
    }

    public function down()
    {
        echo "m170303_111742_rbac_to_edit_pedigree does not support migration down.\n";
        return false;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}