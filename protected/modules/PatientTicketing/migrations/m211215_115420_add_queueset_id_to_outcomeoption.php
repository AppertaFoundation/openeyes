<?php

class m211215_115420_add_queueset_id_to_outcomeoption extends OEMigration
{
    private $table = 'patientticketing_ticketassignoutcomeoption';

    public function safeUp()
    {
        if (!$this->isColumnExist($this->table, 'queueset_id')) {
            $this->addColumn($this->table, 'queueset_id', 'INT(11) AFTER followup');
            $this->addColumn("{$this->table}_version", 'queueset_id', 'INT(11) AFTER followup');
            $this->addForeignKey('fk_outcomeoption_queueset_id', $this->table, 'queueset_id', 'patientticketing_queueset', 'id');

            $this->migrateOptions();
        }
    }

    public function safeDown()
    {
        echo "This migration does not support down migration as we do not know if the column was already there by the time this migration ran.\n";
        return false;
    }

    private function migrateOptions()
    {
        /**  All queue sets needs to have all the existing options */

        $outcome_options = $this->dbConnection->createCommand("SELECT * FROM {$this->table}")->queryAll();
        $queue_set_ids = $this->dbConnection->createCommand('SELECT id FROM patientticketing_queueset')->queryColumn();

        // Get the first queue set id and update the existing outcome options
        if (count($queue_set_ids) >= 1) {
            $this->execute("UPDATE {$this->table} SET queueset_id=:queueset_id", [':queueset_id' => $queue_set_ids[0]]);
        }

        // get rid of the first queue set id
        array_shift($queue_set_ids);

        // if there are more queue sets, we create the same outcome options for them
        $extra_outcome_options = [];
        foreach ($queue_set_ids as $queue_set_id) {
            foreach ($outcome_options as $outcome_option) {
                unset($outcome_option['id']);
                $outcome_option['queueset_id'] = $queue_set_id;
                $extra_outcome_options[] = $outcome_option;
            }
        }

        if ($extra_outcome_options) {
            $this->insertMultiple($this->table, $extra_outcome_options);
        }
    }
}
