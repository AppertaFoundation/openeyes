<?php

class m171004_134833_audit_rbac extends OEMigration
{
    public function up()
    {
        $this->addRole('Audit');
        $this->addTask('TaskViewAudit');
        $this->addTaskToRole('TaskViewAudit', 'Audit');
    }

    public function down()
    {
        $this->removeTaskFromRole('TaskViewAudit', 'Audit');
        $this->removeRole('Audit');
        $this->removeTask('TaskViewAudit');
    }
}