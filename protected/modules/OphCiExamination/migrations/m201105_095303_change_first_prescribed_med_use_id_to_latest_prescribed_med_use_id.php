<?php

class m201105_095303_change_first_prescribed_med_use_id_to_latest_prescribed_med_use_id extends OEMigration
{

    public function safeUp()
    {
        $this->renameOEColumn('event_medication_use', 'first_prescribed_med_use_id', 'latest_prescribed_med_use_id', true);
    }

    public function safeDown()
    {
        $this->renameOEColumn('event_medication_use', 'latest_prescribed_med_use_id', 'first_prescribed_med_use_id', true);
    }
}
