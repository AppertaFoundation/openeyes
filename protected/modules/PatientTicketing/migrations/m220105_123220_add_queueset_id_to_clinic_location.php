<?php

class m220105_123220_add_queueset_id_to_clinic_location extends OEMigration
{
    private $table = 'patientticketing_clinic_location';

    public function safeUp()
    {
        if (!$this->isColumnExist($this->table, 'queueset_id')) {
            $this->addOEColumn($this->table, 'queueset_id', 'INT(11) DEFAULT NULL AFTER name', true);
            $this->addForeignKey('fk_cliniclocation_queueset_id', $this->table, 'queueset_id', 'patientticketing_queueset', 'id');

            $this->migrateLocations();
        }
    }

    public function safeDown()
    {
        echo "This migration does not support down migration as we do not know if the column was already there by the time this migration ran.\n";
        return false;
    }

    private function migrateLocations()
    {
        /**  All queue sets needs to have all the existing locations */

        $locations = $this->dbConnection->createCommand("SELECT * FROM {$this->table}")->queryAll();
        $queue_set_ids = $this->dbConnection->createCommand('SELECT id FROM patientticketing_queueset')->queryColumn();

        // Get the first queue set id and update the existing locations
        if (count($queue_set_ids) >= 1) {
            $this->execute("UPDATE {$this->table} SET queueset_id=:queueset_id", [':queueset_id' => $queue_set_ids[0]]);
        }

        // get rid of the first queue set id
        array_shift($queue_set_ids);

        // if there are more queue sets, we create the same locations for them
        $extra_locations = [];
        foreach ($queue_set_ids as $queue_set_id) {
            foreach ($locations as $location) {
                unset($location['id']);
                $location['queueset_id'] = $queue_set_id;
                $extra_locations[] = $location;
            }
        }

        if ($extra_locations) {
            $this->insertMultiple($this->table, $extra_locations);
        }
    }
}
