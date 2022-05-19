<?php

class m220407_020756_add_checkin_step_type extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->insert(
            'pathway_step_type',
            array(
                'default_state' => PathwayStep::STEP_REQUESTED,
                'group' => 'path',
                'small_icon' => 'direction-right',
                'large_icon' => 'i-arr',
                'widget_view' => 'checkin',
                'type' => 'process',
                'user_can_create' => 1,
                'short_name' => 'checkin',
                'long_name' => 'Check in',
                'active' => 1,
            )
        );

        $step_type_id = $this->dbConnection->getLastInsertID();

        // Assume all currently instanced pathways will always start with a check-in step.
        $select = <<<EOSQL
SELECT
    id as pathway_id,
    {$step_type_id} AS step_type_id,
    'checkin' AS short_name,
    'Check in' AS long_name,
    0 AS `order`,
    start_time,
    start_time AS end_time,
    IF(start_time IS NOT NULL, 2, 0) AS status
FROM pathway
EOSQL;

        $step_instances = $this->dbConnection->createCommand($select)->queryAll();

        // It is possible in some instances for this list to be empty (eg. freshly restored demo data), so in that instance do not create any check-in steps.
        if (!empty($step_instances)) {
            $this->insertMultiple(
                'pathway_step',
                $step_instances
            );
        }

        // All pathway types assigned to a worklist definition will have a check-in step as the first step.
        $select = <<<EOSQL
SELECT
    pt.id as pathway_type_id,
    {$step_type_id} AS step_type_id,
    'checkin' AS short_name,
    'Check in' AS long_name,
    0 AS `order`,
    0 AS status
FROM worklist_definition wd
JOIN pathway_type pt ON pt.id = wd.pathway_type_id
GROUP BY pt.id
EOSQL;

        $type_step_instances = $this->dbConnection->createCommand($select)->queryAll();

        // It is possible in some instances for this list to be empty (eg. freshly restored demo data), so in that instance do not create any check-in steps.
        if (!empty($type_step_instances)) {
            $this->insertMultiple(
                'pathway_type_step',
                $type_step_instances
            );
        }
    }

    /**
     * @return bool|void
     * @throws CException
     */
    public function safeDown()
    {
        $id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'checkin\'')
            ->queryScalar();

        $this->delete(
            'pathway_step',
            'step_type_id = :id',
            [':id' => $id]
        );

        $this->delete(
            'pathway_type_step',
            'step_type_id = :id',
            [':id' => $id]
        );

        $this->delete(
            'pathway_step_type',
            'id = :id',
            [':id' => $id]
        );
    }
}
