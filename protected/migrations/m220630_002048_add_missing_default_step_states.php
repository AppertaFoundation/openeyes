<?php

class m220630_002048_add_missing_default_step_states extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'pathway_step_type',
            array('default_state' => 0)
        );
    }

    public function safeDown()
    {
        echo "m220630_002048_add_missing_default_step_states does not support migration down.\n";
        return false;
    }
}
