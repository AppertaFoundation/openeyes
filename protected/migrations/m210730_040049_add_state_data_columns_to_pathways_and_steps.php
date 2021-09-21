<?php

class m210730_040049_add_state_data_columns_to_pathways_and_steps extends OEMigration
{
    /**
     * @throws CException
     * @throws JsonException
     */
    public function safeUp()
    {
        $this->addOEColumn('pathway', 'state_data', 'JSON', true);
        $this->addOEColumn('pathway_step', 'state_data', 'JSON', true);
        $this->addOEColumn('pathway_type_step', 'default_state_data', 'JSON', true);
        $this->addOEColumn('pathway_step_type', 'state_data_template', 'JSON', true);
        $this->addOEColumn('event', 'step_id', 'int', true);
        $this->addForeignKey(
            'event_step_fk',
            'event',
            'step_id',
            'pathway_step',
            'id',
            'SET NULL' // Don't want to delete the event, so instead just sever the link to the step.
        );

        // Add state data templates to all existing applicable pathway step types and existing requested steps.
        foreach (array(
            'Exam' => 'OphCiExamination',
            'Bio' => 'OphInBiometry',
            'Rx' => 'OphDrPrescription',
            'Letter' => 'OphCoCorrespondence',
         ) as $type => $event_type_class) {
            $event_step_type = $this->dbConnection->createCommand()
                ->select('id')
                ->from('pathway_step_type')
                ->where('short_name = :name', [':name' => $type])
                ->queryScalar();
            $new_event_action = (new NewEventState())->toJSON();
            $new_event_action['event_type'] = $event_type_class;
            $new_event_json = json_encode($new_event_action, JSON_THROW_ON_ERROR);
            $this->update(
                'pathway_step_type',
                ['state_data_template' => $new_event_json],
                'id = :id',
                [':id' => $event_step_type]
            );
        }

        $psd_step_type = $this->dbConnection->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = "drug admin"')
            ->queryScalar();
        $psd_json = json_encode((new PSDState())->toJSON(), JSON_THROW_ON_ERROR);

        $this->update(
            'pathway_step_type',
            ['state_data_template' => $psd_json],
            'id = :id',
            [':id' => $psd_step_type]
        );
    }

    public function down()
    {
        $this->dropForeignKey('event_step_fk', 'event');
        $this->dropOEColumn('event', 'step_id', true);
        $this->dropOEColumn('pathway', 'state_data', true);
        $this->dropOEColumn('pathway_step', 'state_data', true);
        $this->dropOEColumn('pathway_type_step', 'default_state_data', true);
        $this->dropOEColumn('pathway_step_type', 'state_data_template', true);
    }
}
