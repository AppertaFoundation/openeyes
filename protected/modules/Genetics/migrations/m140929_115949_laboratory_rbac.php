<?php

class m140929_115949_laboratory_rbac extends OEMigration
{
    public function up()
    {
        $this->addRole('Genetics Laboratory Technician');
        $this->addTask('TaskEditGeneticsWithdrawals');
        $this->addOperation('OprnEditGeneticsWithdrawals');

        $this->addOperationToTask('OprnEditGeneticsWithdrawals', 'TaskEditGeneticsWithdrawals');

        $this->addTaskToRole('TaskEditGeneticsWithdrawals', 'Genetics Admin');
        $this->addTaskToRole('TaskCreateDnaExtraction', 'Genetics Laboratory Technician');
        $this->addTaskToRole('TaskEditDNAExtraction', 'Genetics Laboratory Technician');
    }

    public function down()
    {
        $this->removeTaskFromRole('TaskEditDnaExtraction', 'Genetics Laboratory Technician');
        $this->removeTaskFromRole('TaskCreateDnaExtraction', 'Genetics Laboratory Technician');
        $this->removeTaskFromRole('TaskEditGeneticsWithdrawals', 'Genetics Admin');

        $this->removeOperationFromTask('OprnEditGeneticsWithdrawals', 'TaskEditGeneticsWithdrawals');

        $this->removeOperation('OprnEditGeneticsWithdrawals');
        $this->removeTask('TaskEditGeneticsWithdrawals');
        $this->removeRole('Genetics Laboratory Technician');
    }
}
