<?php

class m170306_145403_rbac_role_task_swap extends OEMigration
{
    public function up()
    {
        $this->removeTaskFromRole("Genetics Admin", "TaskEditGeneticStudy");
        $this->addTaskToRole("TaskEditGeneticStudy", "Genetics Admin");
    }

    public function down()
    {
        $this->removeTaskFromRole("TaskEditGeneticStudy", "Genetics Admin");
        $this->addTaskToRole("Genetics Admin", "TaskEditGeneticStudy");
    }
}
