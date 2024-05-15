<?php

class m210121_030317_add_found_opnotes_to_past_surgery_element extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('et_ophciexamination_pastsurgery', 'found_previous_op_notes', 'bool', true);
    }

    public function down()
    {
        $this->dropOEColumn('et_ophciexamination_pastsurgery', 'found_previous_op_notes', true);
    }
}
