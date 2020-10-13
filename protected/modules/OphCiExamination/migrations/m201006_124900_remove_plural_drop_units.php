<?php

/**
 * Class m180510093824_prescription_events_import
 *
 * This is not the right place for this migration, but we must ensure
 * that it runs right before the examination import.
 */

class m201006_124900_remove_plural_drop_units extends OEMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE event_medication_use
            SET dose_unit_term = 'drop'
            WHERE dose_unit_term like 'drop%(s)%'");
    }

    public function safeDown()
    {
        echo "down not supported for this migration";
    }
}
