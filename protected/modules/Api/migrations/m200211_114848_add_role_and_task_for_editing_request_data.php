<?php

class m200211_114848_add_role_and_task_for_editing_request_data extends OEMigration
{
    public function safeUp()
    {
        $this->addRole('Edit Payload Processor');
        $this->addTask('TaskEditAPIRequest');
        $this->addOperation('OprnEditRequestData');

        $this->addOperationToTask('OprnEditRequestData', 'TaskEditAPIRequest');
        $this->addTaskToRole('TaskEditAPIRequest', 'Edit Payload Processor');
    }

    public function safeDown()
    {
        $this->removeTaskFromRole('TaskEditAPIRequest', 'Edit Payload Processor');
        $this->removeOperationFromTask('OprnEditRequestData', 'TaskEditAPIRequest');

        $this->removeOperation('OprnEditRequestData');
        $this->removeTask('TaskEditAPIRequest');
        $this->removeRole('Edit Payload Processor');
    }

}
