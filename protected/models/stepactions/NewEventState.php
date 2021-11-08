<?php

class NewEventState extends StepStateType
{
    protected string $action_id = 'new_event';
    protected string $event_label = 'Manage Event';
    public function toJSON(): array
    {
        return array_merge(
            parent::toJSON(),
            array(
                'event_type' => null,
                'default_element_list' => array(),
                'workflow_id' => null
            )
        );
    }

    public function getOptions(): array
    {
        $event_types = EventType::model()->getActiveList();
        $event_json = array_map(
            static function ($event_type) {
                return array('id' => $event_type->class_name, 'label' => $event_type->name);
            },
            $event_types
        );
        return [
            'event_type_list' => $event_json
        ];
    }
}
