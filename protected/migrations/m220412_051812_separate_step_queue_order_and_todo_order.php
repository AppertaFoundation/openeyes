<?php

class m220412_051812_separate_step_queue_order_and_todo_order extends OEMigration
{
    public function safeUp()
    {
        $this->renameOEColumn('pathway_step', 'order', 'queue_order', true);
        $this->renameOEColumn('pathway_type_step', 'order', 'queue_order', true);
        $this->alterOEColumn('pathway_step', 'queue_order', 'int', true);
        $this->addOEColumn('pathway_step', 'todo_order', 'int', true);

        $this->execute('UPDATE pathway_step SET todo_order = queue_order');
        $this->execute('UPDATE pathway_step_version SET todo_order = queue_order');

        $this->alterOEColumn('pathway_step', 'todo_order', 'int NOT NULL', true);
    }

    public function down()
    {
        $this->renameOEColumn('pathway_step', 'queue_order', 'order', true);
        $this->renameOEColumn('pathway_type_step', 'queue_order', 'order', true);
        $this->alterOEColumn('pathway_step', 'order', 'int NOT NULL', true);
        $this->dropOEColumn('pathway_step', 'todo_order', true);
    }
}
