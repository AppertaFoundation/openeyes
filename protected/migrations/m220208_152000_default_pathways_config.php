<?php

class m220208_152000_default_pathways_config extends OEMigration
{
    public function safeUp()
    {

        $institution_id = $this->dbConnection->createCommand("SELECT id FROM institution WHERE remote_id = :code")->queryScalar(array(':code' => Yii::app()->params['institution_code']));

        # If the instituition_id column has not already been added by a previous migration, add it now
        if (!$this->verifyColumnExists('pathway_type', 'institution_id')) {
            $this->addOEColumn('pathway_type', 'institution_id', 'int DEFAULT ' . $institution_id, true);
        }

        # Find all current workflow rules
        $workflow_rules = $this->dbConnection->createCommand("  SELECT 
                                                                    r.workflow_id,
                                                                    CASE 
                                                                    	WHEN r.subspecialty_id IS NULL THEN 
                                                                    			(SELECT s.id 
                                                                                FROM subspecialty s 
                                                                                    INNER JOIN service_subspecialty_assignment ssa ON ssa.subspecialty_id = s.id
                                                                                    INNER JOIN firm f ON ssa.id = f.service_subspecialty_assignment_id
                                                                                WHERE f.id = r.firm_id)
                                                                        ELSE r.subspecialty_id
                                                                    END AS subspecialty_id,
                                                                    CASE 
                                                                    	WHEN r.subspecialty_id IS NULL THEN 
                                                                    			(SELECT s.short_name 
                                                                                FROM subspecialty s 
                                                                                    INNER JOIN service_subspecialty_assignment ssa ON ssa.subspecialty_id = s.id
                                                                                    INNER JOIN firm f ON ssa.id = f.service_subspecialty_assignment_id
                                                                                WHERE f.id = r.firm_id)
                                                                        ELSE s.short_name
                                                                    END AS subspecialty_name, 
                                                                    r.firm_id,
                                                                    r.episode_status_id,
                                                                    w.`name` AS 'workflow_name',
                                                                    w.institution_id,
                                                                    f.`name` AS 'firm_name',
                                                                    e.`name` AS 'status_name'
                                                                FROM ophciexamination_workflow_rule r 
                                                                        INNER JOIN ophciexamination_workflow w ON r.workflow_id = w.id
                                                                        LEFT JOIN subspecialty s ON r.subspecialty_id = s.id
                                                                        LEFT JOIN firm f ON r.firm_id = f.id
                                                                        LEFT JOIN episode_status e ON r.episode_status_id = e.id ")->queryAll(true);

        # Due to the need to insert parameters into the JSON string portion (default_state_data), there isn't a simple way
        # to acheive this in pure SQL. So resorting to using foreach loops. However, this is gauranteed to have very few records,
        # so should take no more than 3 seconds to run even on a very large dataset

        foreach ($workflow_rules as $rule) {
            # build the path name nased on the workflow rule parts. Try to eliminate any repetive names
            $path_name = $rule['workflow_name'];

            if (!empty($rule['subspecialty_name']) && !str_contains(strtolower($rule['workflow_name']), strtolower($rule['subspecialty_name']))) {
                $path_name .= " - " . $rule['subspecialty_name'];
            }

            if (!empty($rule['firm_name']) && !str_contains(strtolower($rule['workflow_name']), strtolower($rule['firm_name']))) {
                $path_name .= " - " . $rule['firm_name'];
            }

            if (!empty($rule['status_name']) && !str_contains(strtolower($rule['workflow_name']), strtolower($rule['status_name']))) {
                $path_name .= " - " . $rule['status_name'];
            }

            $this->insert(
                'pathway_type',
                array(
                'name' => $path_name,
                'institution_id' => $rule['institution_id'],
                'is_preset' => 1,
                )
            );

            $pathway_type_id = $this->dbConnection->getLastInsertID();

            echo("Adding pathway for: " . $path_name);


            # Find all steps for the workflow and add to the pathway
            $workflow_steps = $this->dbConnection->createCommand("  SELECT
                                                                es.id AS 'step_id',
                                                                es.`name` AS 'step_name',
                                                                es.position
                                                                FROM ophciexamination_element_set es
                                                                WHERE es.workflow_id = :workflow_id
                                                                    AND es.is_active = 1
                                                                ORDER BY es.position")->queryAll(true, [ ':workflow_id' => $rule['workflow_id']]);

            foreach ($workflow_steps as $step) {
                $this->insert('pathway_type_step', [
                        'pathway_type_id' => $pathway_type_id,
                        'step_type_id' => 7,
                        'order' => $step['position'],
                        'short_name' => substr($step['step_name'], 0, 19),
                        'long_name' => $step['step_name'],
                        'default_state_data' => '{
                            "action_type":"new_event",
                            "event_label":"Manage Event",
                            "event_type":"OphCiExamination",
                            "default_element_list":[],
                            "workflow_id":"' . $rule['workflow_id'] . '",
                            "long_name":"' . $step['step_name'] . '",
                            "short_name":"' . substr($step['step_name'], 0, 19) . '",
                            "subspecialty_id":"' . $rule['subspecialty_id'] . '",
                            "firm_id":"' . $rule['firm_id'] . '",
                            "workflow_step_id":"' . $step['step_id'] . '"
                        }']);
            }

            # add a discharge step to the end (using position + 1 from the last step)
            $this->insert('pathway_type_step', [
                        'pathway_type_id' => $pathway_type_id,
                        'step_type_id' => 3,
                        'order' => $step['position'] + 1,
                        'short_name' => 'Discharge',
                        'long_name' => 'Check out',
                        'default_state_data' => '{
                            "firm_id":"' . $rule['firm_id'] . '"
                        }']);
        }
    }

    public function safeDown()
    {
        echo "Down not supported";
    }
}
