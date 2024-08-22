<?php

class m221014_141148_limit_common_systemic_disorder_by_institution extends CDbMigration
{
    public function safeUp()
    {
        $disorder_table_name = 'common_systemic_disorder';
        $disorder_group_table_name = 'common_systemic_disorder_group';

        $institutions = $this->dbConnection->createCommand('SELECT DISTINCT i.id FROM institution i JOIN institution_authentication ia ON ia.institution_id = i.id WHERE ia.active = 1')->queryColumn();

        if (count($institutions) === 0) {
            // No institutions at all, simply return here to prevent an error during the migration process
            return;
        }

        // pluck first institution
        $first_institution = array_shift($institutions);

        // assign all groups to first institution - removing any existing assignments first
        $this->delete($disorder_group_table_name . '_institution', 'institution_id = ' . $first_institution);

        $original_disorder_groups = $this->dbConnection->createCommand('SELECT * FROM ' . $disorder_group_table_name)->queryAll();
        foreach ($original_disorder_groups as $original_disorder_group) {
            $this->insert($disorder_group_table_name . '_institution', [
                'common_systemic_disorder_group_id' => $original_disorder_group['id'],
                'institution_id' => $first_institution
            ]);
        }

        // assign all disorders to first institution - removing any existing assignments first
        $this->delete($disorder_table_name . '_institution', 'institution_id = ' . $first_institution);

        $original_disorders = $this->dbConnection->createCommand('SELECT * FROM ' . $disorder_table_name)->queryAll();
        foreach ($original_disorders as $original_disorder) {
            $this->insert($disorder_table_name . '_institution', [
                'common_systemic_disorder_id' => $original_disorder['id'],
                'institution_id' => $first_institution
            ]);
        }

        // loop through additional institutions
        foreach ($institutions as $institution) {
            // duplicate groups - retain id map to original group
            $group_id_mapping = [];

            if (count($original_disorder_groups) > 0) {
                foreach ($original_disorder_groups as $original_disorder_group) {
                    $this->insert($disorder_group_table_name, [
                        'name' => $original_disorder_group['name'],
                        'display_order' => $original_disorder_group['display_order']
                    ]);

                    $group_id_mapping[$original_disorder_group['id']] = $this->dbConnection->getLastInsertID();

                    $this->insert($disorder_group_table_name . '_institution', [
                        'common_systemic_disorder_group_id' => $group_id_mapping[$original_disorder_group['id']],
                        'institution_id' => $institution
                    ]);
                }
            }

            // duplicate disorders - assigning to duplicated group if exists
            if (count($original_disorders) > 0) {
                foreach ($original_disorders as $original_disorder) {
                    $this->insert($disorder_table_name, [
                        'disorder_id' => $original_disorder['disorder_id'],
                        'display_order' => $original_disorder['display_order'],
                        'group_id' => ($original_disorder['group_id'] && $group_id_mapping[$original_disorder['group_id']]) ? $group_id_mapping[$original_disorder['group_id']] : $original_disorder['group_id'],
                    ]);

                    $this->insert($disorder_table_name . '_institution', [
                        'common_systemic_disorder_id' => $this->dbConnection->getLastInsertID(),
                        'institution_id' => $institution
                    ]);
                }
            }
        }

        return true;
    }

    public function safeDown()
    {
        echo "m221014_141148_limit_common_systemic_disorder_by_institution not support migration down.\n";
        return false;
    }
}
