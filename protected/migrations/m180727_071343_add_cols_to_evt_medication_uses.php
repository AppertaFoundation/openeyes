<?php

class m180727_071343_add_cols_to_evt_medication_uses extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('event_medication_use', 'continue', 'BOOLEAN NOT NULL DEFAULT 0', true);
        $this->addOEColumn('event_medication_use', 'prescribe', 'BOOLEAN NOT NULL DEFAULT 0', true);
    }

    public function down()
    {
        $this->dropOEColumn('event_medication_use', 'continue', true);
        $this->dropOEColumn('event_medication_use', 'prescribe', true);
    }
}
