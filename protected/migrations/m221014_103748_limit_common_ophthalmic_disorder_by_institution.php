<?php

class m221014_103748_limit_common_ophthalmic_disorder_by_institution extends CDbMigration
{
    public function safeUp()
    {
        $disorder_table_name = 'common_ophthalmic_disorder';
        $disorder_group_table_name = 'common_ophthalmic_disorder_group';

        $institutions = $this->dbConnection->createCommand('SELECT i.id FROM institution i JOIN institution_authentication ia ON ia.institution_id = i.id')->queryColumn();

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
                'common_ophthalmic_disorder_group_id' => $original_disorder_group['id'],
                'institution_id' => $first_institution
            ]);
        }

        // assign all disorders to first institution - removing any existing assignments first
        $this->delete($disorder_table_name . '_institution', 'institution_id = ' . $first_institution);

        $original_disorders = $this->dbConnection->createCommand('SELECT * FROM ' . $disorder_table_name)->queryAll();
        foreach ($original_disorders as $original_disorder) {
            $this->insert($disorder_table_name . '_institution', [
                'common_ophthalmic_disorder_id' => $original_disorder['id'],
                'institution_id' => $first_institution
            ]);
        }

        // loop through additional institutions
        foreach ($institutions as $institution) {
            // save new ids for remapping later
            $new_group_ids = [];

            // duplicate groups - retain id map to original group
            if (count($original_disorder_groups) > 0) {
                foreach ($original_disorder_groups as $original_disorder_group) {
                    $this->insert($disorder_group_table_name, [
                        'name' => $original_disorder_group['name'],
                        'display_order' => $original_disorder_group['display_order'],
                        'deleted' => $original_disorder_group['deleted']
                    ]);

                    $new_group_ids[$original_disorder_group['id']] = $this->dbConnection->getLastInsertID();

                    $this->insert($disorder_group_table_name . '_institution', [
                        'common_ophthalmic_disorder_group_id' => $new_group_ids[$original_disorder_group['id']],
                        'institution_id' => $institution
                    ]);
                }
            }

            // duplicate disorders - assigning to duplicated group if exists
            if (count($original_disorders) > 0) {
                foreach ($original_disorders as $original_disorder) {
                    $new_disorder = $original_disorder;
                    if ($original_disorder['group_id']) {
                        $new_disorder['group_id'] = $new_group_ids[$original_disorder['group_id']];
                    }

                    $this->insert($disorder_table_name, [
                        'disorder_id' => $new_disorder['disorder_id'],
                        'subspecialty_id' => $new_disorder['subspecialty_id'],
                        'display_order' => $new_disorder['display_order'],
                        'group_id' => $new_disorder['group_id'],
                        'alternate_disorder_id' => $new_disorder['alternate_disorder_id'],
                        'alternate_disorder_label' => $new_disorder['alternate_disorder_label'],
                        'finding_id' => $new_disorder['finding_id']
                    ]);

                    $this->insert($disorder_table_name . '_institution', [
                        'common_ophthalmic_disorder_id' => $this->dbConnection->getLastInsertID(),
                        'institution_id' => $institution
                    ]);
                }
            }
        }

        return true;
    }

    public function safeDown()
    {
        echo "m221014_103748_limit_common_ophthalmic_disorder_by_institution not support migration down.\n";
        return false;
    }
}
