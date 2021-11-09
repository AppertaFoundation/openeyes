<?php

class m211107_234809_add_rr_and_other_fields_to_observations extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophciexamination_observation_entry', 'rr', 'int', true);
        $this->addOEColumn('ophciexamination_observation_entry', 'other', 'varchar(255)', true);
	}

    public function down()
    {
        $this->dropOEColumn('ophciexamination_observation_entry', 'rr', true);
        $this->dropOEColumn('ophciexamination_observation_entry', 'other', true);
    }
}
