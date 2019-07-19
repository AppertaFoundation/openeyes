<?php

class m190508_154056_add_step_completed_flag_to_workflow extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_event_elementset_assignment', 'step_completed', 'TINYINT(1) DEFAULT 1 AFTER event_id');
        $this->addColumn('ophciexamination_event_elementset_assignment_version', 'step_completed', 'TINYINT(1) DEFAULT 1 AFTER event_id');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_event_elementset_assignment', 'step_completed');
        $this->dropColumn('ophciexamination_event_elementset_assignment_version', 'step_completed');
    }
}
